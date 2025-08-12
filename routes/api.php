<?php

use Illuminate\Http\Request;
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

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::resource('/posts', \App\Http\Controllers\Api\PostController::class)
    ->except(['create', 'edit']); 
    Route::resource('/datapusats', \App\Http\Controllers\Api\DataPusatController::class);
    Route::resource('/barangmasuks', \App\Http\Controllers\Api\BarangMasuksController::class);
    Route::resource('/barangkeluars', \App\Http\Controllers\Api\BarangKeluarsController::class);
    Route::resource('/users', \App\Http\Controllers\Api\UsersController::class);
});
