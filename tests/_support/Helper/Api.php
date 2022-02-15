<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Api extends \Codeception\Module
{
    public function createUser(string $name, string $email, string $password): void
    {
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'api_token' => Hash::make($password)
        ]);
    }
}
