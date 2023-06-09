<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InatObservation;
use App\Models\InatUser;
use App\Models\InatTaxa;
use App\Models\InatLocation;

class ImportController extends Controller
{
    private $foreign_ids;
    
    public function import()
    {
        return view("welcome");
        ini_set('max_execution_time', 600);
        // $file = fopen(public_path("/data/inat/expanded/test.csv"), "r");
        // $dir_files = scandir(public_path("/data/inat/expanded"));
        // $csv_filenames = array_filter($dir_files, function ($value) {
        //     return pathinfo($value, PATHINFO_EXTENSION) === "csv";
        // });
        // dd($csv_filenames);
        // $csv_filenames = ["2020_01_01_to_2021_06_01.csv", "2021_01_01_to_2022_06_01.csv", "2022_06_01_to_present.csv", "upto_2020_01_01.csv"];
        $csv_filenames = ["2020_01_01_to_2021_06_01.csv", "2021_01_01_to_2022_06_01.csv", "2022_06_01_to_present.csv", "upto_2020_01_01.csv"];
        $batch_size = 1000;
        
        $this->get_foreign_ids();
        // echo "<table>";
        // foreach($this->foreign_ids["observation"] as $value){
        //     echo "<tr><td>$value</td></tr>";
        // }
        // echo "</table>";
        // dd();
        foreach($csv_filenames as $filename){
            $this->get_foreign_ids();
            $file = fopen(public_path("/data/inat/expanded/$filename"), "r");
            $header = fgetcsv($file);
            $batch_counter = 0;
            $batch = [];
            echo "$filename<br>";
            while($row = fgetcsv($file)){
                if(!in_array($row[0], $this->foreign_ids["observation"])){
                    $combined_row = array_combine($header, $row);
                    $batch[] = $combined_row;
                    $batch_counter++;
                    if($batch_counter >= $batch_size){
                        $this->import_batch($batch);
                        $batch_counter = 0;
                        $batch = [];
                    }
                }
            }
            if($batch_counter > 0){
                $this->import_batch($batch);
            }
        }
    }
    public function get_pull_urls_1() {
        $urls = [
            "2012-07-23 to 2012-07-29" => [
                "total_results" => 385,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2012-07-23&d2=2012-07-29&per_page=1&page=1"
            ],
            "2013-07-20 to 2013-07-28" => [
                "total_results" => 126,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2013-07-20&d2=2013-07-28&per_page=1&page=1"
            ],
            "2014-07-19 to 2014-07-27" => [
                "total_results" => 200,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2014-07-19&d2=2014-07-27&per_page=1&page=1"
            ],
            "2015-07-18 to 2015-07-26" => [
                "total_results" => 215,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2015-07-18&d2=2015-07-26&per_page=1&page=1"
            ],
            "2016-07-23 to 2016-07-31" => [
                "total_results" => 267,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2016-07-23&d2=2016-07-31&per_page=1&page=1"
            ],
            "2017-07-22 to 2017-07-30" => [
                "total_results" => 655,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2017-07-22&d2=2017-07-30&per_page=1&page=1"
            ],
            "2018-07-21 to 2018-07-29" => [
                "total_results" => 491,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2018-07-21&d2=2018-07-29&per_page=1&page=1"
            ],
            "2019-07-20 to 2019-07-28" => [
                "total_results" => 1591,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2019-07-20&d2=2019-07-28&per_page=1&page=1"
            ],
            "2020-07-18 to 2020-07-22" => [
                "total_results" => 4169,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2020-07-18&d2=2020-07-22&per_page=1&page=1"
            ],
            "2020-07-23 to 2020-07-26" => [
                "total_results" => 4803,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2020-07-23&d2=2020-07-26&per_page=1&page=1"
            ],
            "2021-07-17 to 2021-07-19" => [
                "total_results" => 11560,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2021-07-17&d2=2021-07-19&per_page=1&page=1"
            ],
            "2021-07-20 to 2021-07-22" => [
                "total_results" => 11560,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2021-07-20&d2=2021-07-22&per_page=1&page=1"
            ],
            "2021-07-23 to 2021-07-25" => [
                "total_results" => 11560,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2021-07-23&d2=2021-07-25&per_page=1&page=1"
            ],
            "2022-07-23 to 2022-07-25" => [
                "total_results" => 11154,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2022-07-23&d2=2022-07-31&per_page=1&page=1"
            ],
            "2022-07-26 to 2022-07-28" => [
                "total_results" => 11154,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2022-07-23&d2=2022-07-31&per_page=1&page=1"
            ],
            "2022-07-29 to 2022-07-31" => [
                "total_results" => 11154,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2022-07-23&d2=2022-07-31&per_page=1&page=1"
            ]
        ];
        $new_urls = [];
        foreach($urls as $k => $v){
            $start = explode(" to ", $k)[0];
            $end = explode(" to ", $k)[1];
            $url = $this->get_pull_url($start, $end, 1, 1);
            $data = json_decode(file_get_contents($url, true));
            $results = $data->total_results;
            $new_urls[] = [
                "start" => $start,
                "end" => $end,
                "total_results" => $results,
                "url" => $url
            ];
        }
        dd($new_urls, json_encode($new_urls));

    }

