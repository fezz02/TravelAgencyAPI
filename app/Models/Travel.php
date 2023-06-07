<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Travel extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'is_public',
        'name',
        'description',
        'number_of_days',
        'number_of_nights',
    ];

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($travel) {
            Travel::updateAttributes($travel);
        });

        static::updating(function ($travel) {
            Travel::updateAttributes($travel);
        });
    }

    private static function updateAttributes($travel): Travel
    {
        $travel->slug = Str::slug($travel->title);
        $travel->number_of_nights = $travel->number_of_days - 1;
        return $travel;
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
}
