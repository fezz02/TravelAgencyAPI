<?php

namespace App\Traits;

use App\Models\Travel;
use Illuminate\Database\Eloquent\Builder;

trait FromTravel {

    public function scopeFromTravel(Builder $query, Travel|int $travel){
        $travelId = (is_int($travel)) ? $travel : $travel->id;

        return $query->whereHas('travel', fn($query) => $query->where('id', $travelId));
    }
}