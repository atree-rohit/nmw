<?php

namespace App\Http\Controllers;

use DateTime;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;


use App\Models\InatObservation;
use App\Models\InatUser;
use App\Models\InatTaxa;
use App\Models\InatLocation;

class HomeController extends Controller
{
    public function index(){
        $data = InatObservation::get()->groupBy("observed_on");
        dd($data);
    }
    public function index_3()
    {
        dd(1);
        $d1 = "";
        $d2 = "2011-11-01";
        $params = [
            "place_id" => 6681,
            "taxon_id" => 47157,
            "page" => 1,
            "per_page" => 1,
        ];
        $completed_flag = false;
        $total_results = 0;
        $segments = [];        

        while($completed_flag == false){
            
            do{
                $params["d1"] = $d1;
                $params["d2"] = $d2;
                $data = $this->inatPull($params);
                $total_results = +$data["total_results"];
                if($data["total_results"] < 5000){
                    $d2 = date("Y-m-d", strtotime($d2 . " + 1 months"));
                } else if($data["total_results"] > 10000){
                    $check = $d2;
                    $d2 = date("Y-m-d", strtotime($d2 . " - 1 months"));
                    if($check == $d2){
                        dd($check, $d2);
                    }
                }
            } while ($data["total_results"] < 5000 || $data["total_results"] > 10000) ;
            $segments[] = [
                "params" => [
                    "d1" => $d1,
                    "d2" => $d2,
                ],
                "pages" => ceil($data["total_results"] / 200),
                "total_results" => $data["total_results"],
            ];
            if($data["total_results"] == 0 || $d2 > date("Y-m-d") || $total_results >= 50000){
                $completed_flag = true;
            }
            else{
                $d1 = $d2;
                $d2 = date("Y-m-d", strtotime($d2 . " + 6 months"));
            }
            
        }
        dd($segments);
    }

    public function inatPull($params)
    {
        $response = Http::get('https://api.inaturalist.org/v1/observations', $params);
        return $response->json();
    }

    public function index_2(){
        $d1 = "";
        $d2 = "2011-11-01";
        $params = [
            "place_id" => 6681,
            "taxon_id" => 47157,
            "page" => 1,
            "per_page" => 1,
        ];
        $completed_flag = false;
        $pull_segments = [];
        $total = 0;
        
        ob_start();
        echo "<table border='1'><tr><th>Start Date</th><th>End Date</th><th>Pages</th><th>Observations</th></tr>";
        while(!$completed_flag){
            $increase_flag = false;
            $params["d1"] = $d1;
            $params["d2"] = $d2;
            $data = $this->inat_pull($params);
            if($data["total_results"] > 10000){
                $increase_flag = true;
                $dates = [
                    "start" => new DateTime($d1),
                    "end" => new DateTime($d2),
                ];
                $interval = $dates["start"]->diff($dates["end"]);
                if($interval->days > 31){
                    $d2 = date("Y-m-d", strtotime($d2 . " - 1 month"));
                } else {
                    $half_interval = ceil($interval->days / 2);
                    $d2 = date("Y-m-d", strtotime($d2 . " - $half_interval day"));
                }
                echo "<tr style='background:pink;'><td>$d1</td><td>$d2</td><td colspan='2'>Over 10,000</td></tr>";
                ob_flush();
                continue;
            } 
            $segment = [
                "params" => [
                    "d1" => $d1,
                    "d2" => $d2,
                ],
                "pages" => ceil($data["total_results"] / 200),
                "observations" => $data["total_results"],
            ];
            $total += $data["total_results"];
            echo "<tr>";
            echo "<td>" . $segment["params"]["d1"] . "</td>";
            echo "<td>" . $segment["params"]["d2"] . "</td>";
            echo "<td>" . $segment["pages"] . "</td>";
            echo "<td>" . $segment["observations"] . "</td>";
            
            echo "</tr>";
            ob_flush();
            $pull_segments[] = $segment;
            if($data["total_results"] == 0 || $d2 > date("Y-m-d") || $total > 50000){
                echo "<tr><td>".$data["total_results"]."</td><td>".date("Y-m-d")."</td><td>".$total."</td></tr>";
                $completed_flag = true;
            }
            else{
                $d1 = $d2;
                $d2 = date("Y-m-d", strtotime($d2 . " + 6 months"));
            }
        }
        
        echo "</table>";
        dd($pull_segments);
    }

