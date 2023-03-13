<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Response;

class DashboardController extends BasicAdminController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        dump(Session::get('api-token'));
        return $this->view(template: 'Dashboard', request: $request);
    }
}