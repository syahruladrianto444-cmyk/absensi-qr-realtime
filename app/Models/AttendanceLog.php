<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nama',
        'npm',
        'latitude',
        'longitude',
        'distance',
        'status',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'fraud_score',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance' => 'decimal:2',
        'fraud_score' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeSuspicious($query)
    {
        return $query->where('fraud_score', '>', 0.5);
    }

    public function getFraudLevelAttribute(): string
    {
        if ($this->fraud_score >= 0.7) return 'high';
        if ($this->fraud_score >= 0.4) return 'medium';
        return 'low';
    }
}
