<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lot;
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

    public static function winners()
    {
        $winners = DB::table('lots')
            ->select('users.id', 'users.name', 'lots.name AS lot', 'lots.id AS lid')
            ->join('users', 'lots.winner_id', '=', 'users.id')
            ->whereNotNull('winner_id')
            ->get();
        return $winners;
    }
}
