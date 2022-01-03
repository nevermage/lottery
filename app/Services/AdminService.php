<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\User;

class AdminService
{

    public static function getLots()
    {
        return Lot::get();
    }

    public static function updateLot($request, $id)
    {
        $lot = Lot::findOrFail($id);
        $lot->update($request->all());
        return ['data' => 'lot was updated'];
    }

    public static function deleteLot($id)
    {
        $lot = Lot::findOrFail($id);
        $lot->delete();
        return ['data' => 'lot was deleted'];
    }

    public static function getUsers()
    {
        return User::get();
    }

    public static function updateUser($request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return ['data' => 'User was updated'];
    }

    public static function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return ['data' => 'User was deleted'];
    }

}
