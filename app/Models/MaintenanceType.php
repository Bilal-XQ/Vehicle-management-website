<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'recommended_interval_miles',
        'recommended_interval_months',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recommended_interval_miles' => 'integer',
        'recommended_interval_months' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Category constants
     */
    public const CATEGORY_ROUTINE = 'Routine';
    public const CATEGORY_PREVENTIVE = 'Preventive';
    public const CATEGORY_REPAIR = 'Repair';
    public const CATEGORY_INSPECTION = 'Inspection';
    public const CATEGORY_EMERGENCY = 'Emergency';

    /**
     * Get all available categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ROUTINE,
            self::CATEGORY_PREVENTIVE,
            self::CATEGORY_REPAIR,
            self::CATEGORY_INSPECTION,
            self::CATEGORY_EMERGENCY,
        ];
    }

    /**
     * Get maintenance logs for this type
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    /**
     * Scope to get only active maintenance types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get maintenance types by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get routine maintenance types
     */
    public function scopeRoutine($query)
    {
        return $query->where('category', self::CATEGORY_ROUTINE);
    }

    /**
     * Scope to get preventive maintenance types
     */
    public function scopePreventive($query)
    {
        return $query->where('category', self::CATEGORY_PREVENTIVE);
    }

    /**
     * Check if this maintenance type is routine
     */
    public function isRoutine(): bool
    {
        return $this->category === self::CATEGORY_ROUTINE;
    }

    /**
     * Check if this maintenance type is preventive
     */
    public function isPreventive(): bool
    {
        return $this->category === self::CATEGORY_PREVENTIVE;
    }

    /**
     * Get the recommended interval as a human-readable string
     */
    public function getRecommendedIntervalAttribute(): string
    {
        $intervals = [];
        
        if ($this->recommended_interval_miles) {
            $intervals[] = number_format($this->recommended_interval_miles) . ' miles';
        }
        
        if ($this->recommended_interval_months) {
            $intervals[] = $this->recommended_interval_months . ' months';
        }
        
        return implode(' or ', $intervals) ?: 'As needed';
    }
}
