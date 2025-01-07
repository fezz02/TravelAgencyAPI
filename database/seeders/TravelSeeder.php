<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Travel;
use Illuminate\Database\Seeder;

final class TravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Travel::factory(200)->create();
    }
}
