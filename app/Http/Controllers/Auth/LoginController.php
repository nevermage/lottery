<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthenticateService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        $response = AuthenticateService::login($request);

        if (array_key_exists('token', $response)) {
            return response()->json($response['token'], Response::HTTP_OK);
        }
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

    public function checkUser(Request $request): JsonResponse
    {
        $response = AuthenticateService::checkuser($request);
        if (!array_key_exists('data', $response)) {
            return response()->json($response, Response::HTTP_OK);
        }
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

}
