<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_event',
        'latitude',
        'longitude',
        'radius',
        'start_time',
        'end_time',
        'is_active',
        'google_form_url',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function isWithinTimeWindow(): bool
    {
        $now = now();
        return $now->between($this->start_time, $this->end_time);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
