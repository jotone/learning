<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\User;
use Inertia\Response;

class DashboardController extends BaseDashboardController
{
    /**
     * Index page method.
     * This method returns a Response object representing the index page view.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->view('Dashboard/Index');
    }
}