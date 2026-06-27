<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceRecordController;
use \App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
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


Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/login-with-token', [AuthController::class, 'tokenLogin']);


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//
//
//});


Route::group(['middleware' => "auth:sanctum"], function(){

    Route::prefix('attendance')->group(function (){
        Route::post('check_in/{user?}', [AttendanceRecordController::class, 'checkIn']);
        Route::post('check_out/{user?}', [AttendanceRecordController::class, 'checkOut']);
        Route::get('day_wise', [AttendanceRecordController::class, 'dayWise']);
        Route::get('record', [AttendanceRecordController::class, 'monthlyRecord']);
        Route::post('user/note/{record?}/{type?}', [AttendanceRecordController::class, 'userNote']);
//        Route::get('user/record/of_single_date')
    });
    Route::get('dashboard', [\App\Http\Controllers\Api\HomeController::class, 'dashboard']);

    Route::prefix('salary')->group(function(){
        Route::get('/', [\App\Http\Controllers\Api\SalaryController::class, 'index']);
         Route::get('calculator', [App\Http\Controllers\Api\SalaryController::class, 'salaryCalculate']);
         Route::post('/calculate-employee-salary', [App\Http\Controllers\Api\SalaryController::class, 'calculateEmployeeSalary']);
    });

    Route::prefix('break')->group(function(){
        Route::get('/', [\App\Http\Controllers\Api\BreakController::class, 'index']);
        Route::post('start/{employee?}', [\App\Http\Controllers\Api\BreakController::class, 'start']);
        Route::post('stop/{employee?}', [\App\Http\Controllers\Api\BreakController::class, 'stop']);
        Route::get('latest/{employee?}', [\App\Http\Controllers\Api\BreakController::class, 'latestBreak']);
        Route::get('employee', [\App\Http\Controllers\Api\BreakController::class, 'employeeBreak']);

    });

    Route::prefix('leave')->group(function(){
        Route::get('/', [LeaveController::class, 'index']);
        Route::post('request', [LeaveController::class, 'store']);
        Route::patch('status', [LeaveController::class, 'status']);
        Route::get('show/{leave}', [LeaveController::class, 'show']);
        Route::get('check', [LeaveController::class, 'getLeaveByDate']);
    });

    Route::prefix('report')->group(function(){
       Route::get('/', [ReportController::class, 'index']);
    });


    Route::prefix('employee')->controller(\App\Http\Controllers\Api\EmployeeController::class)->group(function(){
        Route::get('/', 'index');
        Route::post('store', 'store');
        Route::post('update/{id}', 'update');
        Route::post('delete', 'delete');
    });

    Route::get('teamLeaders', [\App\Http\Controllers\Api\EmployeeController::class, 'teamLeaders']);

    Route::prefix('office')->controller(App\Http\Controllers\Api\OfficeController::class)->group(function(){
       Route::get('index', 'index');
       Route::post('store', 'store');
       Route::post('update/{id}', 'update');
       Route::post('delete/{id}', 'destroy');
    });


    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
});






