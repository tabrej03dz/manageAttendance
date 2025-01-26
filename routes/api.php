<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceRecordController;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\LeaveController;
use \App\Http\Controllers\Api\ReportController;


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
        Route::post('user/note/{record?}/{type?}', [AttendanceRecordController::class, 'userNote'])->name('userNote');
//        Route::get('user/record/of_single_date')
    });
    Route::get('dashboard', [\App\Http\Controllers\Api\HomeController::class, 'dashboard'])->name('dashboard');

    Route::prefix('salary')->name('salary.')->group(function(){
        Route::get('/', [\App\Http\Controllers\Api\SalaryController::class, 'index'])->name('index');
    });

    Route::prefix('break')->name('break.')->group(function(){
        Route::get('/', [\App\Http\Controllers\Api\BreakController::class, 'index'])->name('index');
        Route::post('start/{employee?}', [\App\Http\Controllers\Api\BreakController::class, 'start'])->name('start');
        Route::post('stop/{employee?}', [\App\Http\Controllers\Api\BreakController::class, 'stop'])->name('stop');
        Route::get('latest/{employee?}', [\App\Http\Controllers\Api\BreakController::class, 'latestBreak'])->name('latest');
    });

    Route::prefix('leave')->name('leave.')->group(function(){
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::post('request', [LeaveController::class, 'store'])->name('request');
        Route::patch('status', [LeaveController::class, 'status'])->name('status');
        Route::get('show/{leave}', [LeaveController::class, 'show'])->name('show');
        Route::get('check', [LeaveController::class, 'getLeaveByDate'])->name('check');
    });

    Route::prefix('report')->name('prefix.')->group(function(){
       Route::get('/', [ReportController::class, 'index'])->name('index');
    });

    Route::prefix('employee')->name('employee.')->controller(\App\Http\Controllers\Api\EmployeeController::class)->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});






