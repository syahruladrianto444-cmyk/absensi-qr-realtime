<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'event_id',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expired_at);
    }

    public function scopeValid($query)
    {
        return $query->where('expired_at', '>', now());
    }
}
