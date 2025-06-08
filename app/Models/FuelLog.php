<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FuelLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'logged_by',
        'fuel_date',
        'odometer_reading',
        'quantity',
        'price_per_unit',
        'total_cost',
        'fuel_type',
        'gas_station',
        'miles_driven',
        'is_full_tank',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fuel_date' => 'date',
        'odometer_reading' => 'integer',
        'quantity' => 'decimal:3',
        'price_per_unit' => 'decimal:3',
        'total_cost' => 'decimal:2',
        'miles_driven' => 'integer',
        'is_full_tank' => 'boolean',
    ];

    /**
     * Fuel type constants (same as Vehicle)
     */
    public const FUEL_GASOLINE = 'gasoline';
    public const FUEL_DIESEL = 'diesel';
    public const FUEL_HYBRID = 'hybrid';
    public const FUEL_ELECTRIC = 'electric';

    /**
     * Get all available fuel types
     */
    public static function getFuelTypes(): array
    {
        return [
            self::FUEL_GASOLINE,
            self::FUEL_DIESEL,
            self::FUEL_HYBRID,
            self::FUEL_ELECTRIC,
        ];
    }

    /**
     * Get the vehicle this fuel log belongs to
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who logged this fuel entry
     */
    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    /**
     * Scope to get fuel logs by vehicle
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope to get fuel logs by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('fuel_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get fuel logs by fuel type
     */
    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Scope to get only full tank entries
     */
    public function scopeFullTank($query)
    {
        return $query->where('is_full_tank', true);
    }

    /**
     * Scope to get recent fuel logs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('fuel_date', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope to get expensive fuel entries (above threshold)
     */
    public function scopeExpensive($query, $threshold = 50)
    {
        return $query->where('total_cost', '>', $threshold);
    }

    /**
     * Get fuel efficiency (miles per gallon) for this entry
     */
    public function getFuelEfficiencyAttribute(): ?float
    {
        if (!$this->miles_driven || !$this->quantity || $this->miles_driven <= 0 || $this->quantity <= 0) {
            return null;
        }

        return round($this->miles_driven / $this->quantity, 2);
    }

    /**
     * Get cost per mile for this entry
     */
    public function getCostPerMileAttribute(): ?float
    {
        if (!$this->miles_driven || !$this->total_cost || $this->miles_driven <= 0) {
            return null;
        }

        return round($this->total_cost / $this->miles_driven, 3);
    }

    /**
     * Check if this is a recent entry (within last week)
     */
    public function isRecent(): bool
    {
        return $this->fuel_date->isAfter(Carbon::now()->subWeek());
    }

    /**
     * Get the previous fuel log for the same vehicle
     */
    public function getPreviousFuelLog()
    {
        return static::where('vehicle_id', $this->vehicle_id)
                    ->where('fuel_date', '<', $this->fuel_date)
                    ->orderBy('fuel_date', 'desc')
                    ->first();
    }

    /**
     * Get the next fuel log for the same vehicle
     */
    public function getNextFuelLog()
    {
        return static::where('vehicle_id', $this->vehicle_id)
                    ->where('fuel_date', '>', $this->fuel_date)
                    ->orderBy('fuel_date', 'asc')
                    ->first();
    }

    /**
     * Calculate miles driven since last fuel log
     */
    public function calculateMilesDriven(): ?int
    {
        $previousLog = $this->getPreviousFuelLog();
        
        if (!$previousLog) {
            return null;
        }

        return $this->odometer_reading - $previousLog->odometer_reading;
    }

    /**
     * Auto-calculate miles driven when saving
     */
    protected static function booted()
    {
        static::saving(function ($fuelLog) {
            // Auto-calculate total cost if not provided
            if (!$fuelLog->total_cost && $fuelLog->quantity && $fuelLog->price_per_unit) {
                $fuelLog->total_cost = $fuelLog->quantity * $fuelLog->price_per_unit;
            }

            // Auto-calculate miles driven if not provided
            if (!$fuelLog->miles_driven && !$fuelLog->isDirty('miles_driven')) {
                $calculatedMiles = $fuelLog->calculateMilesDriven();
                if ($calculatedMiles !== null && $calculatedMiles > 0) {
                    $fuelLog->miles_driven = $calculatedMiles;
                }
            }
        });
    }
}
