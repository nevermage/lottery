<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Verification;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateService
{

    public static function login($request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        return AuthenticateService::LoginAttempt($request);
    }


    public static function loginAttempt($request)
    {
        $user = User::get()->where('email', $request['email'])->first();
        if ($user == null) {
            return ['data' => 'This email dont match our records'];
        }
        if ($user['password'] == $request['password']) {
            $jwt = JWT::encode($request->all(), "secret");

            setcookie("token", $jwt, time()+30*24*60*60);

            return $jwt;
        }
        return ['data' => 'Password is incorrect'];
    }

    public static function registerValidation($request)
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
        return true;
    }

    public static function register($request)
    {
        $registerCheck = AuthenticateService::registerValidation($request);
        if ($registerCheck !== true) {
            return $registerCheck;
        }

        $hash = Hash::make($request['email']);

        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'api_token' => $hash
        ]);

        Mail::to($request['email'])->send(new Verification($hash));

        return null;
    }

    public static function verifyEmail($request)
    {
        $user = User::get()->where('api_token', $request['hash'])->first();
        if ($user == null) {
            return ['data' => 'No users with this token'];
        }
        if ($user['email_verified_at'] != null) {
            return ['data' => 'User is already verified'];
        }
        $user->email_verified_at = Carbon::now();
        $user->save();

        return redirect('/');
    }

    public static function logout()
    {
        setcookie("token", null, 0);
        return response()->json(
            ['data' => 'User logged out'],
            Response::HTTP_OK
        );
    }

    public static function getUserId($request)
    {
        $user = self::checkUser($request, 'array');

        if (is_object($user)) {
            return $user['id'];
        }
        return null;
    }

    public static function getUserRole($request)
    {
        $user = self::checkUser($request, 'array');

        if (is_object($user)) {
            return $user['role_id'];
        }
        return null;
    }

    public static function checkUser($request, $returnType='object')
    {
        $token = $request->cookie('token');
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6IlRvbWFzLnp4Y0BnbWFpbC5jb20iLCJwYXNzd29yZCI6InNlY3JldDEyMyJ9.nI09WGAueoUGNIWZB7HMUwIkQGZmXirijyM-UiuD7nc";
        if ($token == null) {
            if ($returnType == 'array') {
                return null;
            }

            return response()->json(
                ['data' => 'UnAuthenticated'],
                Response::HTTP_UNAUTHORIZED
            );
        }
        return self::loginViaToken($token, $returnType ?: 'object');
    }

    public static function loginViaToken($token, $returnType='object')
    {
        $credentials = JWT::decode($token, "secret", array('HS256'));
        $user = User::get()->where('email', $credentials->email)
            ->where('password', $credentials->password)
            ->first();
        if ($user == null) {
            return response()->json(
                ['data' => 'UnAuthenticated'],
                Response::HTTP_UNAUTHORIZED
            );
        }
        if ($user['password'] == $credentials->password) {
            unset($user['password']);
            unset($user['api_token']);
            setcookie("token", $token, time()+30*24*60*60);
            if ($returnType == 'object') {
                return response()->json(
                    ['data' => $user],
                    Response::HTTP_OK
                );
            }
            if ($returnType == 'array') {
                return $user;
            }
        }
        return response()->json(
            ['data' => 'UnAuthenticated'],
            Response::HTTP_UNAUTHORIZED
        );
    }

}


