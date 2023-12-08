<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Server error response
     *
     * @param Exception $e
     * @return JsonResponse
     */
    protected function serverError(Exception $e): JsonResponse
    {
        Log::error($e->getMessage(), $e->getTrace());
        return response()->json(['errors' => [$e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
