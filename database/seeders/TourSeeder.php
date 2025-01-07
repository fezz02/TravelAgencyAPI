<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

final class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tour::factory(600)->create();
    }
}
