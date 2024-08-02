<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceRecordController;

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
        Route::get('/', [AttendanceRecordController::class, 'index'])->name('index');
        Route::get('check_in', [AttendanceRecordController::class, 'checkIn'])->name('check_in');
        Route::get('check_out', [AttendanceRecordController::class, 'checkOut'])->name('check_out');
    });
});
