<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RespectsPrivacy {

    public function scopePublic(Builder $query){
        return $query->where('is_public', true);
    }

    public function scopePrivate(Builder $query){
        return $query->where('is_public', false);
    }
}