<?php

namespace App\Http\Middleware;

use App\Traits\PermissionListTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class DashboardPermissions
{
    use PermissionListTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check testing middleware
        if (config('app.env') === 'testing') {
            return $next($request);
        }
        // Get request token or session token value
        $token = Session::get('api-token') ?? $request->bearerToken() ?? null;
        // Request token entity
        $token_entity = PersonalAccessToken::findToken($token);
        abort_if(!$token_entity, 403);
        // Get user from token entity
        $user = $token_entity->tokenable_type::with('role')->find($token_entity->tokenable_id);

        abort_if(!$user, 403);
        // Prepare user permissions
        $permissions = $this->userPermissions($user->role->permissions);

        // Check if user permission exists for current request action
        if (
            isset($permissions[$request->route()->getControllerClass()])
            && in_array($request->route()->getActionMethod(), $permissions[$request->route()->getControllerClass()])
        ) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Forbidden'
        ], 403);
    }
}