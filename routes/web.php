<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OffController;
use App\Http\Controllers\PolicyController;
use \App\Http\Controllers\PaymentController;
use App\Http\Controllers\VisitController;
use \App\Http\Controllers\OwnerController;
use \App\Http\Controllers\LunchBreakController;
use \App\Http\Controllers\AdvancePaymentController;
use \App\Http\Controllers\PlanController;
use \App\Http\Controllers\CorrectionNoteController;
use App\Http\Controllers\RequestDemoController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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

Route::controller(\App\Http\Controllers\FrontController::class)->group(function(){
    Route::get('/', 'mainpage')->name('mainpage');
    Route::get('/Blogs', 'blogs')->name('blogs');
    Route::get('/blogDetailsPage', 'blogDetailsPage')->name('blogDetailsPage');
    Route::get('/reqDemo', 'reqDemo')->name('reqDemo');
    Route::get('/thankyou', 'thankyou')->name('thankyoupage');
});

Route::controller(RequestDemoController::class)->group(function(){
    Route::get('request/index', 'index')->name('request.index');
    Route::post('reqest/store', 'store')->name('request.store');
    // Route::get('/', '')->name('blogDetailsPage');
    // Route::get('/', '')->name('reqDemo');
});




//Route::get('change-pass', [HomeController::class, 'changePass'])->name('change-pass');

Auth::routes();

