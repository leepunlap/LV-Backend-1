<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AirportBillingSource extends Model
{
    use HasFactory;

    public function getBillingSources($id, $type)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'tbl_airport_charges_id' => $id,
            'type' => $type,
        ]);
        return $builder->get();
    }
}
