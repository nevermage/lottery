<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Verification;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

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
            return response()->json(
                ['data' => 'This email dont match our records'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }
        if ($user['password'] == $request['password']) {
            $jwt = JWT::encode($request->all(), "secret");

            setcookie("token", $jwt, time()+30*24*60*60);

            return response()->json(['data' => $jwt], HttpResponse::HTTP_OK);
        }
        return response()->json(['data' => 'Password is incorrect'], HttpResponse::HTTP_UNAUTHORIZED);
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
            return response()->json(
                ['data' => 'This email is already taken'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
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

        return response()->json(['data' => 'Confirm your email!'], HttpResponse::HTTP_OK);
    }

    public static function verifyEmail($request)
    {
        $user = User::get()->where('api_token', $request['hash'])->first();
        if ($user == null) {
            return response()->json(
                ['data' => 'No users with this token'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }
        if ($user['email_verified_at'] != null) {
            return response()->json(
                ['data' => 'User is already verified'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
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
            HttpResponse::HTTP_OK
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

    public static function checkUser($request, $returnType='array')
    {
        $token = $request->cookie('token');
        if ($token == null) {
            return response()->json(
                ['data' => 'UnAuthenticated'],
                HttpResponse::HTTP_UNAUTHORIZED
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
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }
        if ($user['password'] == $credentials->password) {
            unset($user['password']);
            unset($user['api_token']);
            setcookie("token", $token, time()+30*24*60*60);
            if ($returnType == 'object') {
                return response()->json(
                    ['data' => $user],
                    HttpResponse::HTTP_OK
                );
            }
            if ($returnType == 'array') {
                return $user;
            }
        }
        return response()->json(
            ['data' => 'UnAuthenticated'],
            HttpResponse::HTTP_UNAUTHORIZED
        );
    }

}


