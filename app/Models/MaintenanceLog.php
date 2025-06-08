<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MaintenanceLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'maintenance_type_id',
        'performed_by',
        'service_provider',
        'maintenance_date',
        'mileage_at_service',
        'cost',
        'parts_used',
        'labor_hours',
        'description',
        'next_service_mileage',
        'next_service_date',
        'warranty_until',
        'receipt_number',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'maintenance_date' => 'date',
        'next_service_date' => 'date',
        'warranty_until' => 'date',
        'cost' => 'decimal:2',
        'labor_hours' => 'decimal:2',
        'mileage_at_service' => 'integer',
        'next_service_mileage' => 'integer',
        'parts_used' => 'json',
    ];

    /**
     * Status constants
     */
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_COMPLETED,
            self::STATUS_SCHEDULED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get the vehicle this maintenance belongs to
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the maintenance type
     */
    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    /**
     * Get the user who performed the maintenance
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope to get completed maintenance
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get scheduled maintenance
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope to get maintenance by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get maintenance due for service
     */
    public function scopeDueForService($query, $days = 30)
    {
        $date = Carbon::now()->addDays($days);
        
        return $query->where('next_service_date', '<=', $date)
                    ->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Scope to get maintenance by vehicle
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope to get maintenance by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('maintenance_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get expensive maintenance (above threshold)
     */
    public function scopeExpensive($query, $threshold = 500)
    {
        return $query->where('cost', '>', $threshold);
    }

    /**
     * Check if maintenance is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if maintenance is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if maintenance is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            return false;
        }

        return $this->next_service_date && $this->next_service_date->isPast();
    }

    /**
     * Check if maintenance is due soon
     */
    public function isDueSoon($days = 7): bool
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            return false;
        }

        if (!$this->next_service_date) {
            return false;
        }

        return $this->next_service_date->diffInDays(Carbon::now()) <= $days;
    }

    /**
     * Get the warranty status
     */
    public function getWarrantyStatusAttribute(): string
    {
        if (!$this->warranty_until) {
            return 'No warranty';
        }

        if ($this->warranty_until->isPast()) {
            return 'Expired';
        }

        $daysLeft = $this->warranty_until->diffInDays(Carbon::now());
        
        if ($daysLeft <= 30) {
            return "Expires in {$daysLeft} days";
        }

        return 'Active';
    }

    /**
     * Get total parts cost from parts_used JSON
     */
    public function getTotalPartsCostAttribute(): float
    {
        if (!$this->parts_used || !is_array($this->parts_used)) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($this->parts_used as $part) {
            if (isset($part['cost']) && is_numeric($part['cost'])) {
                $total += (float) $part['cost'];
            }
        }

        return $total;
    }

    /**
     * Get labor cost (assuming hourly rate if not specified)
     */
    public function getLaborCostAttribute(): float
    {
        if (!$this->labor_hours) {
            return 0.0;
        }

        // Default labor rate - this could be configurable
        $defaultLaborRate = 75.0;
        
        return $this->labor_hours * $defaultLaborRate;
    }
}
