<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;


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

    public function winners()
    {
        $winners = UserService::winners();
        return response()->json($winners, Response::HTTP_OK);
    }
}
