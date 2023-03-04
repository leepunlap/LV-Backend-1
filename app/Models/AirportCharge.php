<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AirportCharge extends Model
{
    use HasFactory;

    public function getAvailableAirports($origin, $destination)
    {
        $aircraftModel = new Aircraft();
        $aircraftDetailModel = new AircraftDetail();
        $query = DB::table($this->getTable() . ' as ac');
        $query->leftJoin($aircraftModel->getTable() . ' as a', 'a.id', '=', 'ac.equipment_id');
        $query->leftJoin($aircraftDetailModel->getTable() . ' as ad', 'ad.aircrafts_id', '=', 'a.id');
        $query->selectRaw('ad.*, a.manufacture, a.model, a.type, a.icao, a.mtow');
        // $query->where([
        //     'origin_airport_id' => $origin->id,
        //     'destination_airport_id' => $destination->id,
        // ]);
        $query->orderBy('ac.id', 'ASC');
        $query->groupBy('ad.id');
        return $query->get();
    }

    public function getDetailsByOriginDestinationAircraft($origin, $destination, $aircraft)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'origin_airport_id' => $origin,
            'destination_airport_id' => $destination,
            'equipment_id' => $aircraft,
        ]);
        $builder->orderBy('id', 'DESC');
        return $builder->first();
    }

    public function getCharges($request, $date)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'origin_airport_id' => $request->origin_airport,
            'destination_airport_id' => $request->destination_airport,
            'equipment_id' => $request->aircraft,
        ]);
        $builder->orderBy('id', 'DESC');
        return $builder->first();
    }

    public function originAirport()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Aircraft::class, 'equipment_id');
    }
}
