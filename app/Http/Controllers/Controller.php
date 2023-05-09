<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Default paginator items per page
     *
     * @var int
     */
    protected int $take = 30;

    /**
     * Model default order query parameters
     *
     * @var array
     */
    protected array $order = [
        'by' => ['id'],
        'dir' => 'desc'
    ];

    use AuthorizesRequests, ValidatesRequests;
}
