<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = ['unmoderated', 'accepted', 'active', 'excited'];

        return [
            'name' => $this->faker->streetName(),
            'description' => $this->faker->text(150),
            'creator_id' => 1,
            'status' => $statuses[array_rand($statuses)]
        ];
    }
}
