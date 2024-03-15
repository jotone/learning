<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            try {
                // Get request token or session token value
                $token = Session::get('api-token') ?? $request->bearerToken() ?? null;
                // Request token entity
                $token_entity = PersonalAccessToken::findToken($token);
                // Get user from token entity
                $user = $token_entity->tokenable_type::find($token_entity->tokenable_id);
            } catch (\Exception) {
                abort(403);
            }
        }
        // Retrieve the controller class from the current route.
        $controller = $request->route()->getControllerClass();
        // Check if the controller belongs to Dashboard or API namespaces.
        if (
            str_starts_with($controller, 'App\Http\Controllers\Dashboard')
            || str_starts_with($controller, 'App\Http\Controllers\Api')
        ) {
            // Determine if the controller is an API controller based on the namespace.
            $is_api = str_starts_with($controller, 'App\Http\Controllers\Api');
            // Extract the schema class and method from the controller action.
            [$schema_class, $method] = explode('@', $request->route()->getAction()['controller']);
        } else {
            // Mark this as an API call for further checks.
            $is_api = true;
            // For GraphQL requests, derive the schema name from the route action alias.
            $schema_name = str_replace(config('graphql.route.prefix') . '.', '', $request->route()->getAction()['as']);
            // Block the request if it doesn't contain 'query' or 'operations' parameters.
            if (!$request->has('query') && !$request->has('operations')) {
                return response()->json('Forbidden operation', 403);
            }
            // Initialize the method variable
            $method = '';
            // Normalize the query/operations string.
            if ($request->has('operations')) {
                $operations = $request->get('operations');
                $query = is_array($operations) ? $operations['query'] : $operations;
            } else {
                $query = $request->get('query');
            }
            $query = preg_replace('/(\s+|\n)/', '', $query);

            // Determine if the query is a read operation based on its structure.
            if (str_starts_with($query, '{' . Str::plural($schema_name)) || str_starts_with($query, '{__typename' . Str::plural($schema_name))) {
                $method = 'query';
            } else {
                // For mutations, clean up the query to isolate the operation name.
                $query = mb_strtolower(preg_replace('/(query|[^a-zA-Z\s]+)/', '', substr($query, 0, strpos($query, '('))));
                if (str_starts_with($query, 'mutation')) {
                    if ($request->has('operations')) {
                        $query = str_replace($schema_name, '', $query);
                    }
                    // Extract the method name from the cleaned query.
                    $method = substr(trim(preg_replace('/\s+/', ' ', $query)), 8);
                }
            }
            // If no valid method was determined, block the request.
            if (empty($method)) {
                return response()->json('Forbidden operation', 403);
            }
            // Construct the fully qualified schema class name.
            $schema_class = 'App\GraphQL\Schemas\\' . ucfirst($schema_name) . 'Schema';
        }
        // Retrieve permissions for the current user role and controller.
        $permissions = $user->role->permissions()->where('controller', $schema_class)->first();
        // Check if the determined method is allowed for the user.
        if (empty($permissions) || !in_array($method, $permissions?->allowed_methods ?? [])) {
            // Return a forbidden response for API calls, or abort for others.
            if ($is_api) {
                return response()->json('Forbidden operation', 403);
            } else {
                abort(403);
            }
        }
        // Proceed with the next middleware in the stack.
        return $next($request);
    }
}
