<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = User::create([
            'name' => 'creator',
            'email' => 'creator@gmail.com',
            'password' => 'password',
            'email_verified_at' => Carbon::now(),
        ])['id'];

        Lot::factory()
            ->count(50)
            ->create(['creator_id' => $id,]);
    }
}
