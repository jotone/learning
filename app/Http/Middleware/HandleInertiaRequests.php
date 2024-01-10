<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define template file depends on the current route
     *
     * @param Request $request
     * @return string
     */
    public function rootView(Request $request): string
    {
        return 'dashboard' === $request->route()->getPrefix() ? 'dashboard' : 'main';
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => auth()->check()
                ? array_merge(
                    ['apiToken' => Session::get('api-token')],
                    User::select(['id', 'first_name', 'last_name', 'email', 'role_id'])
                        ->with([
                            'role' => fn($q) => $q->select('id', 'name', 'level')
                        ])
                        ->firstWhere('id', $request->user()->id)
                        ->toArray()
                )
                : null,
        ]);
    }
}
