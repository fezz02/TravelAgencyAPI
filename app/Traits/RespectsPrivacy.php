<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RespectsPrivacy
{
    public function scopeOnlyPublic(Builder $query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOnlyPrivate(Builder $query)
    {
        return $query->where('is_public', false);
    }
}
