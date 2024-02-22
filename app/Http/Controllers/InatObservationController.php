<?php

namespace App\Http\Controllers;

use App\Models\InatObservation;
use App\Models\InatTaxa;
use App\Models\InatUser;
use App\Models\InatLocation;
use Illuminate\Http\Request;

class InatObservationController extends Controller
{
    public function clean()
    {
        ini_set('max_execution_time', 300);
        $grouped_observations = INatObservation::where("nmw", "<>", null)->limit(-1)->get()->groupBy("taxa_id");
        $count = [
            "taxa" => 0,
            "observations" => 0
        ];
        // dd($grouped_observations);
        foreach($grouped_observations as $taxa_id => $observations){
            $taxa = $observations->first()->taxa;
            if(strpos($taxa->ancestry, "47224")){
                $count["taxa"]++;
                $observations->each(function ($observation) use (&$count) {
                    $observation->nmw = null;
                    $observation->save();
                    $count["observations"]++;
                });
            }
        }
        dd($count);

    }

    
    public function generate_data_json()
    {
        $nmw_dates = file_get_contents(public_path("data/nmw_dates.json"));
        $dates_obj = json_decode($nmw_dates, true);

        $dates_obj = array_combine(
            array_column($dates_obj, 'year'),
            array_map(fn ($item) => (object) [ 'start' => $item['start'], 'end' => $item['end'] ], $dates_obj)
        );
        
        $observations = InatObservation::with(["user", "taxa", "location"])->where("nmw", "<>", null)->limit(-1)->get();
        $data = [];
        
        foreach ($observations->groupBy("nmw") as $year => $year_observations) {    
            $data[$year] = $this->getYearStats($year_observations);
        }
        // dd($data);
        file_put_contents(public_path("data/nmw_data_2012_to_2023.json"), json_encode($data));
        return response()->json("JSON Set Successful", 200);

    }

    private function getYearStats($observations)
    {
        return [
            "total_observations" => $observations->count(),
            "total_taxa" => $observations->groupBy("taxa_id")->count(),
            "count_id_levels" => $this->getIDLevelsCount($observations),
            "total_users" => $observations->groupBy("user_id")->count(),
            "count_users" => $this->getUsersCount($observations),
            "locations" => $this->getLocationsCount($observations),
            "dates" => $this->getDatesCount($observations),
        ];
    }

    private function getIDLevelsCount($observations)
    {
        $counts = [];
        foreach($observations->groupBy("taxa_id") as $taxa_observations){
            $taxa = $taxa_observations->first()->taxa;
            if(!isset($counts[$taxa->rank])){
                $counts[$taxa->rank] = [
                    "taxa" => 0,
                    "observations" => 0,
                    "species" => []
                ];
            }
            $counts[$taxa->rank]["taxa"]++;
            $counts[$taxa->rank]["observations"] += $taxa_observations->count();
            $counts[$taxa->rank]["species"][] = [
                "id" => $taxa->id,
                "scientific_name" => $taxa->scientific_name,
                "common_name" => $taxa->common_name,
                "ancestry" => $taxa->ancestry,
                "observations" => $taxa_observations->count()
            ];

        }
        return $counts;

    }

    private function getUsersCount($observations)
    {
        $count = [];
        foreach($observations->groupBy("user_id") as $user_observations){
            $user = $user_observations->first()->user;
            $count[$user->id] = [
                "name" => $user->name,
                "login" => $user->login,
                "observations" => $user_observations->count()
            ];
        }
        return $count;
    }

    private function getLocationsCount($observations)
    {
        $locations_array = [];
        $count = [];
        foreach($observations->groupBy("location_id") as $location_observations){
            $location = $location_observations->first()->location;
            if(!isset($locations_array[$location->region])){
                $locations_array[$location->region] = [];
            }

            if(!isset($locations_array[$location->region][$location->state])){
                $locations_array[$location->region][$location->state] = [];
            } 

            if(!isset($locations_array[$location->region][$location->state][$location->district])){
                $locations_array[$location->region][$location->state][$location->district] = $location_observations->toArray();
            } 
        }
        
        foreach($locations_array as $region => $states){
            $count[$region] = ["region_total" => 0];
            foreach($states as $state => $districts){
                $count[$region][$state] = ["state_total" => 0];
                foreach($districts as $district => $observations){
                    $no_of_observations = count($observations);
                    $count[$region]["region_total"] += $no_of_observations;
                    $count[$region][$state]["state_total"] += $no_of_observations;
                    $count[$region][$state][$district] = $no_of_observations;
                }
            }
        }
        return $count;
    }

