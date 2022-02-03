<?php

namespace App\Services;

use App\Mail\WinNotification;
use App\Models\User;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\LotUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LotService
{

    public static function createdBy(int $id)
    {
        return Lot::get()->where('creator_id', $id)
            ->whereIn('status', ['active', 'expired'])
            ->toArray();
    }

    public static function wonBy(int $id)
    {
        return Lot::get()->where('winner_id', $id)
            ->whereIn('status', ['active', 'expired'])
            ->toArray();
    }

    public static function getById(int $id): array
    {
        return DB::select("
            select
                lots.id, lots.name , creator_id,
                (select name from users where id = creator_id) as creator,
                (select name from users where id = winner_id) as winner,
                lots.image_path, description,
                (select timestampdiff(second, now(), roll_time)) as roll_time,
                winner_id
            from lots
            where lots.id = $id
        ;");
    }

    public static function getActive(): array
    {
        return DB::table('lots')
            ->join('users','lots.creator_id','=','users.id')
            ->where('lots.status', '=', 'active')
            ->get(['lots.id', 'lots.name',
                'users.name as creator',
                'lots.image_path', 'description',
                'roll_time'])
            ->toArray()
            ;
    }

    public static function joinLot(Request $request, int $lid): array
    {
        $uid = AuthenticateService::getUserId($request);
        if ($uid === null) {
            return ['data' => 'UnAuthenticated'];
        }

        $validationData = self::joinLotValidation($uid, $lid);
        if (!array_key_exists('validated', $validationData)) {
            return $validationData;
        }

        LotUser::create([
            'lot_id' => $lid,
            'user_id' => $uid
        ]);

        return ['added' => true];
    }

    private static function joinLotValidation(int $uid, int $lid): array
    {
        $user = User::where('id', $uid)->first();
        if ($user['email_verified_at'] === null) {
            return ['data' => 'User has not verified email'];
        }

        $userLots = LotUser
            ::where('user_id', '=', $uid)
            ->count();

        if ($userLots >= 5) {
            return ['data' => 'User is already joined to 5 lotteries'];
        }

        $lot = Lot::where('id', $lid)->first();
        if ($lot === null) {
            return ['data' => 'lot is not exists'];
        }

        if ($lot['creator_id'] === $uid) {
            return ['data' => 'Creator can not join to own lot'];
        }

        if ($lot['status'] !== 'active') {
            return ['data' => 'Lot is not active'];
        }

        $ifJoined = LotUser
            ::where('lot_id', '=', $lid)
            ->where('user_id', '=', $uid)
            ->first();

        if ($ifJoined != null) {
            return ['data' => 'User already joined'];
        }

        return ['validated' => true];
    }

    private static function createValidation(Request $request): array
    {
        $user = AuthenticateService::checkUser($request);
        if (array_key_exists('data', $user)) {
            return $user;
        }
        if ($user['email_verified_at'] === null) {
            return ['data' => 'User has not verified email'];
        }

        $request->validate([
            'name' => 'required|min:5',
            'image_path' => 'min:5',
            'description' => 'min:15',
        ]);

        return ['validated' => true];
    }

    public static function createFile(Request $request, int $id): ?string
    {
        if ($request->hasFile('imageFile')) {
            return Storage::putFile(
                "lots/$id",
                $request->file('imageFile'),
                'public'
            );
        }
        return null;
    }

    public static function create(Request $request): array
    {
        $validationResponse = self::createValidation($request);
        if (!array_key_exists('validated', $validationResponse)) {
            return $validationResponse;
        }

        $lotData = [
            'name' => $request['name'],
            'creator_id' => AuthenticateService::getUserId($request),
            'status' => 'unmoderated',
            'description' => $request['description'] ?: null,
        ];
        $newLot = Lot::create($lotData);
        $filePath = self::createFile($request, $newLot->id);
        if ($filePath !== null) {
            $lot = Lot::findOrFail($newLot->id);
            $lot->update(['image_path' => $filePath]);
        }

        return ['created' => true];
    }

    private static function updateValidation(Request $request, int $id): array
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
        $lot = Lot::findOrFail($id)->toArray();
        if ($lot['creator_id'] !== $uid) {
            return ['data' => 'user is not owner of lot'];
        }
        if ($lot['status'] !== 'accepted') {
            return ['data' => 'unable to update lot'];
        }

        return ['validated' => true];
    }

    public static function update(Request $request, int $id): array
    {
        $validationResponse = self::updateValidation($request, $id);
        if (!array_key_exists('validated', $validationResponse)) {
            return $validationResponse;
        }

        $data = $request->all();
        if (isset($data['roll_time'])) {
            $data += ['status' => 'active'];
        }

        Lot::findOrFail($id)->update($data);

        return ['updated' => true];
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

    }

}
