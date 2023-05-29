<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InatObservation;
use App\Models\InatUser;
use App\Models\InatTaxa;
use App\Models\InatLocation;

class ImportController extends Controller
{
    public function import()
    {
        ini_set('max_execution_time', 300);
        $existing_observation_ids = InatObservation::get()->pluck("id")->toArray();
        $fields = ["id", "observed_on", "user_id", "user_login", "user_name", "created_at", "updated_at", "quality_grade", "license", "image_url", "num_identification_agreements", "num_identification_disagreements", "oauth_application_id", "place_guess", "latitude", "longitude", "scientific_name", "common_name", "taxon_order_name", "taxon_superfamily_name", "taxon_family_name", "taxon_subfamily_name", "taxon_tribe_name", "taxon_subtribe_name", "taxon_genus_name", "taxon_species_name", "taxon_subspecies_name", "taxon_variety_name", "taxon_form_name"];
        $model_fields = [
            "observation" => ["id", "observed_on", "created_at", "updated_at", "quality_grade", "license", "image_url", "num_identification_agreements", "num_identification_disagreements", "oauth_application_id", "user_id"],
            "user" => ["id", "login", "name"],
            "taxa" => ["id", "scientific_name", "common_name", "order", "superfamily", "family", "subfamily", "tribe", "subtribe", "genus", "species", "subspecies", "variety", "form", "rank", "ancestry"],
            "location" => ["id", "place_guess", "latitude", "longitude", "region", "state", "district"]
        ];
        $model_names = ["observation", "user", "taxa", "location"];
        // $dir_files = scandir(public_path("/data/inat/expanded"));
        // $csv_filenames = array_filter($dir_files, function ($value) {
        //     return pathinfo($value, PATHINFO_EXTENSION) === "csv";
        // });
        $csv_filenames = [
            "2021_01_01_to_2022_06_01.csv",
            "2022_06_01_to_present.csv",
            "upto_2020_01_01.csv"
        ];
        $op_interval = 10000;
        $add_counter = [];
        ob_start();
        foreach($csv_filenames as $filename){
            $file = fopen(public_path("/data/inat/expanded/".$filename), "r");
            $header = fgetcsv($file);
            $check_headers[$filename] = $header;
            $add_counter[$filename] = [
                "observation" => [
                    "exists" => 0,
                    "created" => 0
                ],
                "user" => [
                    "exists" => 0,
                    "created" => 0
                ],
                "taxa" => [
                    "exists" => 0,
                    "created" => 0
                ],
                "location" => [
                    "exists" => 0,
                    "created" => 0
                ]
            ];
            
            while($row = fgetcsv($file)){
                $combined_row = array_combine($header, $row);
                if(!in_array($combined_row["id"], $existing_observation_ids)){
                    $model_data = [
                        "user" => [
                            "id" => $combined_row["user_id"],
                            "login" => $combined_row["user_login"],
                            "name" => $combined_row["user_name"]
                        ],
                        "taxa" => [
                            "id" => $combined_row["taxon_id"],
                            "scientific_name" => $combined_row["scientific_name"],
                            "common_name" => $combined_row["common_name"],
                            "order" => $combined_row["taxon_order_name"],
                            "superfamily" => $combined_row["taxon_superfamily_name"],
                            "family" => $combined_row["taxon_family_name"],
                            "subfamily" => $combined_row["taxon_subfamily_name"],
                            "tribe" => $combined_row["taxon_tribe_name"],
                            "subtribe" => $combined_row["taxon_subtribe_name"],
                            "genus" => $combined_row["taxon_genus_name"],
                            "species" => $combined_row["taxon_species_name"],
                            "subspecies" => $combined_row["taxon_subspecies_name"],
                            "variety" => $combined_row["taxon_variety_name"],
                            "form" => $combined_row["taxon_form_name"],
                        ],
                        "location" => [
                            "place_guess" => $combined_row["place_guess"],
                            "latitude" => $combined_row["latitude"],
                            "longitude" => $combined_row["longitude"],
                        ],
                        "observation" => array_combine($model_fields["observation"], array_map(function($value) use ($combined_row){
                            return $combined_row[$value];
                        }, $model_fields["observation"]))
                    ];
                    $models = [
                        "user" => InatUser::firstOrCreate($model_data["user"]),
                        "taxa" => InatTaxa::firstOrCreate($model_data["taxa"]),
                        "location" => InatLocation::firstOrCreate($model_data["location"]),
                    ];
                    $model_data["observation"]["taxa_id"] = $models["taxa"]->id;
                    $model_data["observation"]["location_id"] = $models["location"]->id;
                    // dd($model_data);
                    $models["observation"] = InatObservation::firstOrCreate($model_data["observation"]);
                    // $models["observation"]->save();
                    
                    foreach($model_names as $mn){
                        if($models[$mn]->wasRecentlyCreated){
                            $add_counter[$filename][$mn]["created"]++;
                        }else{
                            $add_counter[$filename][$mn]["exists"]++;
                        }
                    }   
                } else {
                    $add_counter[$filename]["observation"]["exists"]++;
                }
                if(($add_counter[$filename]["observation"]["created"] + $add_counter[$filename]["observation"]["exists"]) % ($op_interval/10) == 0 && ($add_counter[$filename]["observation"]["created"] + $add_counter[$filename]["observation"]["exists"]) > 0){
                    echo $filename." > ".json_encode($add_counter[$filename]["observation"])."\n<br>";
                    ob_flush();
                }
                if($add_counter[$filename]["observation"]["created"] >= $op_interval){
                    echo "<pre>";
                    print_r($add_counter);
                    echo "</pre>";
                    dd($add_counter[$filename]["observation"]["created"], $add_counter[$filename]["observation"]["exists"], $op_interval/10, ($add_counter[$filename]["observation"]["created"] + $add_counter[$filename]["observation"]["exists"]) % ($op_interval/10));
                }
            }
            fclose($file);
        }
        
          
        
        dd("Import");
    }
}
