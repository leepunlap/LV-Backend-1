<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EnrouteChargeElement extends Model
{
    use HasFactory;

    public function getChargeElements($id)
    {
        $builder = DB::table($this->getTable());
        $builder->select('*');
        $builder->where([
            'tbl_enroute_countries_id' => $id,
        ]);
        return $builder->get();
    }
}
