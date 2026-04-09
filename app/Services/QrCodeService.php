<?php

namespace App\Services;

use App\Models\QrToken;
use App\Models\Event;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate a unique token for an event.
     */
    public function generateToken(int $eventId): string
    {
        $secret = config('absensi.qr_secret_key');
        $timestamp = now()->timestamp;
        $random = Str::random(8);

        return hash('sha256', $eventId . $timestamp . $secret . $random);
    }

    /**
     * Create a new QR token record and return it.
     */
    public function refreshToken(int $eventId): QrToken
    {
        $expirySeconds = config('absensi.qr_token_expiry_seconds', 60);

        $token = QrToken::create([
            'token' => $this->generateToken($eventId),
            'event_id' => $eventId,
            'expired_at' => now()->addSeconds($expirySeconds),
        ]);

        return $token;
    }

    /**
     * Validate a token string - returns QrToken if valid, null if not.
     */
    public function validateToken(string $token): ?QrToken
    {
        return QrToken::where('token', $token)
            ->where('expired_at', '>', now())
            ->first();
    }

    /**
     * Generate QR code image as SVG string.
     */
    public function generateQrImage(string $url, int $size = 300): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->errorCorrection('H')
            ->margin(2)
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->generate($url);
    }

    /**
     * Cleanup expired tokens.
     */
    public function cleanupExpired(): int
    {
        return QrToken::where('expired_at', '<', now())->delete();
    }

    /**
     * Get the latest valid token for an event, or create a new one.
     */
    public function getOrCreateToken(int $eventId): QrToken
    {
        $existing = QrToken::where('event_id', $eventId)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        return $this->refreshToken($eventId);
    }
}
