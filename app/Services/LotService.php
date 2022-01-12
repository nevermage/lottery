<?php

namespace App\Services;

use App\Mail\WinNotification;
use App\Models\User;
use App\Models\Lot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\AuthenticateService;
use App\Models\LotUser;
use Illuminate\Support\Facades\Mail;

class LotService
{

    public static function createdBy($id)
    {
        $lots = Lot::get()->where('creator_id', $id)
            ->whereIn('status', ['active', 'expired']);
        return $lots;
    }

    public static function wonBy($id)
    {
        $lots = Lot::get()->where('winner_id', $id)
            ->whereIn('status', ['active', 'expired']);
        return $lots;
    }

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

        return $lot;
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

        return $lots;
    }

    public static function joinLot($request, $lid)
    {
        $uid = AuthenticateService::getUserId($request);
        if ($uid == null) {
            return ['data' => 'UnAuthenticated'];
        }

        $validationData = self::joinLotValidation($uid, $lid);
        if ($validationData != null) {
            return $validationData;
        }

        LotUser::create([
            'lot_id' => $lid,
            'user_id' => $uid
        ]);

        return null;
    }

    public static function joinLotValidation($uid, $lid)
    {
        $user = User::where('id', $uid)->first();
        if ($user['email_verified_at'] == null) {
            return ['data' => 'User has not verified email'];
        }

        $userLots = LotUser
            ::where('user_id', '=', $uid)
            ->count();

        if ($userLots >= 5) {
            return ['data' => 'User is already joined to 5 lotteries'];
        }

        $lot = Lot::where('id', $lid)->first();
        if ($lot == null) {
            return ['data' => 'lot is not exists'];
        }

        if ($lot['creator_id'] == $uid) {
            return ['data' => 'Creator can not join to own lot'];
        }

        if ($lot['status'] != 'active') {
            return ['data' => 'Lot is not active'];
        }

        $ifJoined = LotUser
            ::where('lot_id', '=', $lid)
            ->where('user_id', '=', $uid)
            ->first();

        if ($ifJoined != null) {
            return ['data' => 'User already joined'];
        }

        return null;
    }

    public static function create($request)
    {
        $user = AuthenticateService::checkUser($request, 'array');
        if ($user == null) {
            return null;
        }
        if ($user['email_verified_at'] == null) {
            return ['data' => 'User has not verified email'];
        }

        $uid = $user['id'];
        $request->validate([
            'name' => 'required|min:5',
            'image_path' => 'min:5',
            'description' => 'min:15',
        ]);

        $lotData = [
            'name' => $request['name'],
            'creator_id' => $uid,
            'status' => 'unmoderated',
            'description' => $request['description'] ?: null,
            'image_path' => $request['image_path'] ?: null,
        ];

        Lot::create($lotData);

        return ['data' => 'Lot was created'];
    }

    public static function update($request, $id)
    {
        $uid = AuthenticateService::getUserId($request);
        if (!$uid) {
            return ['data' => 'UnAuthenticated'];
        }
        $request->validate([
            'name' => 'min:5',
            'image_path' => 'min:5',
            'description' => 'min:15',
            'roll_time' => 'date_format:Y-m-d H:i:s|after:tomorrow'
        ]);
        $lot = Lot::findOrFail($id);

        if ($lot['creator_id'] != $uid) {
            return ['data' => 'user is not owner of lot'];
        }
        if ($lot['status'] != 'accepted') {
            return ['data' => 'unable to update lot'];
        }

        $data = $request->all();
        if (isset($data['roll_time'])) {
            $data += ['status' => 'active'];
            //add scheduled task to roll winner
        }

        $lot->update($data);

        return null;
    }

    public static function rollWinner()
    {
        $now = Carbon::parse(Carbon::now())->startOfMinute()->toDateTimeString();

        DB::update("update lots set winner_id="
            . "(select user_id from lot_user where lot_id = lots.id order by rand() limit 1),"
            . "status='expired'"
            . " where roll_time = '$now';");

        $lots = Lot::where('roll_time', '=', $now)
            ->join('users', 'lots.winner_id', '=', 'users.id')
            ->get([
                'users.name as winner',
                'lots.name as name',
                'lots.id',
                'email',
            ])
            ->toArray();

        foreach ($lots as $lot) {
            Mail::to($lot['email'])
                ->send(new WinNotification($lot['winner'], $lot['id'], $lot['name']));
        }

        return 0;
    }

}
