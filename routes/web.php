<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EngineerWorkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ConsultationMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EngineerSpecialtyController;
use App\Http\Controllers\EngineerProfileController;



/*
|--------------------------------------------------------------------------
| الصفحة الرئيسية
|--------------------------------------------------------------------------
*/

Route::get('/', [
    HomeController::class,
    'index',
])->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| استشارات العميل
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:customer,admin',
])->group(function () {

    Route::get('/consultations/create', [
        ConsultationController::class,
        'create',
    ])->name('consultations.create');

    Route::post('/consultations', [
        ConsultationController::class,
        'store',
    ])->name('consultations.store');
});

Route::get('/my-consultations', [
    ConsultationController::class,
    'myConsultations',
])
    ->middleware([
        'auth',
        'role:customer',
    ])
    ->name('consultations.mine');

/*
|--------------------------------------------------------------------------
| عرض جميع الاستشارات
|--------------------------------------------------------------------------
*/

Route::get('/consultations', [
    ConsultationController::class,
    'index',
])
    ->middleware([
        'auth',
        'role:admin,engineer,employee',
    ])
    ->name('consultations.index');

/*
|--------------------------------------------------------------------------
| إدارة الموظفين — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:admin',
])->group(function () {

    Route::get('/employees', [
        EmployeeController::class,
        'index',
    ])->name('employees.index');

    Route::get('/employees/create', [
        EmployeeController::class,
        'create',
    ])->name('employees.create');

    Route::post('/employees', [
        EmployeeController::class,
        'store',
    ])->name('employees.store');
});

/*
|--------------------------------------------------------------------------
| تعيين المهندس — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:admin',
])->group(function () {

    Route::get('/consultations/{consultation}/assign', [
        ConsultationController::class,
        'assignForm',
    ])->name('consultations.assign.form');

    Route::patch('/consultations/{consultation}/assign', [
        ConsultationController::class,
        'assignEngineer',
    ])->name('consultations.assign');
});

/*
|--------------------------------------------------------------------------
| صفحات المهندس
|--------------------------------------------------------------------------
*/

Route::get('/engineer/consultations', [
    ConsultationController::class,
    'engineerConsultations',
])
    ->middleware([
        'auth',
        'role:engineer',
    ])
    ->name('engineer.consultations');

Route::post(
    '/consultations/{consultation}/upload-engineer-file',
    [
        ConsultationController::class,
        'uploadEngineerFile',
    ]
)
    ->middleware([
        'auth',
        'role:engineer,admin',
    ])
    ->name('consultations.engineer-file.upload');

/*
|--------------------------------------------------------------------------
| الدفع
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/consultations/{consultation}/payment', [
        PaymentController::class,
        'create',
    ])->name('payments.create');

    Route::post('/consultations/{consultation}/payment', [
        PaymentController::class,
        'store',
    ])->name('payments.store');
});

/*
|--------------------------------------------------------------------------
| إدارة الدفعات — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:admin',
])->group(function () {

    Route::get('/payments', [
        PaymentController::class,
        'index',
    ])->name('payments.index');

    Route::patch('/payments/{payment}/confirm', [
        PaymentController::class,
        'confirm',
    ])->name('payments.confirm');
});

/*
|--------------------------------------------------------------------------
| الإشعارات
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/notifications', [
        NotificationController::class,
        'index',
    ])->name('notifications.index');

    Route::patch('/notifications/read-all', [
        NotificationController::class,
        'markAllAsRead',
    ])->name('notifications.read-all');

    Route::patch('/notifications/{notification}/read', [
        NotificationController::class,
        'markAsRead',
    ])->name('notifications.read');

    Route::delete('/notifications/{notification}', [
        NotificationController::class,
        'destroy',
    ])->name('notifications.destroy');
});

/*
|--------------------------------------------------------------------------
| مكتبة أعمال المهندسين العامة
|--------------------------------------------------------------------------
*/

Route::get('/engineer-library', [
    EngineerWorkController::class,
    'publicIndex',
])->name('engineer.works.public');

Route::get('/engineer-library/{engineerWork}', [
    EngineerWorkController::class,
    'show',
])->name('engineer.works.show');

