<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\Travel;

class TourService {

    public function store(array $fields, Travel $travel): Tour
    {
        return $travel->tours()->create($fields);
    }

    public function update(array $fields, Tour $tour): Tour
    {
        $tour->update($fields);
        return $tour->fresh();
    }
}