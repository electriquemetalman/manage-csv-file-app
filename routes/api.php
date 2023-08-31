<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\competitionController;
use App\Http\Controllers\visitorController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::post('/compareFile/{id}', [visitorController::class, 'compareFile']);
Route::post('/createCompetition', [competitionController::class, 'store']);
Route::get('/ranking/{id}', [visitorController::class, 'ranking']);
Route::apiResource('visitor', visitorController::class);
Route::get('/index', [competitionController::class, 'index']);
Route::get('/show/{id}', [competitionController::class, 'show']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource('competition', competitionController::class);
    });
    Route::delete('/logout', [AuthController::class, 'logout']);
});
