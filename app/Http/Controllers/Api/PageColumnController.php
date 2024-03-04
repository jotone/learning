<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\PageColumn\SortRequest;
use App\Http\Requests\PageColumn\UpdateRequest;
use App\Models\PageColumn;
use Illuminate\Http\JsonResponse;

class PageColumnController extends BaseApiController
{
    /**
     * Update the specified page column entity
     *
     * @param PageColumn $column
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(PageColumn $column, UpdateRequest $request): JsonResponse
    {
        return $this->simpleUpdateRequest($column, $request);
    }

    /**
     * Update the page column positions
     *
     * @param SortRequest $request
     * @return JsonResponse
     */
    public function sort(SortRequest $request): JsonResponse
    {
        return $this->simpleSortRequest(PageColumn::class, $request, 'table_position');
    }
}