<?php

namespace App\Http\Controllers;

use App\Models\LotUser;
use App\Http\Resources\LotUserResource;

use Dotenv\Validator;
use Illuminate\Http\Request;

class LotUserController extends Controller
{
    public function getLotUsers($id)
    {
        $allLotUsers = LotUser::get()
            ->where('lot_id', $id);
        return LotUserResource::collection($allLotUsers);
    }
}