Route::middleware('auth')->group(function (){
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('home');


    Route::prefix('attendance')->name('attendance.')->controller(App\Http\Controllers\AttendanceRecordController::class)->group(function(){
        Route::get('index/{user?}', 'index')->name('index');
        Route::post('check_in/{user?}', 'checkIn')->name('check_in');
        Route::post('check_out/{user?}', 'checkOut')->name('check_out');
        Route::get('form/{form_type}/{user?}', 'form')->name('form');
        Route::get('day-wise', 'dayWise')->name('day-wise');
        Route::post('note/{record}', 'addNote')->name('note');
        Route::post('user/note/{record}/{type}', 'userNote')->name('user.note');
        Route::get('user/note/response/{record}/{type}/{status}', 'userNoteResponse')->name('user.note.response');
        Route::get('reason/form/{type}/{message}/{record}', 'reasonFormLoad')->name('reason.form');
        Route::post('store', 'store')->name('store');
    });

    Route::get('setting/instruction', function (){
        return view('dashboard.settingInstruction');
    })->name('setting.instruction');

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
        Route::get('permission/{user}', [EmployeeController::class, 'permission'])->name('permission');
        Route::get('permissionRemove/{permission}/{user}', [EmployeeController::class, 'permissionRemove'])->name('permissionRemove');
    });

    Route::prefix('owner')->name('owner.')->group(function(){
       Route::get('/', [OwnerController::class, 'index'])->name('index');
       Route::get('create', [OwnerController::class, 'create'])->name('create');
       Route::post('store', [OwnerController::class, 'store'])->name('store');
       Route::get('edit/{owner}', [OwnerController::class, 'edit'])->name('edit');
       Route::post('update/{owner}', [OwnerController::class, 'update'])->name('update');
       Route::post('delete/{owner}', [OwnerController::class, 'delete'])->name('delete');
       Route::get('status/{owner}', [OwnerController::class, 'status'])->name('status');
       Route::get('plan/{owner}', [PlanController::class, 'ownerPlan'])->name('plan');
    });

    Route::prefix('plan')->name('plan.')->group(function (){
        Route::get('create/{owner}', [PlanController::class, 'create'])->name('create');
        Route::post('store/{owner}', [PlanController::class, 'store'])->name('store');
        Route::get('edit/{plan}', [PlanController::class, 'edit'])->name('edit');
        Route::post('update/{plan}', [PlanController::class, 'update'])->name('update');
        Route::get('status/{plan}', [PlanController::class, 'status'])->name('status');
        Route::post('delete/{plan}', [PlanController::class, 'delete'])->name('delete');
    });

    Route::prefix('break')->name('break.')->group(function(){
        Route::get('/', [LunchBreakController::class, 'index'])->name('index');
        Route::post('start/{employee?}', [LunchBreakController::class, 'start'])->name('start');
        Route::post('stop/{break}/{employee?}', [LunchBreakController::class, 'stop'])->name('stop');
        Route::get('form/{employee?}/{break?}', [LunchBreakController::class, 'form'])->name('form');
    });

    Route::post('profile/update/{user}', [HomeController::class, 'updateProfile'])->name('profile.update');

    // DEMO TESTING MAIL


    Route::prefix('office')->name('office.')->group(function(){
       Route::get('/', [OfficeController::class, 'index'])->name('index');
       Route::get('create', [OfficeController::class, 'create'])->name('create');
       Route::get('edit/{office}', [OfficeController::class, 'edit'])->name('edit');
       Route::post('store', [OfficeController::class, 'store'])->name('store');
       Route::post('update/{office}', [OfficeController::class, 'update'])->name('update');
       Route::get('delete/{office}', [OfficeController::class, 'delete'])->name('delete');
       Route::get('status/{office}', [OfficeController::class, 'status'])->name('status');
       Route::get('detail/{office}', [OfficeController::class, 'detail'])->name('detail');
    });

    Route::prefix('payment')->name('payment.')->group(function(){
        Route::get('/', [PaymentController::class, 'index'])->name('index');
       Route::get('paymentForm/{payment}', [PaymentController::class, 'paymentForm'])->name('paymentForm');
       Route::post('add/{payment}', [PaymentController::class, 'addPayment'])->name('add');
       Route::get('advance/{office}', [PaymentController::class, 'advance'])->name('advance');
    });



    Route::get('/userprofile/{user}', [HomeController::class, 'profile'])->name('userprofile');
    Route::post('userPassword/{user}', [HomeController::class, 'changePassword'])->name('userPassword');

    Route::prefix('leave')->name('leave.')->group(function(){
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('create', [LeaveController::class, 'create'])->name('create');
        Route::post('store', [LeaveController::class, 'store'])->name('store');
        Route::get('status/{leave}/{status}/{type?}', [LeaveController::class, 'status'])->name('status');
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

    Route::prefix('visit')->name('visit.')->group(function(){
       Route::get('/', [VisitController::class, 'index'])->name('index');
       Route::get('create', [VisitController::class, 'create'])->name('create');
       Route::post('store', [VisitController::class, 'store'])->name('store');
       Route::get('edit/{visit}', [VisitController::class, 'edit'])->name('edit');
       Route::post('update/{visit}', [VisitController::class, 'update'])->name('update');
       Route::get('delete/{visit}', [VisitController::class, 'delete'])->name('delete');
       Route::get('status/{visit}/{status}', [VisitController::class, 'status'])->name('status');
       Route::get('paid/{visit}', [VisitController::class, 'pay'])->name('paid');
    });

    Route::prefix('salary')->name('salary.')->group(function(){
       Route::get('/', [\App\Http\Controllers\SalaryController::class, 'index'])->name('index');
       Route::get('status/{salary}', [\App\Http\Controllers\SalaryController::class, 'status'])->name('status');
       Route::post('pay/{salary}', [\App\Http\Controllers\SalaryController::class, 'paidAmount'])->name('pay');
    });

    Route::prefix('recycle')->name('recycle.')->group(function(){
        Route::get('/', [\App\Http\Controllers\RecycleController::class, 'index'])->name('index');
        Route::get('user/delete/{user}', [\App\Http\Controllers\RecycleController::class, 'userDelete'])->name('user.delete');
        Route::get('user/restore/{user}', [\App\Http\Controllers\RecycleController::class, 'userRestore'])->name('user.restore');
    });

    Route::get('manual/entry/form', [AttendanceRecordController::class, 'manualEntryForm'])->name('manual.entry.form');

    Route::prefix('permission')->name('permission.')->group(function(){
       Route::get('/', [\App\Http\Controllers\PermissionController::class, 'index'])->name('index');
       Route::get('create', [\App\Http\Controllers\PermissionController::class, 'create'])->name('create');
       Route::post('give', [\App\Http\Controllers\PermissionController::class, 'givePermission'])->name('give');
       Route::post('store', [\App\Http\Controllers\PermissionController::class, 'store'])->name('store');
    });

    Route::prefix('advance')->name('advance.')->group(function(){
        Route::get('/', [AdvancePaymentController::class, 'index'])->name('index');
        Route::get('create/{user?}', [AdvancePaymentController::class, 'create'])->name('create');
        Route::post('store', [AdvancePaymentController::class, 'store'])->name('store');
    });


    Route::prefix('role')->name('role.')->group(function(){
        Route::get('/', [\App\Http\Controllers\RoleController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\RoleController::class, 'create'])->name('create');
        Route::post('store', [\App\Http\Controllers\RoleController::class, 'store'])->name('store');
        Route::get('delete/{role}', [\App\Http\Controllers\RoleController::class, 'delete'])->name('delete');
        Route::get('permission/{role}', [\App\Http\Controllers\RoleController::class, 'permission'])->name('permission');
        Route::get('permissionRemove/{permission}/{role}', [\App\Http\Controllers\RoleController::class, 'permissionRemove'])->name('permissionRemove');
    });

    Route::prefix('note')->name('note.')->group(function(){
       Route::get('/', [\App\Http\Controllers\NoteController::class, 'index'])->name('index');
       Route::get('create', [\App\Http\Controllers\NoteController::class, 'create'])->name('create');
       Route::post('store', [\App\Http\Controllers\NoteController::class, 'store'])->name('store');
        Route::get('status/{note}', [\App\Http\Controllers\NoteController::class, 'status'])->name('status');
        Route::get('delete/{note}', [\App\Http\Controllers\NoteController::class, 'delete'])->name('delete');
    });

    Route::prefix('correctionNote')->name('correctionNote.')->group(function(){
        Route::get('create/{record}/{note?}', [CorrectionNoteController::class, 'create'])->name('create');
        Route::post('store/{record}', [CorrectionNoteController::class, 'store'])->name('store');
    });


    // hello how are you mera naam hai jolly
    // web.php

// OfficeController.php
    Route::get('note/get-office-role-and-employee/{officeId}', [\App\Http\Controllers\NoteController::class, 'getOfficeRoleAndEmployee']);


    Route::get('/foo', function () {
        $exitCode = Artisan::call('storage:link');
        if ($exitCode === 0) {
            return 'Success';
        } else {
            return 'Failed'; // You can customize this message as needed
        }
    });
});
