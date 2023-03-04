<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EnrouteCountry extends Model
{
    use HasFactory;

    public function getEnRouteCountries($id)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'tbl_enroutes_id' => $id
        ]);
        return $builder->get();
    }
}
