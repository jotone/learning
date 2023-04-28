<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendRegistrationEmail;
use App\Models\{Role, User, UserInfo};
use Illuminate\Http\{JsonResponse, Request, UploadedFile};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

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
            if (!empty($args['img_url']) && $args['img_url'] instanceof UploadedFile) {
                $user->img_url = FileHelper::saveFile($args['img_url'], 'images/users/' . $user->id);
                $user->save();
            }

            SendRegistrationEmail::dispatch($user);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTrace());
        }

        return response()->json($user, 201);
    }

    public function update()
    {

    }

    /**
     * Remove User
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
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
            'last_name'  => $args['last_name'],
            'email'      => $args['email'],
            'about'      => $args['about'] ?? null,
            'role_id'    => $args['role_id'] ?? Role::where('slug', 'student')->value('id')
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
            'user_id'          => $user->id,
            'timezone'         => $args['timezone'] ?? null,
            'country'          => $args['country'] ?? null,
            'state_region'     => $args['state_region'] ?? null,
            'city'             => $args['city'] ?? null,
            'address'          => $args['address'] ?? null,
            'extended_address' => $args['extended_address'] ?? null,
            'zip'              => $args['zip'] ?? null,
            'phone'            => $args['phone'] ?? null,
            'shirt_size'       => $args['shirt_size'] ?? null
        ];
    }
}