<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(50)->create();
    }
}
