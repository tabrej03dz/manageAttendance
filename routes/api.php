<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceRecordController;
use \App\Http\Controllers\Api\AuthController;


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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/login-with-token', [AuthController::class, 'tokenLogin']);


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//
//
//});


Route::group(['middleware' => "auth:sanctum"], function(){
    Route::prefix('attendance')->name('attendance.')->group(function (){
        Route::post('check_in/{user?}', [AttendanceRecordController::class, 'checkIn'])->name('check_in');
        Route::post('check_out/{user?}', [AttendanceRecordController::class, 'checkOut'])->name('check_out');
        Route::get('day_wise', [AttendanceRecordController::class, 'dayWise'])->name('day_wise');
        Route::get('record', [AttendanceRecordController::class, 'monthlyRecord'])->name('record');
    });

    Route::prefix('salary')->name('salary.')->group(function(){
        Route::get('/', [\App\Http\Controllers\Api\SalaryController::class, 'index'])->name('index');
    });



    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});






