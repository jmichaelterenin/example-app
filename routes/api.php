<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::resource('blocks', App\Http\Controllers\BlockController::class, ['only' => ['index']]);
Route::get('blocks/available/{clinic_id}', [App\Http\Controllers\BlockController::class, 'available']); 
Route::post('blocks/store', [App\Http\Controllers\BlockController::class, 'store']); 