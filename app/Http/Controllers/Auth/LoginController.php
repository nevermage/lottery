<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LotService;
use Illuminate\Http\Request;
use App\Services\AuthenticateService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function login(Request $request)
    {
        return AuthenticateService::login($request);
    }

    public function checkUser(Request $request)
    {
        return AuthenticateService::checkuser($request);
    }

    public function logout()
    {
        return AuthenticateService::logout();
    }

}
