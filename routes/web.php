<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OffController;
use App\Http\Controllers\PolicyController;

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

Route::middleware('auth')->group(function (){
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('home');


    Route::prefix('attendance')->name('attendance.')->group(function(){
        Route::get('index/{user?}', [AttendanceRecordController::class, 'index'])->name('index');
        Route::post('check_in/{user?}', [AttendanceRecordController::class, 'checkIn'])->name('check_in');
        Route::post('check_out/{user?}', [AttendanceRecordController::class, 'checkOut'])->name('check_out');
        Route::get('form/{form_type}/{user?}', [AttendanceRecordController::class, 'form'])->name('form');
        Route::get('day-wise', [AttendanceRecordController::class, 'dayWise'])->name('day-wise');
        Route::post('note/{record}', [AttendanceRecordController::class, 'addNote'])->name('note');
        Route::post('user/note/{record}/{type}', [AttendanceRecordController::class, 'userNote'])->name('user.note');
        Route::get('user/note/response/{record}/{type}/{status}', [AttendanceRecordController::class, 'userNoteResponse'])->name('user.note.response');
    });

    Route::prefix('employee')->name('employee.')->group(function (){
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('create', [EmployeeController::class, 'create'])->name('create');
        Route::post('store', [EmployeeController::class, 'store'])->name('store');
        Route::get('edit/{employee}', [EmployeeController::class, 'edit'])->name('edit');
        Route::post('update/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::post('delete/{employee}', [EmployeeController::class, 'delete'])->name('delete');
        Route::get('profile/{user}', [HomeController::class, 'profile'])->name('profile');
        Route::get('attendance', [EmployeeController::class, 'employeeAttendance'])->name('attendance');
        Route::get('status/{employee}', [EmployeeController::class, 'status'])->name('status');
    });

    Route::post('profile/update/{user}', [HomeController::class, 'updateProfile'])->name('profile.update');

    Route::prefix('office')->name('office.')->group(function(){
       Route::get('/', [OfficeController::class, 'index'])->name('index');
       Route::get('create', [OfficeController::class, 'create'])->name('create');
       Route::get('edit/{office}', [OfficeController::class, 'edit'])->name('edit');
       Route::post('store', [OfficeController::class, 'store'])->name('store');
       Route::post('update/{office}', [OfficeController::class, 'update'])->name('update');
       Route::get('delete/{office}', [OfficeController::class, 'delete'])->name('delete');
       Route::get('status/{office}', [OfficeController::class, 'status'])->name('status');
    });



    Route::get('/userprofile/{user}', [HomeController::class, 'profile'])->name('userprofile');
    Route::post('userPassword/{user}', [HomeController::class, 'changePassword'])->name('userPassword');

    Route::prefix('leave')->name('leave.')->group(function(){
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('create', [LeaveController::class, 'create'])->name('create');
        Route::post('store', [LeaveController::class, 'store'])->name('store');
        Route::get('status/{leave}/{status}', [LeaveController::class, 'status'])->name('status');
        Route::get('show/{leave}', [LeaveController::class, 'show'])->name('show');
    });

    Route::prefix('off')->name('off.')->group(function(){
       Route::get('/', [OffController::class, 'index'])->name('index');
       Route::get('create', [OffController::class, 'create'])->name('create');
       Route::post('store', [OffController::class, 'store'])->name('store');
       Route::get('edit/{off}', [OffController::class, 'edit'])->name('edit');
       Route::post('update/{off}', [OffController::class, 'update'])->name('update');
       Route::post('delete/{off}', [OffController::class, 'delete'])->name('delete');
    });

    Route::prefix('policy')->name('policy.')->group(function(){
        Route::get('/', [PolicyController::class, 'index'])->name('index');
        Route::get('create', [PolicyController::class, 'create'])->name('create');
        Route::post('store', [PolicyController::class, 'store'])->name('store');
        Route::get('edit/{policy}', [PolicyController::class, 'edit'])->name('edit');
        Route::post('update/{policy}', [PolicyController::class, 'update'])->name('update');
        Route::post('delete/{policy}', [PolicyController::class, 'delete'])->name('delete');
        Route::get('read/{policy?}', [PolicyController::class, 'read'])->name('read');
        Route::get('accept/{policy}', [PolicyController::class, 'accept'])->name('accept');
    });

    Route::prefix('reports')->name('reports.')->group(function(){
       Route::get('/', [\App\Http\Controllers\FinalReportController::class, 'index'])->name('index');
    });

    Route::prefix('info')->name('info.')->group(function(){
       Route::get('create', [\App\Http\Controllers\UserAdditionalInformationController::class, 'create'])->name('create');
       Route::post('store', [\App\Http\Controllers\UserAdditionalInformationController::class, 'store'])->name('store');
    });

});
