<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{

    public static function getUser($id)
    {
        $user = User::get()->where('id', $id)
            ->makeHidden(['password', 'api_token', 'updated_at' ])
            ->first();
        return response()->json($user, HttpResponse::HTTP_OK);
    }

    public static function getAll()
    {
        $users = User::get()
            ->makeHidden(['password', 'api_token', 'updated_at' ]);
        return response()->json($users, HttpResponse::HTTP_OK);
    }
}
