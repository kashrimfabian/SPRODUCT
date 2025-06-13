<?php

use App\Http\Controllers\AlizetiController;
use App\Http\Controllers\MauzoController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    // Route to fetch all alizeti batches
    Route::get('/alizeti', [AlizetiController::class, 'index']);

    // Route to fetch the price based on alizeti_id and sale_type
    Route::get('/get-price/{alizetiId}/{saleType}', [MauzoController::class, 'getPrice']);

    // Route to store a new mauzo record
    Route::post('/mauzo', [MauzoController::class, 'store']);
});

// Your regular web routes
Route::resource('mauzo', MauzoController::class); // Assuming you have this for your web views