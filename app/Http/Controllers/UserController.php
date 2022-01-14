<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function getById(int $id): JsonResponse
    {
        $user = UserService::getUser($id);
        return response()->json($user, Response::HTTP_OK);
    }

    public function getAll(): JsonResponse
    {
        $users = UserService::getAll();
        return response()->json($users, Response::HTTP_OK);

    }

    public function winners(): JsonResponse
    {
        $winners = UserService::winners();
        return response()->json($winners, Response::HTTP_OK);
    }
}
