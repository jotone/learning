<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Inertia\Response;

class DashboardController extends BasicAdminController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->view('Dashboard');
    }
}