<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roleNames = collect([
            'editor',
            'admin',
        ]);

        $roleNames->each(fn ($name) => Role::create(['name' => $name]));
    }
}
