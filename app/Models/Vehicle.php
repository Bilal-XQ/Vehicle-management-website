<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'make_id',
        'model',
        'year',
        'license_plate',
        'vin',
        'color',
        'fuel_type',
        'engine_size',
        'transmission',
        'mileage',
        'purchase_date',
        'purchase_price',
        'status',
        'insurance_expiry',
        'registration_expiry',
        'inspection_expiry',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'insurance_expiry' => 'date',
        'registration_expiry' => 'date',
        'inspection_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'engine_size' => 'decimal:1',
        'mileage' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Status constants
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SOLD = 'sold';

    /**
     * Fuel type constants
     */
    public const FUEL_GASOLINE = 'gasoline';
    public const FUEL_DIESEL = 'diesel';
    public const FUEL_HYBRID = 'hybrid';
    public const FUEL_ELECTRIC = 'electric';

    /**
     * Transmission constants
     */
    public const TRANSMISSION_MANUAL = 'manual';
    public const TRANSMISSION_AUTOMATIC = 'automatic';
    public const TRANSMISSION_CVT = 'cvt';

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_MAINTENANCE,
            self::STATUS_INACTIVE,
            self::STATUS_SOLD,
        ];
    }

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
     * Get all available transmission types
     */
    public static function getTransmissionTypes(): array
    {
        return [
            self::TRANSMISSION_MANUAL,
            self::TRANSMISSION_AUTOMATIC,
            self::TRANSMISSION_CVT,
        ];
    }

    /**
     * Get the vehicle make
     */
    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }

    /**
     * Get vehicle assignments
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(VehicleAssignment::class);
    }

    /**
     * Get assigned users
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'vehicle_assignments')
                    ->withPivot(['assigned_at', 'unassigned_at', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Get currently assigned user
     */
    public function currentlyAssignedUser()
    {
        return $this->assignedUsers()->whereNull('vehicle_assignments.unassigned_at')->first();
    }

    /**
     * Get maintenance logs
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    /**
     * Get fuel logs
     */
    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    /**
     * Get vehicle documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    /**
     * Scope to get active vehicles
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get vehicles by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get vehicles by fuel type
     */
    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Check if vehicle is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if vehicle is in maintenance
     */
    public function isInMaintenance(): bool
    {
        return $this->status === self::STATUS_MAINTENANCE;
    }

    /**
     * Get vehicles with expiring documents
     */
    public function scopeExpiringDocuments($query, $days = 30)
    {
        $date = Carbon::now()->addDays($days);
        
        return $query->where(function($q) use ($date) {
            $q->where('insurance_expiry', '<=', $date)
              ->orWhere('registration_expiry', '<=', $date)
              ->orWhere('inspection_expiry', '<=', $date);
        });
    }

    /**
     * Get the vehicle's full name (Make Model Year)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->make->name} {$this->model} {$this->year}";
    }

    /**
     * Get the vehicle's age in years
     */
    public function getAgeAttribute(): int
    {
        return Carbon::now()->year - $this->year;
    }

    /**
     * Check if any documents are expiring soon
     */
    public function hasExpiringDocuments($days = 30): bool
    {
        $date = Carbon::now()->addDays($days);
        
        return $this->insurance_expiry <= $date ||
               $this->registration_expiry <= $date ||
               $this->inspection_expiry <= $date;
    }

    /**
     * Get latest fuel efficiency (miles per gallon)
     */
    public function getLatestFuelEfficiency()
    {
        $latestFuelLog = $this->fuelLogs()
                             ->orderBy('created_at', 'desc')
                             ->first();

        if ($latestFuelLog && $latestFuelLog->miles_driven > 0 && $latestFuelLog->quantity > 0) {
            return round($latestFuelLog->miles_driven / $latestFuelLog->quantity, 2);
        }

        return null;
    }
}
