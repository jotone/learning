<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Inertia\Response;

class UsersController extends BasicAdminController
{
    /**
    * @return Response
    */
    public function index(): Response
    {
        return $this->view('Users/Index');
    }
}