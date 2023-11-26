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
    Route::get('download', [FileController::class, 'download']);
    Route::get('create-file', [FileController::class, 'create']);
    Route::post('remove-file', [FileController::class, 'removeFile']);
    Route::get('group-files', [FileController::class, 'index']);
    Route::get('search/{filter}', [FileController::class, 'search']);
    Route::post('bulk-checkin', [FileController::class, 'bulkCheckIn']);
    Route::post('checkout', [FileController::class, 'checkOut']);



});
