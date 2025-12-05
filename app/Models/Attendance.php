<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Attendance extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'staff_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'latitude',
        'longitude',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'battery_percentage',
        'is_wifi_on',
        'signal_strength',
        'status',
        'break_start_time',
        'break_end_time',
        'break_duration',
        'late_reason',
        'early_checkout_reason',
        'early_checkout_status',
        'location_history',
        'device_info',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'break_duration' => 'decimal:2',
        'is_wifi_on' => 'boolean',
        'location_history' => 'array',
        'device_info' => 'array',
    ];

    /**
     * Get the staff that owns the attendance record.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('attendance_date', [$fromDate, $toDate]);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get today's attendance
     */
    public function scopeToday($query)
    {
        return $query->where('attendance_date', today());
    }

    /**
     * Scope to get current week attendance
     */
    public function scopeCurrentWeek($query)
    {
        return $query->whereBetween('attendance_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope to get current month attendance
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereBetween('attendance_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Check if staff is currently checked in
     */
    public function isCheckedIn(): bool
    {
        return $this->check_in_time && !$this->check_out_time;
    }

    /**
     * Check if staff is on break
     */
    public function isOnBreak(): bool
    {
        return $this->break_start_time && !$this->break_end_time;
    }

    /**
     * Calculate total working hours for the day
     */
    public function getTotalWorkingHoursAttribute(): float
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return 0;
        }

        $totalSeconds = $this->check_out_time->diffInSeconds($this->check_in_time);
        $breakSeconds = $this->break_duration ? ($this->break_duration * 3600) : 0;

        return round(($totalSeconds - $breakSeconds) / 3600, 2);
    }

    /**
     * Get formatted check-in time
     */
    public function getFormattedCheckInTimeAttribute(): ?string
    {
        return $this->check_in_time?->format('H:i');
    }

    /**
     * Get formatted check-out time
     */
    public function getFormattedCheckOutTimeAttribute(): ?string
    {
        return $this->check_out_time?->format('H:i');
    }
}
