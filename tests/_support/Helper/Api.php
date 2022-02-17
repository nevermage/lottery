<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Models\Lot;
use App\Models\User;
use App\Services\AuthenticateService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Api extends \Codeception\Module
{
    public function createUser(
        string $name, string $email,
        string $password, string $verifiedDate = null,
    ): int
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'api_token' => Hash::make($email),
            'email_verified_at' => $verifiedDate
        ])->id;
    }

    public function createAdmin(string $email): void
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => $email,
            'password' => Hash::make('password'),
            'api_token' => Hash::make($email),
            'role_id' => 2
        ]);
    }

    public function deleteUser(string $email): void
    {
        $id = User::get()->where('email', '=', $email)->first()->id;
        User::findOrFail($id)->delete();
    }

    public function getToken(string $email, string $password): string
    {
        return AuthenticateService::loginAttempt($email, $password)['token'];
    }

    public function getApiToken(string $email): string
    {
        return User::get()->where('email', '=', $email)->first()->api_token;
    }

    public function deleteLotsByCreatorEmail(string $email): void
    {
        $lotId = DB::table('lots')
            ->join('users', 'lots.creator_id', '=', 'users.id')
            ->where('users.email', '=', $email)
            ->get('lots.id')
            ->first();

        Lot::findOrFail($lotId->id)->delete();
    }

    public function createLot(string $name, int $creatorId,  string $status): int
    {
        return Lot::create([
            'name' => $name,
            'creator_id' => $creatorId,
            'status' => $status
        ])->id;
    }

    public function deleteLot(int $id): void
    {
        Lot::findOrFail($id)->delete();
    }

    public function createLotWonBy(int $id): int
    {
        return Lot::create([
            'name' => 'lot name',
            'creator_id' => 0,
            'status' => 'expired',
            'winner_id' => $id
        ])->id;
    }
}

