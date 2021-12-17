<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
