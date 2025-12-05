<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'task_type',
        'assigned_by_id',
        'assigned_to_id',
        'client_id',
        'latitude',
        'longitude',
        'is_geo_fence_enabled',
        'max_radius',
        'start_date_time',
        'end_date_time',
        'status',
        'for_date',
        'attachments',
        'location_history',
        'started_at',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'max_radius' => 'decimal:2',
        'is_geo_fence_enabled' => 'boolean',
        'for_date' => 'date',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'attachments' => 'array',
        'location_history' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the staff member who assigned this task.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_by_id');
    }

    /**
     * Get the staff member this task is assigned to.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_to_id');
    }

    /**
     * Get the client associated with this task.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'client_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by assigned staff
     */
    public function scopeAssignedTo($query, $staffId)
    {
        return $query->where('assigned_to_id', $staffId);
    }

    /**
     * Scope to filter by date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('for_date', $date);
    }

    /**
     * Scope to get tasks for today
     */
    public function scopeToday($query)
    {
        return $query->where('for_date', today());
    }

    /**
     * Scope to get overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('end_date_time', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get tasks within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('for_date', [$startDate, $endDate]);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->end_date_time && $this->end_date_time->isPast() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if task is within geofence
     */
    public function isWithinGeofence(float $userLat, float $userLng): bool
    {
        if (!$this->is_geo_fence_enabled || !$this->latitude || !$this->longitude || !$this->max_radius) {
            return true; // No geofence restrictions
        }

        $distance = $this->calculateDistance($userLat, $userLng, $this->latitude, $this->longitude);
        return $distance <= $this->max_radius;
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->start_date_time || !$this->end_date_time) {
            return null;
        }

        $hours = $this->start_date_time->diffInHours($this->end_date_time);
        $minutes = $this->start_date_time->diffInMinutes($this->end_date_time) % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    /**
     * Get task priority based on type and deadlines
     */
    public function getPriorityAttribute(): string
    {
        if ($this->task_type === 'urgent') {
            return 'high';
        }

        if ($this->isOverdue()) {
            return 'high';
        }

        if ($this->end_date_time && $this->end_date_time->diffInHours(now()) < 24) {
            return 'medium';
        }

        return 'low';
    }
}
