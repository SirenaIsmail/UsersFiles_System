<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
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


Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('groups', [GroupController::class, 'index']);
    Route::post('add-group', [GroupController::class, 'store']);
    Route::post('add-members', [GroupController::class, 'addMembers']);
    Route::post('upload', [FileController::class, 'upload']);

});
