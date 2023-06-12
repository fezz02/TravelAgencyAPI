<?php

use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'as' => 'v1.'], function() {
    Route::group(['prefix' => 'travels', 'as' => 'travels.'], function() {
        Route::get('/', [TravelController::class, 'index'])->name('index');
        Route::post('/store', [TravelController::class, 'store'])->name('store');
        Route::put('/update/{travel}', [TravelController::class, 'update'])->name('update');

        Route::group(['as' => 'tours.'], function() {
            Route::get('{travel}/tours', [TourController::class, 'index'])->name('index');
            Route::post('{travel}/tours/store', [TourController::class, 'store'])->name('store');
        });
    });
});
