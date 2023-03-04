<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enroute extends Model
{
    use HasFactory;

    public function getCharges($request, $date)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'origin_airport_id' => $request->origin_airport,
            'destination_airport_id' => $request->destination_airport,
        ]);
        $builder->orderBy('id', 'DESC');
        return $builder->first();
    }

    public function getDetailsByOriginDestination($origin, $destination)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'origin_airport_id' => $origin,
            'destination_airport_id' => $destination,
        ]);
        $builder->orderBy('id', 'DESC');
        return $builder->first();
    }
}