    //get pull urls
    public function get_pull_urls() {
        $nmw_dates = [
            2012 => [23,29],
            2013 => [20,28],
            2014 => [19,27],
            2015 => [18,26],
            2016 => [23,31],
            2017 => [22,30],
            2018 => [21,29],
            2019 => [20,28],
            2020 =>  [18,26],
            2021 =>  [17,25],
            2022 =>  [23,31],
        ];
        $urls = [];
        foreach($nmw_dates as $year => $dates){
            $start = "$year-07-".$dates[0];
            $end = "$year-07-".$dates[1];
            $url = $this->get_pull_url($start, $end, 1, 1);
            $data = json_decode(file_get_contents($url, true));
            $results = $data->total_results;
            if($results > 3500){

            }
            $urls["$start to $end"] = [
                "total_results" => $data->total_results,
                "url" => $url
            ];
        }
        dd($urls);
    }
    public function pull_data() {
        ini_set('max_execution_time', 300);
        $urls = [
            // [
            //     "start" => "2012-07-23",
            //     "end" => "2012-07-29",
            //     "total_results" => 385,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2012-07-23&d2=2012-07-29&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2013-07-20",
            //     "end" => "2013-07-28",
            //     "total_results" => 126,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2013-07-20&d2=2013-07-28&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2014-07-19",
            //     "end" => "2014-07-27",
            //     "total_results" => 200,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2014-07-19&d2=2014-07-27&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2015-07-18",
            //     "end" => "2015-07-26",
            //     "total_results" => 215,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2015-07-18&d2=2015-07-26&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2016-07-23",
            //     "end" => "2016-07-31",
            //     "total_results" => 267,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2016-07-23&d2=2016-07-31&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2017-07-22",
            //     "end" => "2017-07-30",
            //     "total_results" => 655,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2017-07-22&d2=2017-07-30&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2018-07-21",
            //     "end" => "2018-07-29",
            //     "total_results" => 491,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2018-07-21&d2=2018-07-29&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2019-07-20",
            //     "end" => "2019-07-28",
            //     "total_results" => 1591,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2019-07-20&d2=2019-07-28&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2020-07-18",
            //     "end" => "2020-07-22",
            //     "total_results" => 4169,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2020-07-18&d2=2020-07-22&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2020-07-23",
            //     "end" => "2020-07-26",
            //     "total_results" => 4803,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2020-07-23&d2=2020-07-26&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2021-07-17",
            //     "end" => "2021-07-19",
            //     "total_results" => 4738,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2021-07-17&d2=2021-07-19&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2021-07-20",
            //     "end" => "2021-07-22",
            //     "total_results" => 3176,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2021-07-20&d2=2021-07-22&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2021-07-23",
            //     "end" => "2021-07-25",
            //     "total_results" => 3646,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2021-07-23&d2=2021-07-25&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2022-07-23",
            //     "end" => "2022-07-25",
            //     "total_results" => 3408,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2022-07-23&d2=2022-07-25&per_page=1&page=1",
            // ],
            // [
            //     "start" => "2022-07-26",
            //     "end" => "2022-07-28",
            //     "total_results" => 3552,
            //     "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2022-07-26&d2=2022-07-28&per_page=1&page=1",
            // ],
            [
                "start" => "2022-07-29",
                "end" => "2022-07-31",
                "total_results" => 4194,
                "url" => "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=2022-07-29&d2=2022-07-31&per_page=1&page=1",
            ]
        ];

        $per_page = 200;
        $counts = [];
        ob_start();
        echo "<table border='2'>";
        foreach($urls as $v){
            $start = $v["start"];
            $end = $v["end"];
            $key = "$start to $end";
            $counts[$key] = [
                "added" => 0,
                "skipped" => 0,
                "total" => $v["total_results"],
                "ranges" => [],
            ];
            
            if($v["total_results"] < $per_page){
                $data = $this->pull_inat_data($start, $end, $per_page, 1);
                $count_data = $this->add_observations($data);
                $counts[$key]["added"] += $count_data["added"];
                $counts[$key]["skipped"] += $count_data["skipped"];
                $counts[$key]["ranges"][] = "$start to $end";
            } else {
                for($i = 1; $i <= ($v["total_results"]/$per_page) + 1 ; $i++){
                    $data = $this->pull_inat_data($start, $end, $per_page, $i);
                    $count_data = $this->add_observations($data);
                    $counts[$key]["added"] += $count_data["added"];
                    $counts[$key]["skipped"] += $count_data["skipped"];
                    $counts[$key]["ranges"][] = "$start to $end - page $i";
                }
            }
            if (ob_get_length()) ob_end_flush();
            // echo "$key > ".json_encode($counts[$key])."<br>";
            echo "<tr>";
            echo "<td>" . $counts[$key]["added"] . "</td>";
            echo "<td>" . $counts[$key]["skipped"] . "</td>";
            echo "<td>" . $counts[$key]["total"] . "</td>";
            echo "<td><table border='1'>";
            foreach($counts[$key]["ranges"] as $r){
                echo "<tr><td>$r</td></tr>";
            }
            echo "</table></td>";
            echo "</tr>";
            flush();
        }
        echo "</table>";
    }
    public function pull_data_1() {
        
        $all_data = [];
        for($y = 2010 ; $y < 2023 ; $y++){
            $start = "$y-07-20";
            $end = "$y-07-31";
            $data = $this->pull_inat_data($start, $end, 1, 1);
            if($data["total_results"] > 9500){
                $start = "$y-07-20";
                $end = "$y-07-25";
                $data = $this->pull_inat_data($start, $end, 1, 1);
                $all_data["$start to $end"] = $data;
                $start = "$y-07-25";
                $end = "$y-07-31";
                $data = $this->pull_inat_data($start, $end, 1, 1);
                $all_data["$start to $end"] = $data;
            } else {
                $all_data["$start to $end"] = $data;
            }
        }
        dd($all_data);
    }
    public function get_pull_url($start, $end, $per_page, $page_no) {
        return "https://api.inaturalist.org/v1/observations?order=desc&order_by=created_at&place_id=6681&taxon_id=47157&d1=$start&d2=$end&per_page=$per_page&page=$page_no";
    }

    
    public function pull_inat_data($start, $end, $per_page, $page_no) {
        $url = $this->get_pull_url($start, $end, $per_page, $page_no);
        $data = json_decode(file_get_contents($url), true);
        $data["url"] = $url;
        return $data;
    }

