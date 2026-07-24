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
use App\Http\Controllers\EngineerApplicationController;
use App\Http\Middleware\EnsureActiveEngineerMembership;
use App\Http\Controllers\EngineerReviewController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReviewController;


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
| إعدادات الحساب
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // صفحة البيانات الشخصية
    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    // صفحة تغيير كلمة المرور
    Route::get('/profile/password', [
        ProfileController::class,
        'editPassword',
    ])->name('profile.password.edit');

    // صفحة حذف الحساب
    Route::get('/profile/delete', [
        ProfileController::class,
        'deleteAccount',
    ])->name('profile.delete');

    // تحديث البيانات الشخصية
    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    // حذف الحساب نهائيًا
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


Route::get('/my-consultations', [
    ConsultationController::class,
    'myConsultations',
])
    ->middleware([
        'auth',
        'role:customer,engineer,admin',
    ])
    ->name('consultations.mine');

    /*
|--------------------------------------------------------------------------
| عرض جميع الاستشارات — المدير والموظف
|--------------------------------------------------------------------------
*/

Route::get('/consultations', [
    ConsultationController::class,
    'index',
])
    ->middleware([
        'auth',
        'role:admin,employee',
    ])
    ->name('consultations.index');
/*
|--------------------------------------------------------------------------
| عرض جميع الاستشارات
|--------------------------------------------------------------------------
*/



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
        EnsureActiveEngineerMembership::class,
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
    EnsureActiveEngineerMembership::class,
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

Route::patch(
    '/payments/{payment}/reject',
    [PaymentController::class, 'reject']
)->name('payments.reject');

    Route::middleware([
    'auth',
    'role:engineer,admin',
    EnsureActiveEngineerMembership::class,
])
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
Route::middleware([
    'auth',
    'role:customer,engineer',
])->group(function () {

    Route::get(
        '/become-engineer',
        [EngineerApplicationController::class, 'create']
    )->name('engineer-applications.create');

    Route::post(
        '/become-engineer',
        [EngineerApplicationController::class, 'store']
    )->name('engineer-applications.store');
});

Route::middleware([
    'auth',
    'role:customer,engineer,admin',
])->group(function () {

    Route::get('/consultations/create', [
        ConsultationController::class,
        'create',
    ])->name('consultations.create');

    Route::get(
        '/consultations/create/{engineer}',
        [ConsultationController::class, 'createForEngineer']
    )->name('consultations.create-for-engineer');

    Route::post('/consultations', [
        ConsultationController::class,
        'store',
    ])->name('consultations.store');
});
Route::middleware([
    'auth',
    'role:admin',
])
    ->prefix('admin')
    ->group(function () {

        Route::get(
            '/engineer-applications',
            [EngineerApplicationController::class, 'index']
        )->name('engineer-applications.index');

        Route::patch(
            '/engineer-applications/{engineerApplication}/approve',
            [EngineerApplicationController::class, 'approve']
        )->name('engineer-applications.approve');

        Route::patch(
            '/engineer-applications/{engineerApplication}/reject',
            [EngineerApplicationController::class, 'reject']
        )->name('engineer-applications.reject');
    });
    Route::middleware([
    'auth',
    'role:customer,engineer',
])->group(function () {

    Route::get(
        '/consultations/{consultation}/review',
        [EngineerReviewController::class, 'create']
    )->name('engineer-reviews.create');

    Route::post(
        '/consultations/{consultation}/review',
        [EngineerReviewController::class, 'store']
    )->name('engineer-reviews.store');
});
Route::post(
    '/consultations/{consultation}/upload-engineer-file',
    [
        ConsultationController::class,
        'uploadEngineerFile',
    ]
)
    ->middleware([
        'auth',
        'role:admin,engineer',
    ])
    ->name('consultations.upload-engineer-file');
    /*
|--------------------------------------------------------------------------
| الفواتير
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/invoices/{invoice}',
        [
            InvoiceController::class,
            'show',
        ]
    )->name('invoices.show');

    Route::get(
        '/invoices/{invoice}/download',
        [
            InvoiceController::class,
            'download',
        ]
    )->name('invoices.download');

});
/*
|--------------------------------------------------------------------------
| معلومات الدفع العامة
|--------------------------------------------------------------------------
*/

Route::view(
    '/payment-information',
    'payment-information'
)->name('payment-information');
Route::delete(
    '/admin/engineer-works/{engineerWork}',
    [EngineerWorkController::class, 'destroy']
)->name('admin.engineer-works.destroy');
Route::middleware('auth')->group(function () {
    Route::get(
        '/consultations/{consultation}/review',
        [ReviewController::class, 'create']
    )->name('reviews.create');

    Route::post(
        '/consultations/{consultation}/review',
        [ReviewController::class, 'store']
    )->name('reviews.store');

    Route::get(
        '/admin/reviews',
        [ReviewController::class, 'index']
    )->name('reviews.index');

    Route::patch(
        '/admin/reviews/{review}/approve',
        [ReviewController::class, 'approve']
    )->name('reviews.approve');

    Route::patch(
        '/admin/reviews/{review}/reject',
        [ReviewController::class, 'reject']
    )->name('reviews.reject');

    Route::patch(
        '/admin/reviews/{review}/featured',
        [ReviewController::class, 'toggleFeatured']
    )->name('reviews.featured');

    Route::delete(
        '/admin/reviews/{review}',
        [ReviewController::class, 'destroy']
    )->name('reviews.destroy');
});

require __DIR__.'/auth.php';
