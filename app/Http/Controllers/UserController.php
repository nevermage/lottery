<?php

namespace App\Http\Controllers;

//Use User Model
use App\Models\User;

//Use Resources to convert into json
use App\Http\Resources\UserResource as UserResource;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::get();
        return UserResource::collection($users);
    }

    public function getUser($id)
    {
        $user = User::get()->where('id', $id);
        return UserResource::collection($user);
    }
}
