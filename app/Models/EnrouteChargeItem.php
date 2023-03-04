<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EnrouteChargeItem extends Model
{
    use HasFactory;

    public function getChargeItems($id)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'tbl_enroute_charge_elements_id' => $id,
        ]);
        return $builder->get();
    }
}
