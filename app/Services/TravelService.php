<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Travel;

final class TravelService
{
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
