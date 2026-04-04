<?php

namespace App\Services;

use App\Models\Airport;
use App\Models\FlightDistance;

class FlightDistanceService
{
    const EARTH_RADIUS_KM = 6371.0;

    /**
     * Speed of sound at typical cruise altitude (FL350, ~-54°C): 1,062 km/h.
     * Used to convert Mach numbers stored in the aircraft table to km/h.
     */
    const MACH_TO_KMH = 1062.0;

    /**
     * Calculate great-circle distance between two airports using the Haversine formula.
     * Returns distance in kilometres.
     */
    public function calculateDistance(Airport $origin, Airport $destination): float
    {
        $lat1 = deg2rad((float) $origin->latitude);
        $lon1 = deg2rad((float) $origin->longitude);
        $lat2 = deg2rad((float) $destination->latitude);
        $lon2 = deg2rad((float) $destination->longitude);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) ** 2
            + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round(self::EARTH_RADIUS_KM * $c, 2);
    }

    /**
     * Calculate initial bearing from origin to destination (degrees 0–360).
     */
    public function calculateBearing(Airport $origin, Airport $destination): float
    {
        $lat1 = deg2rad((float) $origin->latitude);
        $lon1 = deg2rad((float) $origin->longitude);
        $lat2 = deg2rad((float) $destination->latitude);
        $lon2 = deg2rad((float) $destination->longitude);

        $dLon = $lon2 - $lon1;

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($dLon);

        return round(fmod(rad2deg(atan2($y, $x)) + 360, 360), 2);
    }

    /**
     * Get distance from the cache table, calculating and storing it on first access.
     */
    public function getDistance(Airport $origin, Airport $destination): float
    {
        $record = FlightDistance::where([
            'origin_airport_id'      => $origin->id,
            'destination_airport_id' => $destination->id,
        ])->first();

        if ($record) {
            return $record->distance_km;
        }

        $distanceKm = $this->calculateDistance($origin, $destination);
        $bearingDeg = $this->calculateBearing($origin, $destination);

        FlightDistance::create([
            'origin_airport_id'      => $origin->id,
            'destination_airport_id' => $destination->id,
            'distance_km'            => $distanceKm,
            'bearing_deg'            => $bearingDeg,
        ]);

        return $distanceKm;
    }

    /**
     * Normalise a raw max_speed value from the aircraft table to km/h.
     *
     * The database stores speeds as Mach numbers (e.g. 0.85, 0.90).
     * Values <= 3 are treated as Mach and converted using the speed of sound
     * at FL350. Values > 3 are assumed to already be in km/h.
     */
    public function resolveSpeedKmh(float $rawSpeed): float
    {
        if ($rawSpeed <= 0) {
            return 850.0; // sensible fallback for a mid-range jet
        }

        return $rawSpeed <= 3
            ? round($rawSpeed * self::MACH_TO_KMH, 2)
            : $rawSpeed;
    }

    /**
     * Overhead minutes added on top of pure cruise time to account for
     * taxi, climb, descent, and (for long routes) oceanic/ATC procedures.
     *
     *  < 500 km  → 20 min
     *  500–2000  → 30 min
     *  2000–5000 → 45 min
     *  > 5000    → 60 min  (transatlantic / transpacific)
     */
    public function overheadMinutes(float $distanceKm): int
    {
        if ($distanceKm < 500)  return 20;
        if ($distanceKm < 2000) return 30;
        if ($distanceKm < 5000) return 45;
        return 60;
    }

    /**
     * Estimate flight time as a human-readable string (e.g. "7h 31min").
     * $rawSpeed may be a Mach number or km/h — resolveSpeedKmh() handles both.
     */
    public function estimateFlightTime(float $distanceKm, float $rawSpeed): string
    {
        $speedKmh = $this->resolveSpeedKmh($rawSpeed);

        $totalMinutes = (int) round(($distanceKm / $speedKmh) * 60 + $this->overheadMinutes($distanceKm));
        $hours        = intdiv($totalMinutes, 60);
        $minutes      = $totalMinutes % 60;

        return $hours > 0
            ? "{$hours}h {$minutes}min"
            : "{$minutes}min";
    }

    /**
     * Return flight duration in decimal hours (used for cost calculations).
     * $rawSpeed may be a Mach number or km/h.
     */
    public function flightHours(float $distanceKm, float $rawSpeed): float
    {
        $speedKmh = $this->resolveSpeedKmh($rawSpeed);

        return round($distanceKm / $speedKmh + $this->overheadMinutes($distanceKm) / 60, 4);
    }
}

