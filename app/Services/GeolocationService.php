<?php

namespace App\Services;

class GeolocationService
{
    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in meters.
     */
    public function calculateHaversine(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if user location is within acceptable radius of event.
     */
    public function isWithinRadius(
        float $userLat,
        float $userLon,
        float $eventLat,
        float $eventLon,
        int $radius = 50
    ): bool {
        $distance = $this->calculateHaversine($userLat, $userLon, $eventLat, $eventLon);
        return $distance <= $radius;
    }

    /**
     * Detect potential fake GPS based on accuracy and patterns.
     * Returns fraud score between 0.0 (clean) and 1.0 (suspicious).
     */
    public function detectFakeGps(
        float $latitude,
        float $longitude,
        ?float $accuracy = null,
        ?float $altitude = null,
        ?float $speed = null
    ): float {
        $score = 0.0;

        // Check if accuracy is suspiciously perfect (< 1m)
        if ($accuracy !== null && $accuracy < 1.0) {
            $score += 0.3;
        }

        // Check if accuracy is very poor (> 500m) - might indicate mocking
        if ($accuracy !== null && $accuracy > 500) {
            $score += 0.2;
        }

        // Perfectly round coordinates are suspicious
        if ($this->isPerfectlyRound($latitude) || $this->isPerfectlyRound($longitude)) {
            $score += 0.3;
        }

        // No altitude data on mobile can be suspicious
        if ($altitude === null || $altitude == 0) {
            $score += 0.1;
        }

        // Speed check - if moving impossibly fast
        if ($speed !== null && $speed > 100) { // > 100 m/s = 360 km/h
            $score += 0.4;
        }

        return min($score, 1.0);
    }

    /**
     * Check if a coordinate value is suspiciously round.
     */
    private function isPerfectlyRound(float $value): bool
    {
        $decimals = strlen(substr(strrchr(number_format($value, 8, '.', ''), '.'), 1));
        $significantDecimals = rtrim(number_format($value, 8, '.', ''), '0');
        $actualDecimals = strlen(substr(strrchr($significantDecimals, '.'), 1));

        return $actualDecimals <= 2;
    }
}
