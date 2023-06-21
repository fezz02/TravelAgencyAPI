<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Support\Str;

trait HasRoles
{
    public function assignRole(string $roleName)
    {
        $roleName = Str::snake($roleName);

        switch ($roleName) {
            case 'admin':
                $admin = Role::where('name', 'admin')->firstOrFail();
                $this->roles()->attach($admin);
            case 'editor':
                $editor = Role::where('name', 'editor')->firstOrFail();
                $this->roles()->attach($editor);
                break;
        }

        return $this->roles;
    }
}
