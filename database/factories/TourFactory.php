<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Tour;
use App\Models\Travel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $travelId = Travel::query()
            ->select(['id'])
            ->limit(1)
            ->inRandomOrder()
            ->first()
            ->id;

        return [
            'travel_id' => $travelId,
            'name' => fake()->sentence(2),
            'starting_date' => fake()->dateTimeThisYear('+2 months'),
            'ending_date' => fake()->dateTimeThisYear('+3 months'),
            'price' => fake()->randomFloat(2, 20, 999)
        ];
    }
}