    public function add_observations($data){
        $existing_observation_ids = InatObservation::get()->pluck("id")->toArray();
        $counts = [
            "added" => 0,
            "skipped" => 0,
        ];
        if($data["results"] > 0){
            foreach($data["results"] as $row){
                if(!in_array($row["id"], $existing_observation_ids)){
                    $this->add_observation($row);
                    $counts["added"]++;
                } else {
                    $counts["skipped"]++;
                }
            }
        }
        return $counts;
    }

    public function add_observation($data){
        $existing_ids = [
            "users" => InatUser::get()->pluck("id")->toArray(),
            "taxa" => InatTaxa::get()->pluck("id")->toArray(),
        ];
        
        if(!in_array($data["user"]["id"], $existing_ids["users"])){
            $this->add_user($data["user"]);
        }
        if(!in_array($data["taxon"]["id"], $existing_ids["taxa"])){
            $this->add_taxa($data["taxon"]);
        }
        $coordinates = $data["location"];
        $lat = trim(explode(",",$coordinates)[0]);
        $lon = trim(explode(",",$coordinates)[1]);
        $obv = InatObservation::firstOrCreate([
            "id" => $data["id"],
            "observed_on" => $data["observed_on"],
            "inat_created_at" => $data["created_at"],
            "inat_updated_at" => $data["updated_at"],
            "latitude" => $lat,
            "longitude" => $lon,
            "quality_grade" => $data["quality_grade"],
            "license" => $data["license_code"],
            "image_url" => $data["photos"][0]["url"] ?? null,
            "num_identification_agreements" => $data["num_identification_agreements"],
            "num_identification_disagreements" => $data["num_identification_disagreements"],
            "oauth_application_id" => $data["oauth_application_id"],
            "user_id" => $data["user"]["id"],
            "taxa_id" => $data["taxon"]["id"],
        ]);
    }

