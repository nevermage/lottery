<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthenticateService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $response = AuthenticateService::register($request);
        if (array_key_exists('registered', $response)) {
            return response()->json(['data' => 'Confirm your email!'],Response::HTTP_OK);
        }
        return response()->json($response,Response::HTTP_UNAUTHORIZED);
    }

    public function verify(Request $request): JsonResponse
    {
        $response = AuthenticateService::verifyEmail($request);
        if (array_key_exists('confirmed', $response)) {
            return response()->json(['data' => 'Email confirmed'],Response::HTTP_OK);
        }
        return response()->json($response,Response::HTTP_UNAUTHORIZED);
    }

}