    private function getDatesCount($observations)
    {
        $count = [];
        foreach($observations->groupBy("observed_on") as $date_observations){
            $taxa = [];
            $users = [];
            foreach($date_observations as $o){
                if(!in_array($o->taxa_id, $taxa)){
                    $taxa[] = $o->taxa_id;
                }
                if(!in_array($o->user_id, $users)){
                    $users[] = $o->user_id;
                }
            }
            
            $count[$date_observations->first()->observed_on] = [
                "observations"=> $date_observations->count(),
                "taxa" => count($taxa),
                "users" => count($users),
            ];
        }
        return $count;
    }











    public function clean_locations()
    {
        ini_set('max_execution_time', 300);
        $locations = InatLocation::where("region", null)->limit(500)->get();
        $locations_to_fix = $locations->where("region", null);

        $districts = json_decode(file_get_contents(public_path("/data/districts.json")));
        echo "<table border='1'>";

        $null_locations = [];
        $locations_to_skip = [6413,6680,6694,6705,6709,6710,6712,6723,6724,6725,6739,6742,6755,6756,6757,6763,6787,6788,6790,6795,6845,6853,6859,6861,6877,6893,6897,6899,6900,6913,6919,6924,6939,7274,7277,7278,7285,7296,7834,8137,10080,10318];
        
        foreach($locations as $l_no => $l){
            $lat = number_format($l->latitude, 3);
            $lon = number_format($l->longitude, 3);
            echo "<tr>";
            echo "<td>".$l_no."</td>";
            echo "<td>".$l->id."</td>";
            echo "<td>".$l->region."</td>";
            echo "<td>".$l->state."</td>";
            echo "<td>".$l->district."</td>";
            echo "<td>".$l->latitude."</td>";
            echo "<td>".$l->longitude."</td>";
            echo "<td>".$lat."</td>";
            echo "<td>".$lon."</td>";
            if(!$l->district && !in_array($l->id, $locations_to_skip)){
                $district_polygon = $this->getDistrict($lat, $lon, $districts);
                if($district_polygon){
                    $l->region = $district_polygon->properties->region;
                    $l->state = $district_polygon->properties->state;
                    $l->district = $district_polygon->properties->district;
                    // dd($district_polygon, $l);
                    $l->save();
                } else {
                    $null_locations[] = $l->id;
                }
            } 

            echo "<td>".$l->region."</td>";
            echo "<td>".$l->state."</td>";
            echo "<td>".$l->district."</td></tr>";
            
            
        }
        echo "</table>";
        echo implode(",", $null_locations);
        dd();
    }

    private function getDistrict($lat, $lon , $polygons)
    {   $point = [$lon, $lat];
        foreach ($polygons->features as $features) {
            foreach($features->geometry->coordinates as $district_polygons){
                foreach($district_polygons as $polygon){
                    $result = $this->pointInPolygon($point, $polygon);
                    if($result){
                        return $features;
                    }
                }
            }
        }
        return null;

    }

    private function pointInPolygon($point, $polygon) {
        // dd($point, $polygon);
        $x = $point[0];
        $y = $point[1];
        $inside = false;
    
        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];
    
            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) $inside = !$inside;
        }
    
        return $inside;
    }




    public function clean_old()
    {
        $nmw_dates = file_get_contents(public_path("data/nmw_dates.json"));
        $dates_obj = json_decode($nmw_dates, true);

        $dates_obj = array_combine(
            array_column($dates_obj, 'year'),
            array_map(fn ($item) => (object) [ 'start' => $item['start'], 'end' => $item['end'] ], $dates_obj)
        );
        
        // $observations = InatObservation::where("nmw", null)->get();

        // $data = $observations->groupBy([function ($item) use ($dates_obj) {
        //     $date = explode("-", $item->observed_on);
        //     $year = $date[0];
        //     $day = (int) $date[2];
        
        //     if (isset($dates_obj[$year])) {
        //         $start = $dates_obj[$year]->start;
        //         $end = $dates_obj[$year]->end;
                
        //         if ($day >= $start && $day <= $end) {
        //             return 'within';
        //         }
        //     }
        //     return 'outside';
        // }, 
        // fn ($item) => $item->nmw
        // ]);
        // $nmw_unset = $data["within"][""];

        // foreach ($nmw_unset as $observation) {
        //     $observation->nmw =(int) explode("-", $observation->observed_on)[0];
        //     $observation->save();
        // }


        // dd(count($nmw_unset));

        // foreach ($nmw_dates as $nmw_date) {
        //     if(isset($data[$nmw_date->year])){
        //         $year_data = $data[$nmw_date->year];
        //         $start = $nmw_date->start;
        //         $end = $nmw_date->end;
        //         list($withinRange, $outsideRange) = $year_data->sortBy(fn ($value, $key)  => $key)
        //         ->partition(fn ($value, $key) => $key >= $start && $key <= $end);

        //         dd($withinRange->toArray(), $outsideRange);
        //     }
        // }

        // dd($data);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InatObservation $inatObservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InatObservation $inatObservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InatObservation $inatObservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InatObservation $inatObservation)
    {
        //
    }
}
