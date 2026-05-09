<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Hod\DashboardController as HodDashboard;
use App\Http\Controllers\Accounts\DashboardController as AccountsDashboard;
use App\Http\Controllers\Writer\DashboardController as WriterDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ChangePasswordController;
use App\Http\Controllers\Student\DocumentController;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('hod')) return redirect()->route('hod.dashboard');
        if ($user->hasRole('accounts')) return redirect()->route('accounts.dashboard');
        if ($user->hasRole('writer')) return redirect()->route('writer.dashboard');
        if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
        if ($user->hasRole('student')) return redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Role-based Dashboards
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/students',           [\App\Http\Controllers\Admin\StudentController::class, 'index'])->name('admin.students.index');
        Route::get('/admin/students/datatable', [\App\Http\Controllers\Admin\StudentController::class, 'datatable'])->name('admin.students.datatable');
        Route::get('/admin/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'show'])->name('admin.students.show');

        // Admin — Master Data (view + delete)
        $amc = \App\Http\Controllers\Admin\MasterDataController::class;
        Route::get('/admin/master',                     [$amc, 'index'])->name('admin.master.index');
        Route::get('/admin/master/departments/dt',      [$amc, 'departmentsDatatable'])->name('admin.master.departments.datatable');
        Route::get('/admin/master/courses/dt',          [$amc, 'coursesDatatable'])->name('admin.master.courses.datatable');
        Route::get('/admin/master/batches/dt',          [$amc, 'batchesDatatable'])->name('admin.master.batches.datatable');
        Route::get('/admin/master/subjects/dt',         [$amc, 'subjectsDatatable'])->name('admin.master.subjects.datatable');
        Route::get('/admin/master/courses',             [$amc, 'coursesView'])->name('admin.master.courses');
        Route::get('/admin/master/batches',             [$amc, 'batchesView'])->name('admin.master.batches');
        Route::delete('/admin/master/departments/{department}', [$amc, 'destroyDepartment'])->name('admin.master.departments.destroy');
        Route::delete('/admin/master/courses/{course}',         [$amc, 'destroyCourse'])->name('admin.master.courses.destroy');
        Route::delete('/admin/master/batches/{batch}',          [$amc, 'destroyBatch'])->name('admin.master.batches.destroy');
        Route::delete('/admin/master/subjects/{subject}',       [$amc, 'destroySubject'])->name('admin.master.subjects.destroy');

        // Admin — Staff Management
        $sc = \App\Http\Controllers\Admin\StaffController::class;
        Route::get('/admin/staff',              [$sc, 'index'])->name('admin.staff.index');
        Route::get('/admin/staff/create',       [$sc, 'create'])->name('admin.staff.create');
        Route::post('/admin/staff',             [$sc, 'store'])->name('admin.staff.store');
        Route::get('/admin/staff/{staff}/edit', [$sc, 'edit'])->name('admin.staff.edit');
        Route::put('/admin/staff/{staff}',      [$sc, 'update'])->name('admin.staff.update');
        Route::delete('/admin/staff/{staff}',   [$sc, 'destroy'])->name('admin.staff.destroy');
    });

    // HOD — Department read-only views
    Route::middleware(['role:hod'])->group(function () {
        Route::get('/hod/dashboard', [HodDashboard::class, 'index'])->name('hod.dashboard');
        $hvc = \App\Http\Controllers\Hod\DepartmentViewController::class;
        Route::get('/hod/teachers',          [$hvc, 'teachersIndex'])->name('hod.teachers.index');
        Route::get('/hod/teachers/datatable',[$hvc, 'teachersDatatable'])->name('hod.teachers.datatable');
        Route::get('/hod/students',          [$hvc, 'studentsIndex'])->name('hod.students.index');
        Route::get('/hod/students/datatable',[$hvc, 'studentsDatatable'])->name('hod.students.datatable');
    });

    // Accounts — Fee Management
    Route::middleware(['role:accounts'])->prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/dashboard', [AccountsDashboard::class, 'index'])->name('dashboard');
        
        $afc = \App\Http\Controllers\Accounts\FeeManagementController::class;
        Route::get('/transactions',       [$afc, 'transactions'])->name('fees.transactions');
        Route::get('/dues',               [$afc, 'dues'])->name('fees.dues');
        Route::get('/defaulters',         [$afc, 'defaulters'])->name('fees.defaulters');
        Route::get('/reports',            [$afc, 'reports'])->name('fees.reports');
        Route::get('/structures',         [$afc, 'structures'])->name('fees.structures');
        
        // Manual Fine Management
        Route::get('/fines',              [$afc, 'finesIndex'])->name('fines.index');
        Route::post('/fines/apply',       [$afc, 'applyManualFine'])->name('fines.apply');
    });
    Route::middleware(['role:writer'])->get('/writer/dashboard', [WriterDashboard::class, 'index'])->name('writer.dashboard');
    Route::middleware(['role:teacher'])->get('/teacher/dashboard', [TeacherDashboard::class, 'index'])->name('teacher.dashboard');
    
    
    // Student — with force-password-change gate
    Route::middleware(['role:student', 'force_password_change'])->group(function () {
        Route::get('/student/dashboard', [StudentDashboard::class, 'index'])->name('student.dashboard');

        // Student Document Centre
        Route::prefix('student/documents')->name('student.documents.')->group(function () {
            Route::get('/',        [DocumentController::class, 'index'])->name('index');
            Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
            Route::delete('/delete', [DocumentController::class, 'delete'])->name('delete');
        });

        // Student Profile (AI Face Setup)
        Route::prefix('student/profile')->name('student.profile.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\StudentProfileController::class, 'index'])->name('index');
            Route::post('/update', [\App\Http\Controllers\Student\StudentProfileController::class, 'update'])->name('update');
        });

        // Student Fees
        Route::prefix('student/fees')->name('student.fees.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\FeeController::class, 'index'])->name('index');
            Route::post('/pay-simulate', [\App\Http\Controllers\Student\FeeController::class, 'simulatePayment'])->name('pay.simulate');
        });
    });

    // Student — Change Password (excluded from force_password_change to avoid redirect loop)
    Route::middleware(['role:student'])->prefix('student')->name('student.password.')->group(function () {
        Route::get('/change-password',  [ChangePasswordController::class, 'show'])->name('change');
        Route::put('/change-password',  [ChangePasswordController::class, 'update'])->name('update');
    });

    // Writer — Student Management
    Route::middleware(['role:writer'])->prefix('writer/students')->name('writer.students.')->group(function () {
        Route::get('/',                  [\App\Http\Controllers\Writer\StudentController::class, 'index'])->name('index');
        Route::get('/datatable',         [\App\Http\Controllers\Writer\StudentController::class, 'datatable'])->name('datatable');
        Route::get('/create',            [\App\Http\Controllers\Writer\StudentController::class, 'create'])->name('create');
        Route::post('/store',            [\App\Http\Controllers\Writer\StudentController::class, 'store'])->name('store');
        Route::get('/{student}/edit',    [\App\Http\Controllers\Writer\StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}/update',  [\App\Http\Controllers\Writer\StudentController::class, 'update'])->name('update');
        Route::get('/pending-documents', [\App\Http\Controllers\Writer\StudentController::class, 'pendingDocuments'])->name('pending-documents');
    });

    // Writer — Academic Promotion
    Route::middleware(['role:writer'])->prefix('writer/promotion')->name('writer.promotion.')->group(function () {
        $pc = \App\Http\Controllers\Writer\PromotionController::class;
        Route::get('/', [$pc, 'index'])->name('index');
        Route::get('/get-students', [$pc, 'getStudents'])->name('students');
        Route::post('/promote', [$pc, 'promote'])->name('promote');
    });

    // Writer — Master Data (Departments, Courses, Batches, Subjects)
    Route::middleware(['role:writer'])->prefix('writer/master')->name('writer.master.')->group(function () {
        $mc = \App\Http\Controllers\Writer\MasterDataController::class;

        // Departments
        Route::get('/departments',              [$mc, 'departmentsIndex'])->name('departments.index');
        Route::get('/departments/datatable',    [$mc, 'departmentsDatatable'])->name('departments.datatable');
        Route::post('/departments',             [$mc, 'departmentsStore'])->name('departments.store');
        Route::get('/departments/{department}', [$mc, 'departmentsShow'])->name('departments.show');
        Route::put('/departments/{department}', [$mc, 'departmentsUpdate'])->name('departments.update');
        Route::delete('/departments/{department}', [$mc, 'departmentsDestroy'])->name('departments.destroy');

        // Courses
        Route::get('/courses',            [$mc, 'coursesIndex'])->name('courses.index');
        Route::get('/courses/datatable',  [$mc, 'coursesDatatable'])->name('courses.datatable');
        Route::post('/courses',           [$mc, 'coursesStore'])->name('courses.store');
        Route::get('/courses/{course}',   [$mc, 'coursesShow'])->name('courses.show');
        Route::put('/courses/{course}',   [$mc, 'coursesUpdate'])->name('courses.update');
        Route::delete('/courses/{course}',[$mc, 'coursesDestroy'])->name('courses.destroy');

        // Batches
        Route::get('/batches',            [$mc, 'batchesIndex'])->name('batches.index');
        Route::get('/batches/datatable',  [$mc, 'batchesDatatable'])->name('batches.datatable');
        Route::post('/batches',           [$mc, 'batchesStore'])->name('batches.store');
        Route::get('/batches/{batch}',    [$mc, 'batchesShow'])->name('batches.show');
        Route::put('/batches/{batch}',    [$mc, 'batchesUpdate'])->name('batches.update');
        Route::delete('/batches/{batch}', [$mc, 'batchesDestroy'])->name('batches.destroy');

        // Subjects
        Route::get('/subjects',             [$mc, 'subjectsIndex'])->name('subjects.index');
        Route::get('/subjects/datatable',   [$mc, 'subjectsDatatable'])->name('subjects.datatable');
        Route::post('/subjects',            [$mc, 'subjectsStore'])->name('subjects.store');
        Route::get('/subjects/{subject}',   [$mc, 'subjectsShow'])->name('subjects.show');
        Route::put('/subjects/{subject}',   [$mc, 'subjectsUpdate'])->name('subjects.update');
        Route::delete('/subjects/{subject}',[$mc, 'subjectsDestroy'])->name('subjects.destroy');

        // Fee Structures
        Route::get('/fees',                           [$mc, 'feesIndex'])->name('fees.index');
        Route::get('/fees/datatable',                 [$mc, 'feesDatatable'])->name('fees.datatable');
        Route::post('/fees',                          [$mc, 'feesStore'])->name('fees.store');
        Route::get('/fees/{feeStructure}',            [$mc, 'feesShow'])->name('fees.show');
        Route::put('/fees/{feeStructure}',            [$mc, 'feesUpdate'])->name('fees.update');
        Route::delete('/fees/{feeStructure}',         [$mc, 'feesDestroy'])->name('fees.destroy');
    });

    // Writer — Teacher Management
    Route::middleware(['role:writer'])->prefix('writer/teachers')->name('writer.teachers.')->group(function () {
        $tc = \App\Http\Controllers\Writer\TeacherController::class;
        Route::get('/',           [$tc, 'index'])->name('index');
        Route::get('/datatable',  [$tc, 'datatable'])->name('datatable');
        Route::post('/',          [$tc, 'store'])->name('store');
        Route::get('/{teacher}',  [$tc, 'show'])->name('show');
        Route::put('/{teacher}',  [$tc, 'update'])->name('update');
        Route::post('/{teacher}/toggle-hod', [$tc, 'toggleHod'])->name('toggle-hod');
        Route::delete('/{teacher}', [$tc, 'destroy'])->name('destroy');
    });

    // Student — Attendance
    Route::middleware(['role:student', 'force_password_change'])->prefix('student/attendance')->name('student.attendance.')->group(function () {
        Route::get('/', [App\Http\Controllers\AttendanceController::class, 'index'])->name('index');
        Route::get('/summary', [App\Http\Controllers\AttendanceController::class, 'summary'])->name('summary');
        Route::post('/mark', [App\Http\Controllers\AttendanceController::class, 'markAttendance'])->name('mark');
    });

    // Student — Ultrasonic Attendance (Beta)
    Route::middleware(['role:student', 'force_password_change'])->prefix('student/ultrasonic')->name('student.ultrasonic.')->group(function () {
        Route::get('/', [App\Http\Controllers\UltrasonicAttendanceController::class, 'studentIndex'])->name('index');
        Route::post('/mark', [App\Http\Controllers\UltrasonicAttendanceController::class, 'markAttendance'])->name('mark');
    });

    Route::middleware(['role:teacher'])->prefix('teacher/attendance')->name('teacher.attendance.')->group(function () {
        Route::get('/', [App\Http\Controllers\AttendanceController::class, 'teacherIndex'])->name('index');
        Route::post('/start', [App\Http\Controllers\AttendanceController::class, 'startSession'])->name('start');
        Route::post('/close', [App\Http\Controllers\AttendanceController::class, 'closeSession'])->name('close');
        Route::get('/session/{session}/students', [App\Http\Controllers\AttendanceController::class, 'getSessionStudents'])->name('session-students');
        Route::get('/session/{session}/biometrics', [App\Http\Controllers\AttendanceController::class, 'getSessionBiometrics'])->name('session-biometrics');
        Route::post('/session/{session}/mark-bulk', [App\Http\Controllers\AttendanceController::class, 'markBulkAttendance'])->name('mark-bulk');
        Route::post('/session/{session}/mark-manual', [App\Http\Controllers\AttendanceController::class, 'markManualOverride'])->name('mark-manual');
    });

    // Teacher — Ultrasonic Attendance (Beta)
    Route::middleware(['role:teacher'])->prefix('teacher/ultrasonic')->name('teacher.ultrasonic.')->group(function () {
        Route::get('/', [App\Http\Controllers\UltrasonicAttendanceController::class, 'teacherIndex'])->name('index');
        Route::post('/start', [App\Http\Controllers\UltrasonicAttendanceController::class, 'startSession'])->name('start');
    });

    // Teacher — AI Attendance Risk Prediction Module
    Route::middleware(['role:teacher'])->prefix('teacher/risk')->name('teacher.risk.')->group(function () {
        Route::get('/',                         [App\Http\Controllers\Teacher\AttendanceRiskController::class, 'index'])->name('index');
        Route::post('/predict/{student}',       [App\Http\Controllers\Teacher\AttendanceRiskController::class, 'predict'])->name('predict');
        Route::post('/predict-all',             [App\Http\Controllers\Teacher\AttendanceRiskController::class, 'predictAll'])->name('predict-all');
    });

    // HOD — AI Attendance Risk Prediction Module
    Route::middleware(['role:hod'])->prefix('hod/risk')->name('hod.risk.')->group(function () {
        Route::get('/',                         [App\Http\Controllers\Hod\AttendanceRiskController::class, 'index'])->name('index');
        Route::post('/predict/{student}',       [App\Http\Controllers\Hod\AttendanceRiskController::class, 'predict'])->name('predict');
        Route::post('/predict-department',      [App\Http\Controllers\Hod\AttendanceRiskController::class, 'predictDepartment'])->name('predict-department');
    });
});

require __DIR__.'/auth.php';
