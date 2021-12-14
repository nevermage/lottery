<?php

namespace App\Http\Controllers;

class LotController extends Controller
{
    public function view($lotId = 404)
    {
        return View('lot', ['id' => $lotId]);
    }
}
