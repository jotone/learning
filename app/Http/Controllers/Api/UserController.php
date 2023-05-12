<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Http\Requests\User\{UserStoreRequest, UserUpdateRequest};
use App\Http\Resources\UserResource;
use App\Jobs\SendRegistrationEmail;
use App\Models\{Role, User, UserInfo};
use Illuminate\Http\{JsonResponse, Request, UploadedFile};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\{Auth, DB};

class UserController extends BasicApiController
{
    /**
     * User list
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiList(
            request: $request,
            collection: User::select(['users.*', 'roles.name as role_name'])
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id'),
            total: User::count(),
            resource: UserResource::class,
            search_callback: function ($q, $search) {
                $search = mb_strtolower($search);
                return $q->where(
                    fn($query) => $query
                        ->whereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE '%$search%'")
                        ->orWhereRaw("CONCAT(`last_name`, ' ', `first_name`) LIKE '%$search%'")
                        ->orWhere('email', 'like', "%$search%")
                );
            }
        );
    }

    /**
     * Specified user data
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        return $this->apiShow(
            request: $request,
            query: User::select(['users.*', 'roles.name as role_name'])
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id'),
            id: $id
        );
    }

    /**
     * Create User
     *
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        // Get request data
        $args = $request->validated();


        DB::beginTransaction();
        try {
            // Create user entity
            $user = User::create($this->buildUserData($args));
            // User Info entity
            UserInfo::create($this->buildUserInfo($user, $args));
            // Save user's image
            $user->img_url = $this->saveImage($user->id, $args);

            $user->save();

            SendRegistrationEmail::dispatch($user);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTrace());
        }

        return response()->json($user, 201);
    }

    /**
     * Update user
     *
     * @param User $user
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        // Check the user's role gives allowance to update another user
        if (Auth::id() !== $user->id && Auth::user()->role->level > $user->role->level) {
            return response()->json([], 403);
        }
        $args = $request->validated();
        // UserInfo Entity
        $info = $user->info;

        foreach ($args as $key => $val) {
            if (in_array($key, ['about', 'email', 'first_name', 'last_name', 'role_id', 'status'])) {
                // Update user entity fields
                $user->$key = $val;
            } elseif (in_array($key, [
                'address',
                'city',
                'country',
                'ext_addr',
                'phone',
                'shirt_size',
                'region',
                'timezone',
                'zip'
            ])) {
                // Update user info fields
                $info->$key = $val;
            } elseif ($key === 'img_url') {
                // Store user image
                $user->$key = $this->saveImage($user->id, $args);
            } elseif ($key === 'password' && !empty($val)) {
                $user->$key = $val;
            }
        }

        // Update user's entity
        if ($user->isDirty()) {
            $user->save();
        }
        // Update user info
        if (!empty($info) && $info->isDirty()) {
            $info->save();
        }

        return response()->json(User::with('info')->find($user->id));
    }

    /**
     * Remove User
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        // User cannot remove himself. Check user is allowed to remove another
        if (Auth::id() === $user->id || Auth::user()->role->level > $user->role->level) {
            return response()->json([], 403);
        }
        // Remove user
        $user->delete();

        return response()->json([], 204);
    }

    /**
     * User data array
     *
     * @param array $args
     * @return array
     */
    protected function buildUserData(array $args): array
    {
        // User statuses list
        $statuses = array_flip(config('enums.user.statuses'));
        // User data array
        $data = [
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'email' => $args['email'],
            'about' => $args['about'] ?? null,
            'role_id' => $args['role_id'] ?? Role::where('slug', 'student')->value('id')
        ];
        // Set user password
        if (isset($args['password'])) {
            $data['password'] = $args['password'];
            // Set status "active"
            $data['status'] = $statuses['active'];
        }
        // Set user status
        if (isset($args['status'])) {
            $data['status'] = $args['status'];
        } else if (!isset($data['status'])) {
            $data['status'] = $statuses['missing-details'];
        }
        // If user is already activated -> set his activation date
        if ($data['status'] === $statuses['active']) {
            $data['activated_at'] = now();
        }

        return $data;
    }

    /**
     * User info array
     *
     * @param User $user
     * @param array $args
     * @return array
     */
    protected function buildUserInfo(User $user, array $args): array
    {
        return [
            'user_id' => $user->id,
            'timezone' => $args['timezone'] ?? null,
            'country' => $args['country'] ?? null,
            'region' => $args['region'] ?? null,
            'city' => $args['city'] ?? null,
            'address' => $args['address'] ?? null,
            'ext_addr' => $args['ext_addr'] ?? null,
            'zip' => $args['zip'] ?? null,
            'phone' => $args['phone'] ?? null,
            'shirt_size' => $args['shirt_size'] ?? null
        ];
    }

    /**
     * Set avatar image to user's entity
     *
     * @param int $id
     * @param array $args
     * @return string
     */
    protected function saveImage(int $id, array $args): string
    {
        return !empty($args['img_url']) && $args['img_url'] instanceof UploadedFile
            ? FileHelper::saveFile($args['img_url'], 'images/users/' . $id)
            : ($args['img_url'] ?? '');
    }
}