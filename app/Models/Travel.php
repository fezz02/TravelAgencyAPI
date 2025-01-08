<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\RespectsPrivacy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

final class Travel extends Model
{
    use HasFactory;
    use HasSlug;
    use HasUuids;
    use RespectsPrivacy;

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
        'number_of_days' => 'int',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->number_of_days - 1
        );
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
}
