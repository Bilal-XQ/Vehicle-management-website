<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VehicleAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'assigned_at',
        'unassigned_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
    ];

    /**
     * Get the vehicle for this assignment
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user for this assignment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active assignments (not unassigned)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('unassigned_at');
    }

    /**
     * Scope to get completed assignments (unassigned)
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('unassigned_at');
    }

    /**
     * Scope to get assignments for a specific vehicle
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope to get assignments for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get assignments by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('assigned_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get long-term assignments (more than X days)
     */
    public function scopeLongTerm($query, $days = 30)
    {
        $date = Carbon::now()->subDays($days);
        return $query->where('assigned_at', '<=', $date)
                    ->whereNull('unassigned_at');
    }

    /**
     * Check if this assignment is currently active
     */
    public function isActive(): bool
    {
        return $this->unassigned_at === null;
    }

    /**
     * Check if this assignment is completed
     */
    public function isCompleted(): bool
    {
        return $this->unassigned_at !== null;
    }

    /**
     * Get the duration of this assignment
     */
    public function getDurationAttribute(): ?int
    {
        $endDate = $this->unassigned_at ?? Carbon::now();
        return $this->assigned_at->diffInDays($endDate);
    }

    /**
     * Get the duration in a human readable format
     */
    public function getDurationHumanAttribute(): string
    {
        $endDate = $this->unassigned_at ?? Carbon::now();
        return $this->assigned_at->diffForHumans($endDate, true);
    }

    /**
     * Unassign the vehicle from the user
     */
    public function unassign(string $notes = null): bool
    {
        $this->unassigned_at = Carbon::now();
        
        if ($notes) {
            $this->notes = $this->notes ? $this->notes . "\n" . $notes : $notes;
        }

        return $this->save();
    }

    /**
     * Auto-set assigned_at when creating
     */
    protected static function booted()
    {
        static::creating(function ($assignment) {
            if (!$assignment->assigned_at) {
                $assignment->assigned_at = Carbon::now();
            }
        });
    }
}
