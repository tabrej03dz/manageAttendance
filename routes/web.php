<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('home');

Route::middleware('auth')->group(function (){
    Route::prefix('attendance')->name('attendance.')->group(function(){
        Route::get('index/{user?}', [AttendanceRecordController::class, 'index'])->name('index');
        Route::post('check_in', [AttendanceRecordController::class, 'checkIn'])->name('check_in');
        Route::post('check_out', [AttendanceRecordController::class, 'checkOut'])->name('check_out');
        Route::get('form/{form_type}', [AttendanceRecordController::class, 'form'])->name('form');
    });

    Route::prefix('employee')->name('employee.')->group(function (){
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('create', [EmployeeController::class, 'create'])->name('create');
        Route::post('store', [EmployeeController::class, 'store'])->name('store');
        Route::get('edit/{employee}', [EmployeeController::class, 'edit'])->name('edit');
        Route::post('update/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::get('delete/{employee}', [EmployeeController::class, 'delete'])->name('delete');
    });



});
