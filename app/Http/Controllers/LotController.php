<?php

namespace App\Http\Controllers;

//Use User Model
use App\Models\Lot;

//Use Resources to convert into json
use App\Http\Resources\LotResource as LotResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LotController extends Controller
{
    public function getLots()
    {
        $users = Lot::get();
        return LotResource::collection($users);
    }

    public function getLot($id)
    {
        $user = Lot::get()->where('id', $id);
        return LotResource::collection($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'creator_id' => ['required', 'integer'],
            'status' => ['required', 'string', 'min:3'],
        ]);
    }


    protected function createLot(Request $request)
    {
        $this->validator($request->all())->validate();

        return $this->create($request->all());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Lot
     */
    public function create(array $data)
    {
        return Lot::create([
            'name' => $data['name'],
            'creator_id' => $data['creator_id'],
            'status' => $data['status'],
            'image_path' => $data['image_path'] ?? NULL,
            'description' => $data['description'] ?? NULL,
        ]);
    }

    public function updateLot(Request $request, $id)
    {
        $lot = Lot::findOrFail($id);
        $lot->update($request->all());

        return $lot;
    }

    public function deleteLot($id)
    {
        Lot::find($id)->delete();

        return 204;
    }

}
