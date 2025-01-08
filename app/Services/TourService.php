<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Model;

final class TourService
{
    public function store(array $fields, Travel $travel): Model
    {
        return $travel->tours()->create($fields);
    }

    public function update(array $fields, Tour $tour): Model
    {
        $tour->update($fields);

        return $tour->fresh();
    }
}
