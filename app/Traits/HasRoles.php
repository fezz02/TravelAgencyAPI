<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Role;
use Illuminate\Support\Str;

trait HasRoles
{
    public function assignRole(string $roleName)
    {
        $roleName = Str::snake($roleName);

        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role]);
        return $this->roles;
    }
}
