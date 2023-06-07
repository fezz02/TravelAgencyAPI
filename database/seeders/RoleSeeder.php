<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roleNames = collect([
            'editor',
            'admin'
        ]);

        $roleNames->each(fn($name) => Role::create(['name' => $name]));
    }
}
