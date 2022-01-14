<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\User;
use Illuminate\Http\Request;

class AdminService
{

    public static function getLots(): array
    {
        return Lot::get()->toArray();
    }

    public static function updateLot(Request $request, int $id): array
    {
        $lot = Lot::findOrFail($id);
        $lot->update($request->all());
        return ['data' => 'lot was updated'];
    }

    public static function deleteLot(int $id): array
    {
        $lot = Lot::findOrFail($id);
        $lot->delete();
        return ['data' => 'lot was deleted'];
    }

    public static function getUsers()
    {
        return User::get();
    }

    public static function updateUser(Request $request, int $id): array
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return ['data' => 'User was updated'];
    }

    public static function deleteUser(int $id): array
    {
        $user = User::findOrFail($id);
        $user->delete();
        return ['data' => 'User was deleted'];
    }

}
