<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\FeedController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/login', [UserController::class, 'login'])->name('login');
Route::post('/auth/register', [UserController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/logout', [UserController::class, 'logout']);
    Route::get('/news', [NewsController::class, 'retrieveData']);
    Route::get('/guardian/news', [NewsController::class, 'retrieveTheGuardianNews']);
    Route::get('/newyourktimes/news', [NewsController::class, 'retrieveNewYorkTimesData']);

    Route::get('/feed/create', [FeedController::class, 'create']);
    Route::get('/feeds', [FeedController::class, 'getUserFeeds']);
    Route::delete('/feed/{id}', [FeedController::class, 'delete']);
});
