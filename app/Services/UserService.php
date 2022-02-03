<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{

    public static function getUser(int $id)
    {
        return User::get()->where('id', $id)
            ->makeHidden(['password', 'api_token', 'updated_at'])
            ->first();
    }

    public static function getAll()
    {
        return User::get()
            ->makeHidden(['password', 'api_token', 'updated_at']);
    }

    public static function winners()
    {
        return DB::table('lots')
            ->select(
                'users.id', 'users.name',
                'lots.name AS lot', 'lots.id AS lid',
                'lots.image_path as lotImage',
                'users.image_path as userImage'
            )
            ->join('users', 'lots.winner_id', '=', 'users.id')
            ->whereNotNull('winner_id')
            ->get();
    }
}
