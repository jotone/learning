<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends BasicApiController
{
    /**
     * Role list
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiList(
            request: $request,
            collection: Course::query(),
            total: Course::count(),
            resource: CourseResource::class,
            search_callback: function ($q, string $search) {
                $search = mb_strtolower($search);
                return $q->whereRaw("LOWER(name) LIKE '%$search%' OR LOWER(url) LIKE '%$search%'");
            });
    }
}