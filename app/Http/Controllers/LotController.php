<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Services\LotService;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function getActive()
    {
        return LotService::getActive();
    }

    public function getById($id)
    {
        return LotService::getById($id);
    }

    public function joinLot(Request $request, $id)
    {
        return LotService::joinLot($request, $id);
    }
}