    public function add_user($data){
        InatUser::firstOrCreate([
            "id" => $data["id"],
            "login" => $data["login"],
            "name" => $data["name"]
        ]);
    }


    public function add_taxa($data){
        InatTaxa::firstOrCreate([
            "id" => $data["id"],
            "scientific_name" => $data["name"],
            "common_name" => $data["preferred_common_name"] ?? null,
            "rank" => $data["rank"],
            "ancestry" => $data["ancestry"]
        ]);
    }

    public function get_foreign_ids()
    {
        $all_locations = InatLocation::select("id", "latitude", "longitude")->get()->toArray();
        $locations = [];
        foreach($all_locations as $al){
            $locations[$this->get_location_key($al)] = $al["id"];
        }
        $this->foreign_ids = [
            "user" => InatUser::get()->pluck("id")->toArray(),
            "taxa" => InatTaxa::get()->pluck("id")->toArray(),
            "location" => $locations,
            "observation" => InatObservation::get()->pluck("id")->toArray(),
        ];
        // dd($this->foreign_ids);
    }
    
    public function get_location_key($obj)
    {
        return number_format($obj["latitude"], 3)."_".number_format($obj["longitude"], 3);
    }

    public function import_batch($batch)
    {
        $this->get_foreign_ids();
        //import users
        $this->import_users($batch);
        //import taxa
        $this->import_taxa($batch);
        //import locations & observations
        $this->import_locations_and_observations($batch);
    }

    public function import_users($batch)
    {
        $data = [];
        foreach($batch as $row){
            if(!in_array($row["user_id"], $this->foreign_ids["user"]) && !isset($data[$row["user_id"]])){
                $data[$row["user_id"]] = [
                    "id" => $row["user_id"],
                    "login" => $row["user_login"],
                    "name" => $row["user_name"]
                ];
            }
        }
        InatUser::insert($data);
    }
    
    public function import_taxa($batch)
    {
        $data = [];
        foreach($batch as $row){
            if(!in_array($row["taxon_id"], $this->foreign_ids["taxa"]) && !isset($data[$row["taxon_id"]])){
                $data[$row["taxon_id"]] = [
                    "id" => $row["taxon_id"],
                    "scientific_name" => $row["scientific_name"],
                    "common_name" => $row["common_name"],
                    "order" => $row["taxon_order_name"],
                    "superfamily" => $row["taxon_superfamily_name"],
                    "family" => $row["taxon_family_name"],
                    "subfamily" => $row["taxon_subfamily_name"],
                    "tribe" => $row["taxon_tribe_name"],
                    "subtribe" => $row["taxon_subtribe_name"],
                    "genus" => $row["taxon_genus_name"],
                    "species" => $row["taxon_species_name"],
                    "subspecies" => $row["taxon_subspecies_name"],
                    "variety" => $row["taxon_variety_name"],
                    "form" => $row["taxon_form_name"],
                ];
            }
        }
        InatTaxa::insert($data);
    }

    public function import_locations_and_observations($batch)
    {
        $data = [
            "locations" => [],
            "observations" => []
        ];
        foreach($batch as $row){
            if(!in_array($this->get_location_key($row), array_keys($this->foreign_ids["location"]))){
                // dd($row, $this->get_location_key($row), $this->foreign_ids["location"], in_array($this->get_location_key($row), $this->foreign_ids["location"]));
                InatLocation::insert([
                    "place_guess" => $row["place_guess"],
                    "latitude" => $row["latitude"],
                    "longitude" => $row["longitude"],
                ]);
                $this->foreign_ids["location"][$this->get_location_key($row)] = InatLocation::latest()->first()->id;
            }
            if(!in_array($row["id"], $this->foreign_ids["observation"])){
                $data["observations"][$row["id"]] = [
                    "id" => $row["id"],
                    "observed_on" => $row["observed_on"],
                    "inat_created_at" => $row["created_at"],
                    "inat_updated_at" => $row["updated_at"],
                    "quality_grade" => $row["quality_grade"],
                    "license" => $row["license"],
                    "image_url" => $row["image_url"],
                    "num_identification_agreements" => $row["num_identification_agreements"],
                    "num_identification_disagreements" => $row["num_identification_disagreements"],
                    "oauth_application_id" => $row["oauth_application_id"],
                    "user_id" => $row["user_id"],
                    "taxa_id" => $row["taxon_id"],
                    "location_id" => $this->foreign_ids["location"][$this->get_location_key($row)],
                ];
            }
        }
        InatObservation::insert($data["observations"]);
    }

    public function import_1()
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
