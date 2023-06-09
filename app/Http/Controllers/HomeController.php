<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InatObservation;
use App\Models\InatUser;
use App\Models\InatTaxa;
use App\Models\InatLocation;

class HomeController extends Controller
{
    public function index_1(){
        $d1 = "";
        $d2 = "2019-01-01";
        $params = [
            "place_id" => 6681,
            "taxon_id" => 47157,
            "d1" => "",
            "d2" => "2019-01-01",
            "page" => 1,
            "per_page" => 1,
        ];
        $completed_flag = false;
        $pull_segments = [];
        $total = 0;
        
        //generate manifest
        while(!$completed_flag){
            $params["d1"] = $d1;
            $params["d2"] = $d2;
            $data = $this->inat_pull($params);
            $pull_segments[] = [
                "url" => http_build_query($params),
                "pages" => ceil($data["total_results"] / 200),
                "observations" => $data["total_results"],
            ];
            if($data["total_results"] == 0 || $d2 > date("Y-m-d")){
                $completed_flag = true;
            }
            else{
                $d1 = date("Y-m-d", strtotime($d2 . " - 1 day"));
                $d2 = date("Y-m-d", strtotime($d2 . " + 1 year"));
            }
        }

        foreach($segment as $pull_segments){

        }
    }

    public function index() {
        ini_set('max_execution_time', 600);
        $segments = [
            [
                "params" => [
                    "d1" => "2018-12-30",
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
            for($i = 1; $i <= $segment["pages"] ; $i++){
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
