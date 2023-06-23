<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;

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

// Route::get('import', [ImportController::class, 'import']);
// Route::get('pull_data', [ImportController::class, 'pull_data']);

// Route::post('add_data', [ImportController::class, 'add_data']);
