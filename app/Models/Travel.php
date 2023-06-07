<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Travel extends Model
{
    use HasFactory, HasUuids, Sluggable;

    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days',
    ];

    protected $hidden = [
        'id'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'number_of_days' => 'int'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['numer_of_days'] - 1
        );
    }
    
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
}
