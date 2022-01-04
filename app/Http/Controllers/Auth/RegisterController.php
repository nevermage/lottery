<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthenticateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    public function register(Request $request)
    {
        $response = AuthenticateService::register($request);
        if ($response) {
            return response()->json($response,Response::HTTP_UNAUTHORIZED);
        }
        return response()->json(['data' => 'Confirm your email!'],Response::HTTP_OK);
    }

    public function verify(Request $request)
    {
        $response = AuthenticateService::verifyEmail($request);
        if (is_object($response)) {
            return $response;
        }
        return response()->json($response,Response::HTTP_UNAUTHORIZED);
    }

}
