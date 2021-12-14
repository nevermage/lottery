<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function view($profileId)
    {
        return View('profile', ['id' => $profileId]);
    }
}
