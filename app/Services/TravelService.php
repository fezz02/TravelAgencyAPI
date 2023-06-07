<?php

namespace App\Services;

use App\Models\Travel;

class TravelService {

    public function store(array $fields): Travel
    {
        return Travel::create($fields);
    }

    public function update(array $fields, Travel $travel): Travel
    {
        $travel->update($fields);
        return $travel->fresh();
    }
}