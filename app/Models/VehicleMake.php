<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleMake extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get vehicles for this make
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'make_id');
    }

    /**
     * Scope to get only active makes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get popular makes (with most vehicles)
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('vehicles')
                    ->orderBy('vehicles_count', 'desc')
                    ->limit($limit);
    }
}
