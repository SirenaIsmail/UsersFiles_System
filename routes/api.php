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
    ######################## Groups Apis ################################################
    Route::get('groups', [GroupController::class, 'index']);
    Route::get('my-groups', [GroupController::class, 'myGroups']);
    Route::get('membership-groups', [GroupController::class, 'membershipGroups']);
    Route::post('add-group', [GroupController::class, 'store']);
    Route::post('update-group', [GroupController::class, 'update']);
    Route::post('add-members', [GroupController::class, 'addMembers']);
    Route::post('remove-member', [GroupController::class, 'removeMember']);
    Route::post('group-users', [GroupController::class, 'groupUsers']);
    Route::post('delete-group', [GroupController::class, 'deleteGroup']);
    Route::get('logout', [UserController::class, 'logout']);

    ######################################################################################
    Route::post('upload', [FileController::class, 'upload']);
    Route::post('download', [FileController::class, 'download']);
    Route::post('remove-file', [FileController::class, 'removeFile']);
    Route::get('group-files', [FileController::class, 'index']);
    Route::get('search/{filter}', [FileController::class, 'search']);
    Route::post('bulk-checkin', [FileController::class, 'bulkCheckIn']);
    Route::post('checkout', [FileController::class, 'checkOut']);



});