/*
|--------------------------------------------------------------------------
| أعمال المهندس
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:engineer',
])->group(function () {

    Route::get('/engineer/my-works', [
        EngineerWorkController::class,
        'myWorks',
    ])->name('engineer.works.mine');

    Route::get('/engineer/my-works/create', [
        EngineerWorkController::class,
        'create',
    ])->name('engineer.works.create');

    Route::post('/engineer/my-works', [
        EngineerWorkController::class,
        'store',
    ])->name('engineer.works.store');

    Route::delete('/engineer/my-works/{engineerWork}', [
        EngineerWorkController::class,
        'destroy',
    ])->name('engineer.works.destroy');
});

Route::middleware([
    'auth',
    'role:admin',
])->group(function () {

    Route::get('/admin/engineer-works', [
        EngineerWorkController::class,
        'index',
    ])->name('admin.engineer-works.index');

    Route::patch('/admin/engineer-works/{engineerWork}/approve', [
        EngineerWorkController::class,
        'approve',
    ])->name('admin.engineer-works.approve');

    Route::patch('/admin/engineer-works/{engineerWork}/reject', [
        EngineerWorkController::class,
        'reject',
    ])->name('admin.engineer-works.reject');
});
Route::get('/engineer-works/{engineerWork}', [
    EngineerWorkController::class,
    'show',
])->name('engineer.works.show');
/*
|--------------------------------------------------------------------------
| محادثات الاستشارات
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/consultations/{consultation}/messages',
        [
            ConsultationMessageController::class,
            'index',
        ]
    )->name('consultations.messages.index');

    Route::post(
        '/consultations/{consultation}/messages',
        [
            ConsultationMessageController::class,
            'store',
        ]
    )->name('consultations.messages.store');
});
Route::middleware(['auth'])->group(function () {

    Route::resource('users', UserController::class)
        ->except(['show']);

});
Route::middleware('auth')->group(function () {

    Route::get(
        '/users',
        [UserController::class, 'index']
    )->name('users.index');

    Route::get(
        '/users/{user}/edit',
        [UserController::class, 'edit']
    )->name('users.edit');

    Route::patch(
        '/users/{user}',
        [UserController::class, 'update']
    )->name('users.update');

    Route::delete(
        '/users/{user}',
        [UserController::class, 'destroy']
    )->name('users.destroy');

});
Route::middleware('auth')->group(function () {

    Route::get(
        '/users',
        [UserController::class, 'index']
    )->name('users.index');

    Route::get(
        '/users/create',
        [UserController::class, 'create']
    )->name('users.create');

    Route::post(
        '/users',
        [UserController::class, 'store']
    )->name('users.store');

    Route::get(
        '/users/{user}/edit',
        [UserController::class, 'edit']
    )->name('users.edit');

    Route::patch(
        '/users/{user}',
        [UserController::class, 'update']
    )->name('users.update');

    Route::delete(
        '/users/{user}',
        [UserController::class, 'destroy']
    )->name('users.destroy');

});
Route::patch(
    '/payments/{payment}/reject',
    [PaymentController::class, 'reject']
)->name('payments.reject');
Route::post(
    '/consultations/{consultation}/upload-engineer-file',
    [ConsultationController::class, 'uploadEngineerFile']
)->name('consultations.upload-engineer-file');
Route::middleware(['auth', 'role:customer,admin'])->group(function () {

    Route::get(
        '/consultations/create/{engineer}',
        [ConsultationController::class, 'create']
    )->name('consultations.create-for-engineer');

    Route::get('/consultations/create', [ConsultationController::class, 'create'])
        ->name('consultations.create');

    Route::post('/consultations', [ConsultationController::class, 'store'])
        ->name('consultations.store');
});

    Route::middleware(['auth', 'role:engineer,admin'])
    ->prefix('engineer')
    ->name('engineer.')
    ->group(function () {

        Route::get(
            '/specialty',
            [EngineerSpecialtyController::class, 'edit']
        )->name('specialty.edit');

        Route::put(
            '/specialty',
            [EngineerSpecialtyController::class, 'update']
        )->name('specialty.update');
    });
    Route::get(
    '/engineers/{user}',
    [EngineerProfileController::class, 'show']
)->name('engineers.show');
require __DIR__.'/auth.php';



