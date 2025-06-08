<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VehicleDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_name',
        'file_path',
        'file_size',
        'mime_type',
        'expiry_date',
        'issued_date',
        'document_number',
        'issuing_authority',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiry_date' => 'date',
        'issued_date' => 'date',
        'file_size' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Document type constants
     */
    public const TYPE_INSURANCE = 'insurance';
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_INSPECTION = 'inspection';
    public const TYPE_WARRANTY = 'warranty';
    public const TYPE_MANUAL = 'manual';
    public const TYPE_RECEIPT = 'receipt';
    public const TYPE_OTHER = 'other';

    /**
     * Get all available document types
     */
    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_INSURANCE => 'Insurance Certificate',
            self::TYPE_REGISTRATION => 'Vehicle Registration',
            self::TYPE_INSPECTION => 'Inspection Certificate',
            self::TYPE_WARRANTY => 'Warranty Document',
            self::TYPE_MANUAL => 'Owner\'s Manual',
            self::TYPE_RECEIPT => 'Purchase Receipt',
            self::TYPE_OTHER => 'Other Document',
        ];
    }

    /**
     * Get the vehicle this document belongs to
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope to get active documents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get documents by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to get documents for a specific vehicle
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope to get expiring documents
     */
    public function scopeExpiring($query, $days = 30)
    {
        $date = Carbon::now()->addDays($days);
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', $date)
                    ->where('is_active', true);
    }

    /**
     * Scope to get expired documents
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', Carbon::now())
                    ->where('is_active', true);
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if document is expiring soon
     */
    public function isExpiringSoon($days = 30): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(Carbon::now()) <= $days && !$this->isExpired();
    }

    /**
     * Get the document type display name
     */
    public function getDocumentTypeNameAttribute(): string
    {
        $types = self::getDocumentTypes();
        return $types[$this->document_type] ?? ucfirst($this->document_type);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get the expiry status
     */
    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'No expiry';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        if ($this->isExpiringSoon(30)) {
            $days = $this->expiry_date->diffInDays(Carbon::now());
            return "Expires in {$days} days";
        }

        return 'Valid';
    }

    /**
     * Get the document URL for download
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    /**
     * Check if file exists in storage
     */
    public function fileExists(): bool
    {
        return $this->file_path && Storage::exists($this->file_path);
    }

    /**
     * Delete the associated file when deleting the document
     */
    protected static function booted()
    {
        static::deleting(function ($document) {
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }

    /**
     * Get documents that need attention (expired or expiring)
     */
    public function scopeNeedsAttention($query, $days = 30)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', Carbon::now()->addDays($days));
    }

    /**
     * Get the days until expiry (negative if expired)
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false);
    }
}
