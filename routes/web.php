<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InatObservationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('observations', [HomeController::class, 'observations']);
Route::get('taxa', [HomeController::class, 'taxa']);
Route::get('users', [HomeController::class, 'users']);
Route::get('locations', [HomeController::class, 'locations']);

Route::get('import', [ImportController::class, 'import']);
Route::get('clean', [InatObservationController::class, 'clean']);
Route::get('all_observations_table', [InatObservationController::class, 'all_observations_table']);
Route::get('generate_data_json', [InatObservationController::class, 'generate_data_json']);
// Route::get('pull_data', [ImportController::class, 'pull_data']);

// Route::post('add_data', [ImportController::class, 'add_data']);

Route::get('/nmw_data', function () {
    $path = public_path('data/nmw_data_2012_to_2023.json');

    if (!file_exists($path)) {
        return Response::json(['error' => 'Data file not found'], 404);
    }

    // Read the contents of the JSON file
    $data = file_get_contents($path);

    // Decode the JSON data
    // $jsonData = json_decode($data);

    // Return the JSON response
    return response()->json($data);
});
