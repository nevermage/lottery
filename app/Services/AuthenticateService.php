<?php

namespace App\Services;

use App\Mail\PasswordReset;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Verification;
use Illuminate\Support\Carbon;
use Google_Client;
use Laravel\Socialite\Facades\Socialite;

class AuthenticateService
{
    const AdminRoleId = 2;

    public static function login(Request $request): array
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        return self::loginAttempt($request);
    }


    private static function loginAttempt(Request $request): array
    {
        $user = User::get()->where('email', $request->email)->first();
        if ($user === null) {
            return ['data' => 'This email dont match our records'];
        }
        if (Hash::check($request->password, $user['password'])) {
            return self::generateToken($request->email, $user['password']);
        }
        return ['data' => 'Password is incorrect'];
    }

    private static function registerValidation(Request $request): array
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|min:3',
            'password' => 'required|min:8|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required|min:8',
        ]);

        $user = User::get()->where('email', $request['email'])->first();
        if ($user != null) {
            return ['data' => 'This email is already taken'];
        }
        return ['registered' => true];
    }

    public static function register(Request $request): array
    {
        $registerCheck = self::registerValidation($request);
        if (!array_key_exists('registered', $registerCheck)) {
            return $registerCheck;
        }

        $hash = Hash::make($request['email']);

        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'api_token' => $hash
        ]);

        Mail::to($request['email'])->send(new Verification($hash));

        return $registerCheck;
    }

    public static function verifyEmail(Request $request): array
    {
        $user = User::get()->where('api_token', $request['hash'])->first();
        if ($user === null) {
            return ['data' => 'No users with this token'];
        }
        if ($user['email_verified_at'] != null) {
            return ['data' => 'User is already verified'];
        }
        $user->email_verified_at = Carbon::now();
        $user->save();

        return ['confirmed' => true];
    }

    public static function getUserId(Request $request): ?int
    {
        $user = self::checkUser($request);

        if (!array_key_exists('data', $user)) {
            return $user['id'];
        }
        return null;
    }

    public static function getUserRole(Request $request): ?int
    {
        $user = self::checkUser($request);

        if (!array_key_exists('data', $user)) {
            return $user['role_id'];
        }
        return null;
    }

    public static function checkUser(Request $request): array
    {
        $token = $request->bearerToken();

        if ($token === null) {
            return ['data' => 'UnAuthenticated'];
        }
        return self::loginViaToken($token);
    }

    private static function loginViaToken(string $token): array
    {
        $credentials = JWT::decode($token, "secret", array('HS256'));
        $user = User::get()->where('email', $credentials->email)
            ->where('password', $credentials->password)
            ->first()
            ->toArray();

        if ($user === null) {
            return ['data' => 'UnAuthenticated'];
        }
        if ($user['password'] === $credentials->password) {
            unset($user['password']);
            unset($user['api_token']);

            return $user;
        }
        return ['data' => 'UnAuthenticated'];
    }

    public static function passwordResetRequestMail(Request $request): array
    {
        $user = User::get()->where('email', '=', $request->email);
        if ($user->count() === 0) {
            return ['error' => 'No users with this email'];
        }
        if ($user->first()->email_verified_at === null) {
            return ['error' => 'Verify your email first!'];
        }
        $token = $user->first()->api_token;

        Mail::to($request->email)->send(new PasswordReset($token));

        return ['sent' => true];
    }

    public static function passwordReset(Request $request): array
    {
        $user = User::get()->where('api_token', '=', $request->token);
        if ($user->count() === 0) {
            return ['error' => 'No users with this token'];
        }
        $request->validate(['password' => 'required|min:8']);

        if (Hash::check($request->password, $user->first()->password)) {
            return ['error' => 'Your password is the same as old one'];
        }
        User::findOrFail($user->first()->id)
            ->update(['password' => Hash::make($request->password)]);

        return ['set' => true];
    }

    public static function googleLogin(Request $request): array
    {
        $client = new Google_Client();
        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return ['error' => 'invalid token'];
        }

        return self::identifyViaSocial($payload['email'], $payload['given_name'], $payload['picture'] ?? null);
    }

    public static function facebookLogin(Request $request): array
    {
        $driver= Socialite::driver('facebook');
        $payload = $driver->userFromToken($request->token);
        if (!$payload) {
            return ['error' => 'invalid token'];
        }
        $email = $payload->id . '@facebook';

        return self::identifyViaSocial($email, $payload->name, $payload->avatar ?? null);
    }

    public static function identifyViaSocial(string $email, string $name, $image): array
    {
        $user = User::where('email', '=', $email)->first();
        if ($user === null) {
            $password = Hash::make(str_random(48));
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'email_verified_at' => Carbon::now(),
                'image_path' => $image
            ]);
            return self::generateToken($email, $password);
        }
        return self::generateToken($user->email, $user->password);
    }

    public static function generateToken(string $email, string $password): array
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];
        return ['token' => JWT::encode($credentials, env('JWT_CODING_PASSWORD'))];
    }
}


