<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class UserController extends Controller
{
    public function getById($id)
    {
        return UserService::getUser($id);
    }

    public function getAll()
    {
        return UserService::getAll();
    }
}
