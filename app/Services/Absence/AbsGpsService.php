<?php

namespace App\Services\Absence;

use App\Models\AbsBranch;

class AbsGpsService
{
    public function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isWithinBranchRadius(AbsBranch $branch, float $latitude, float $longitude): bool
    {
        $distance = $this->distanceInMeters(
            (float) $branch->latitude,
            (float) $branch->longitude,
            $latitude,
            $longitude
        );

        return $distance <= (int) $branch->radius_meter;
    }
}
