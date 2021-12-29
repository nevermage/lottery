<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\AuthenticateService;
use App\Models\LotUser;

class LotService
{

    public static function getById($id)
    {
        $lot = DB::select("
            select
                lots.id, lots.name ,
                (select name from users where id = creator_id) as creator,
                (select name from users where id = winner_id) as winner,
                lots.image_path, description,
                roll_time, winner_id
            from lots
            where lots.id = $id
        ;");

        return response()->json($lot, HttpResponse::HTTP_OK);
    }

    public static function getActive()
    {
        $lots = DB::select('
            select
                lots.id, lots.name ,
                users.name as creator,
                lots.image_path, description,
                roll_time
            from lots join users
            on(lots.creator_id = users.id)
            where status = "active"
        ;');

        return response()->json($lots, HttpResponse::HTTP_OK);
    }

    public static function joinLot($request, $lid)
    {
        $uid = AuthenticateService::getUserId($request);
        if ($uid == null) {
            return response()->json(
                ['data' => 'UnAuthenticated'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }

        $userLots = LotUser
            ::where('user_id', '=', $uid)
            ->count();

        if ($userLots >= 5) {
            return response()->json(
                ['data' => 'User is already joined to 5 lotteries'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }

        $ifJoined = LotUser
            ::where('lot_id', '=', $lid)
            ->where('user_id', '=', $uid)
            ->first();

        if ($ifJoined == null) {
            LotUser::create([
                'lot_id' => $lid,
                'user_id' => $uid
            ]);
            return response()->json(
                ['data' => 'User was added'],
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }

        return response()->json(['data' => 'User already joined'], HttpResponse::HTTP_OK);
    }

}
