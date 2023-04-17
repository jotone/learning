<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
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
        return 'dashboard' === $request->route()->getPrefix() ? 'dashboard.app' : 'main.app';
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'role' => $request->user()?->role?->slug,
                'user' => $request->user()
            ],
            /*'ziggy' => function () use ($request) {
                $ziggy_share = (new Ziggy)->toArray();
                unset($ziggy_share['routes']);
                return array_merge($ziggy_share, [
                    'location' => $request->url(),
                ]);
            },*/
        ]);
    }
}
