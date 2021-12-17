<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Search_controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/Search' , [Search_controller::class,'index']);
Route::get('/submitRequest' , [Search_controller::class,'search_processor']);
Route::get('/visualize' , [Search_controller::class,'visualize_data']);
Route::get('/topicVisualizationData' , [Search_controller::class,'topic_visualize_data']);


