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

    public function passwordResetRequestMail(Request $request): JsonResponse
    {
        $response = AuthenticateService::passwordResetRequestMail($request);
        if (array_key_exists('error', $response)) {
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }
        return response()->json(['data' => 'Check your email'], Response::HTTP_OK);
    }

    public function passwordReset(Request $request): JsonResponse
    {
        $response = AuthenticateService::passwordReset($request);
        if (!array_key_exists('set', $response)) {
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }
        return response()->json(['data' => 'Password updated!'], Response::HTTP_OK);
    }

    public function googleLogin(Request $request): JsonResponse
    {
        $response = AuthenticateService::googleLogin($request);
        if (array_key_exists('token', $response)) {
            return response()->json($response['token'], Response::HTTP_OK);
        }
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

}
