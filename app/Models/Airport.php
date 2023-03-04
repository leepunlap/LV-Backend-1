<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Airport extends Model
{
    use HasFactory;

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getAllAirports($start = 0, $length = 10, $order = null, $search = null, $status = 'Active')
    {
        $query = DB::table(Airport::getTable());
        $result['recordsTotal'] = $query->count();
        $query = DB::table(Airport::getTable() . ' as ap');
        $query->leftJoin('countries as co', 'ap.country_id', '=', 'co.id');
        $query->leftJoin('cities as ci', 'ap.city_id', '=', 'ci.id');
        $query->select('ap.id', 'ap.name', 'ap.icao', 'ap.iata', 'ap.status', 'co.name as country', 'ci.name as city');

        $query->where('ap.status', $status);
        $query->where('ap.isdeleted', '0');

        if ($search) {
            $query->whereRaw("(ap.name LIKE '%" . $search . "%' OR co.name LIKE '%" . $search . "%' OR ci.name LIKE '%" . $search . "%' OR ap.icao LIKE '%" . $search . "%' OR ap.iata LIKE '%" . $search . "%')");
        }
        if ($order) {
            $order_col = ['ap.name', 'ap.icao', 'ap.iata', 'co.name', 'ci.name'];
            $query->orderBy($order_col[$order['column']], $order['dir']);
        }

        // $query->groupBy('ap.id');
        $result['recordsFiltered'] = count($query->get());
        $query->offset($start)->limit($length);
        $result['data'] = $query->get();
        return $result;
    }

    public function getAirportByCode($code)
    {
        $cityModel = new City();
        $countryModel = new Country();
        $airportsParametersModel = new AirportParameter();
        if ($code) {
            $builder = DB::table(Airport::getTable() . ' as a');
            $builder->leftJoin($cityModel->getTable() . ' as ci', 'ci.id', '=', 'a.city_id');
            $builder->leftJoin($countryModel->getTable() . ' as co', 'co.id', '=', 'a.country_id');
            $builder->leftJoin($airportsParametersModel->getTable() . ' as ap', 'ap.airports_id', '=', 'a.id');
            $builder->selectRaw('a.*, ci.name as city, co.iso2 as country_code,  co.name as country, GROUP_CONCAT(ap.name) as handling_costs_names, GROUP_CONCAT(ap.value) as handling_costs_values');
            $builder->where('a.icao', $code);
            $builder->groupBy('a.id');
            $response = $builder->first();
            if ($response) {
                return $response;
            }
            return false;
        }
    }

    public function getAirportByName($airportName)
    {
        $cityModel = new City();
        $countryModel = new Country();
        $airportsParametersModel = new AirportParameter();
        if ($airportName) {
            $airportName = explode(" (", $airportName)[0];
            $builder = DB::table(Airport::getTable() . ' as a');
            $builder->selectRaw('a.*, ci.name as city, co.iso2 as country_code,  co.name as country, GROUP_CONCAT(ap.name) as handling_costs_names, GROUP_CONCAT(ap.value) as handling_costs_values');
            $builder->leftJoin($cityModel->getTable() . ' as ci', 'ci.id', '=', 'a.city_id');
            $builder->leftJoin($countryModel->getTable() . ' as co', 'co.id', '=', 'a.country_id');
            $builder->leftJoin($airportsParametersModel->getTable() . ' as ap', 'ap.airports_id', '=', 'a.id');
            $builder->where('a.name', 'LIKE', '%'.$airportName.'%');
            $builder->groupBy('a.id');
            $response = $builder->first();
            if ($response) {
                return $response;
            }
            return false;
        }
    }


    public function getAirport($id)
    {
        $query = DB::table($this->getTable() . ' as ap');
        $query->leftJoin('countries as co', 'ap.country_id', '=', 'co.id');
        $query->leftJoin('cities as ci', 'ap.city_id', '=', 'ci.id');
        $query->leftJoin('airport_parameters as app', 'ap.id', '=', 'app.airports_id');
        $query->selectRaw('ap.*, co.name as country, co.iso2 as country_code, ci.name as city, GROUP_CONCAT(app.id) as handling_costs_ids, GROUP_CONCAT(app.name) as handling_costs_names, GROUP_CONCAT(app.value) as handling_costs_values');
        $query->groupBy('ap.id');
        $query->where('ap.id', $id);
        return $query->first();
    }
}