    public function index_1() {
        ini_set('max_execution_time', 600);
        $segments = [
            [
                "params" => [
                    "d1" => "",
                    "d2" => "2019-01-01",
                ],
                "pages" => 376,
                "total_results" => 75171,
            ],[
                "params" => [
                    "d1" => "2018-12-31",
                    "d2" => "2020-01-01"
                ],
                "pages" => 180,
                "total_results" => 35827,
            ],[
                "params" => [
                    "d1" => "2019-12-31",
                    "d2" => "2021-01-01"
                ],
                "pages" => 451,
                "total_results" => 90110,
            ],[
                "params" => [
                    "d1" => "2020-12-31",
                    "d2" => "2022-01-01"
                ],
                "pages" => 563,
                "total_results" => 112496,
            ],[
                "params" => [
                    "d1" => "2021-12-31",
                    "d2" => "2023-01-01"
                ],
                "pages" => 608,
                "total_results" => 121468,
            ],[
                "params" => [
                    "d1" => "2022-12-31",
                    "d2" => "2024-01-01"
                ],
                "pages" => 181,
                "total_results" => 36003,
            ]
        ];
        foreach($segments as $segment){
            $params = [
                "place_id" => 6681,
                "taxon_id" => 47157,
                "d1" => $segment["params"]["d1"],
                "d2" => $segment["params"]["d2"],
                "page" => 1,
                "per_page" => 200
            ];
            for($i = 45; $i <= $segment["pages"] ; $i++){
                $params["page"] = $i;
                $data = $this->inat_pull($params);
                $this->store_results($data["results"]);
            }
        }
    }

    public function inat_pull($params){
        $base_url = "https://api.inaturalist.org/v1/observations/";
        if(is_string($params)){
            $url = $base_url . "?" . $params;
        } else if(is_array($params)){
            $url = $base_url . "?" . http_build_query($params);
        } else {
            dd($params);
        }
        return json_decode(file_get_contents($url), true);
    }

    public function store_results($results) {
        $store_data = [
            "users" => [],
            "taxa" => [],
            "observations" => []
        ];
        foreach($results as $observation){
            $store_data["users"] = $this->addToArrayOfArrays($store_data["users"], [
                "id" => $observation["user"]["id"],
                "login" => $observation["user"]["login"],
                "name" => $observation["user"]["name"],
                "inat_created_at" => $observation["user"]["created_at"],
                "observation_count" => $observation["user"]["observations_count"],
            ]);
            
            $store_data["taxa"] = $this->addToArrayOfArrays($store_data["taxa"],[
                "id" => $observation["taxon"]["id"],
                "scientific_name" => $observation["taxon"]["name"],
                "common_name" => $observation["taxon"]["preferred_common_name"] ?? null,
                "rank" => $observation["taxon"]["rank"],
                "ancestry" => $observation["taxon"]["ancestry"]
            ]);

            $store_data["observations"] = $this->addToArrayOfArrays($store_data["observations"], [
                "id" => $observation["id"],
                "observed_on" => $observation["observed_on"],
                "inat_created_at" => $observation["created_at"],
                "inat_updated_at" => $observation["updated_at"],
                "quality_grade" => $observation["quality_grade"],
                "license" => $observation["license_code"],
                "image_url" => $observation["photos"][0]["url"] ?? null,
                "num_identification_agreements" => $observation["num_identification_agreements"],
                "num_identification_disagreements" => $observation["num_identification_disagreements"],
                "oauth_application_id" => $observation["oauth_application_id"],
                "user_id" => $observation["user"]["id"],
                "taxa_id" => $observation["taxon"]["id"],
            ]);
        }
        InatUser::insertOrIgnore($store_data["users"]);
        InatTaxa::insertOrIgnore($store_data["taxa"]);
        InatObservation::insertOrIgnore($store_data["observations"]);

        // dd($store_data, $results[0]);
    }

    public function addToArrayOfArrays($arrayOfArrays, $newArray) {
        $existingIds = array_column($arrayOfArrays, 'id');
        if(!in_array($newArray['id'], $existingIds)){
            $arrayOfArrays[] = $newArray;
        }
        return $arrayOfArrays;
    }
}
