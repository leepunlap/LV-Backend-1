<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\FlightDistance;
use App\Services\FlightDistanceService;
use Illuminate\Console\Command;

class CalculateFlightDistances extends Command
{
    protected $signature = 'flights:calculate-distances
                            {--force : Recalculate and overwrite existing records}
                            {--origin= : Limit to a single origin IATA code}
                            {--destination= : Limit to a single destination IATA code}';

    protected $description = 'Pre-calculate great-circle distances between all active airport pairs and store them in flight_distances';

    public function handle(FlightDistanceService $service): int
    {
        $airports = Airport::where('status', 'Active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->when($this->option('origin'), fn($q) => $q->where('iata', $this->option('origin')))
            ->get();

        $destinations = Airport::where('status', 'Active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->when($this->option('destination'), fn($q) => $q->where('iata', $this->option('destination')))
            ->get();

        $force      = $this->option('force');
        $totalPairs = count($airports) * count($destinations);
        $processed  = 0;
        $skipped    = 0;
        $created    = 0;
        $updated    = 0;

        $this->info("Calculating distances for up to {$totalPairs} airport pairs...");
        $bar = $this->output->createProgressBar($totalPairs);
        $bar->start();

        foreach ($airports as $origin) {
            foreach ($destinations as $destination) {
                $bar->advance();

                // Skip same airport
                if ($origin->id === $destination->id) {
                    $skipped++;
                    continue;
                }

                $existing = FlightDistance::where([
                    'origin_airport_id'      => $origin->id,
                    'destination_airport_id' => $destination->id,
                ])->first();

                if ($existing && !$force) {
                    $skipped++;
                    continue;
                }

                $distanceKm = $service->calculateDistance($origin, $destination);
                $bearingDeg = $service->calculateBearing($origin, $destination);

                if ($existing) {
                    $existing->update([
                        'distance_km' => $distanceKm,
                        'bearing_deg' => $bearingDeg,
                    ]);
                    $updated++;
                } else {
                    FlightDistance::create([
                        'origin_airport_id'      => $origin->id,
                        'destination_airport_id' => $destination->id,
                        'distance_km'            => $distanceKm,
                        'bearing_deg'            => $bearingDeg,
                    ]);
                    $created++;
                }

                $processed++;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Created', 'Updated', 'Skipped'],
            [[$created, $updated, $skipped]]
        );
        $this->info('Done.');

        return self::SUCCESS;
    }
}
