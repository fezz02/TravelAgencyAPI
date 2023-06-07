<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\RespectsPrivacy;

class Travel extends Model
{
    use HasFactory, HasUuids, Sluggable, RespectsPrivacy;

    protected $table = 'travels';

    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->number_of_days - 1
        );
    }
    
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
}
