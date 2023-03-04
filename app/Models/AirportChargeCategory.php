<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AirportChargeCategory extends Model
{
    use HasFactory;

    public function getChargeCategories($id)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'tbl_airport_billing_sources_id' => $id,
        ]);
        return $builder->get();
    }
}
