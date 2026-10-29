<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MachineryController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/login', [DemoAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [DemoAuthController::class, 'login'])->name('login.submit');
Route::post('/demo/login', [DemoAuthController::class, 'demoLogin'])->middleware('demo')->name('demo.login');

// Public & in-app interactive documentation
Route::get('/docs', [DocsController::class, 'index'])->name('docs');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [DemoAuthController::class, 'logout'])->name('logout');
    Route::post('/demo/switch-role', [DemoAuthController::class, 'switchRole'])->middleware('demo')->name('demo.switch-role');

    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Projects & Sites
    Route::resource('projects', ProjectController::class);

    // 3. Daily Site Reports
    Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
    Route::get('/daily-reports/create', [DailyReportController::class, 'create'])->name('daily-reports.create');
    Route::post('/daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');
    Route::get('/daily-reports/{dailyReport}', [DailyReportController::class, 'show'])->name('daily-reports.show');
    Route::post('/daily-reports/{dailyReport}/review', [DailyReportController::class, 'review'])->name('daily-reports.review');

    // 4. Workforce & Employees
    Route::resource('employees', EmployeeController::class);

    // 5. Attendance & Overtime
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/bulk', [AttendanceController::class, 'bulkForm'])->name('attendance.bulk');
    Route::post('/attendance/bulk', [AttendanceController::class, 'storeBulk'])->name('attendance.bulk.store');
    Route::post('/attendance/{attendance}/approve-overtime', [AttendanceController::class, 'approveOvertime'])->name('attendance.approve-overtime');
    Route::post('/attendance/approve-all-overtime', [AttendanceController::class, 'approveAllOvertime'])->name('attendance.approve-all-overtime');

    // 6. Payroll & Payslips (Restricted to Admin & HR/Payroll)
    Route::middleware('role:admin,hr_payroll')->group(function () {
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::put('/payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
        Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::post('/payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.mark-paid');
        Route::get('/payroll/{payroll}/payslip', [PayrollController::class, 'payslip'])->name('payroll.payslip');
    });

    // 7. Machinery & Fleet Register
    Route::resource('machineries', MachineryController::class);

    // 8. Compliance & Expiry Center
    Route::get('/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
    Route::post('/compliance/{document}/flag', [ComplianceController::class, 'flagDocument'])->name('compliance.flag');
    Route::post('/compliance/check-expiries', [ComplianceController::class, 'checkExpiries'])->name('compliance.check-expiries');

    // 9. Reports & Exports Hub
    Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');
    Route::get('/exports/projects/csv', [ExportController::class, 'exportProjectsCsv'])->name('exports.projects.csv');
    Route::get('/exports/attendance/csv', [ExportController::class, 'exportAttendanceCsv'])->name('exports.attendance.csv');
    Route::get('/exports/payroll/csv', [ExportController::class, 'exportPayrollCsv'])->name('exports.payroll.csv');
    Route::get('/exports/machinery/csv', [ExportController::class, 'exportMachineryCsv'])->name('exports.machinery.csv');
    Route::get('/exports/projects/print', [ExportController::class, 'printProjects'])->name('exports.projects.print');
    Route::get('/exports/attendance/print', [ExportController::class, 'printAttendance'])->name('exports.attendance.print');
    Route::get('/exports/payroll/print', [ExportController::class, 'printPayroll'])->name('exports.payroll.print');
    Route::get('/exports/machinery/print', [ExportController::class, 'printMachinery'])->name('exports.machinery.print');

    // 10. Activity & Audit Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});
