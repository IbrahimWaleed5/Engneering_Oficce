<?php

use App\Http\Controllers\Auth\PublicEmailVerificationController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationMessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ConversationFileController;
use App\Http\Controllers\ConversationMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EngineerApplicationController;
use App\Http\Controllers\EngineerProfileController;
use App\Http\Controllers\EngineerReviewController;
use App\Http\Controllers\EngineerSpecialtyController;
use App\Http\Controllers\EngineerWorkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureActiveEngineerMembership;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\AdminOfficeController;
use App\Http\Controllers\OfficeApplicationController;
use App\Http\Controllers\AdminSupportController;
use App\Http\Controllers\SupportMessageController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\OfficeSubscriptionController;
use App\Http\Controllers\EngineeringOfficeController;
use App\Http\Controllers\OfficeMembershipApplicationController;
use App\Http\Controllers\OfficeApplicationFileController;
use App\Http\Controllers\OfficeMemberController;
use App\Http\Controllers\ConsultationOfficeAssignmentController;
use App\Http\Controllers\SupportBotController;
use App\Http\Controllers\Employee\SupportTicketController as EmployeeSupportTicketController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\ModerationAppealController;
use App\Http\Controllers\Admin\ModerationAppealController as AdminModerationAppealController;
/*
|--------------------------------------------------------------------------
| الصفحة الرئيسية والصفحات العامة
|--------------------------------------------------------------------------
*/

Route::get('/', [
    HomeController::class,
    'index',
])->name('home');
Route::view(
    '/payment-information',
    'payment-information'
)->name('payment-information');

Route::get('/engineer-library', [
    EngineerWorkController::class,
    'publicIndex',
])->name('engineer.works.public');

Route::get('/engineer-library/{engineerWork}', [
    EngineerWorkController::class,
    'show',
])->name('engineer.works.show');

Route::get(
    '/engineers/{user}',
    [
        EngineerProfileController::class,
        'show',
    ]
)
    ->whereNumber('user')
    ->name('engineers.show');
/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [
    DashboardController::class,
    'index',
])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| إعدادات الحساب
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::patch(
        '/support/{supportTicket}/escalate',
        [
            SupportTicketController::class,
            'escalateToAdmin',
        ]
    )->name('support.escalate');
    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::get('/profile/password', [
        ProfileController::class,
        'editPassword',
    ])->name('profile.password.edit');

    Route::get('/profile/delete', [
        ProfileController::class,
        'deleteAccount',
    ])->name('profile.delete');

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
| استشارات العميل وإنشاء الاستشارات
|--------------------------------------------------------------------------
*/

Route::get('/my-consultations', [
    ConsultationController::class,
    'myConsultations',
])
    ->middleware([
        'auth',
        'verified',
        'role:customer,engineer,admin',
    ])
    ->name('consultations.mine');

Route::middleware([
    'auth',
    'verified',
    'role:customer,engineer,admin',
])->group(function () {
    Route::get('/consultations/create', [
        ConsultationController::class,
        'create',
    ])->name('consultations.create');

    Route::get('/consultations/create/{engineer}', [
        ConsultationController::class,
        'createForEngineer',
    ])->name('consultations.create-for-engineer');

    Route::post('/consultations', [
        ConsultationController::class,
        'store',
    ])->name('consultations.store');
});

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
        'verified',
        'role:admin,employee',
    ])
    ->name('consultations.index');

/*
|--------------------------------------------------------------------------
| إدارة الموظفين — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
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
    'verified',
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

Route::middleware([
    'auth',
    'verified',
    'role:engineer',
    EnsureActiveEngineerMembership::class,
])->group(function () {
    Route::get('/engineer/consultations', [
        ConsultationController::class,
        'engineerConsultations',
    ])->name('engineer.consultations');

    Route::post('/consultations/{consultation}/upload-engineer-file', [
        ConsultationController::class,
        'uploadEngineerFile',
    ])->name('consultations.upload-engineer-file');

    Route::prefix('engineer')
        ->name('engineer.')
        ->group(function () {
            Route::get('/specialty', [
                EngineerSpecialtyController::class,
                'edit',
            ])->name('specialty.edit');

            Route::put('/specialty', [
                EngineerSpecialtyController::class,
                'update',
            ])->name('specialty.update');
        });
});

/*
|--------------------------------------------------------------------------
| الدفع — العميل أو المدير
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:customer,engineer,admin',
])->group(function () {
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
    'verified',
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

    Route::patch('/payments/{payment}/reject', [
        PaymentController::class,
        'reject',
    ])->name('payments.reject');
});

/*
|--------------------------------------------------------------------------
| الإشعارات
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
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
| أعمال المهندس
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
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

/*
|--------------------------------------------------------------------------
| إدارة أعمال المهندسين — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
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

    Route::delete('/admin/engineer-works/{engineerWork}', [
        EngineerWorkController::class,
        'destroy',
    ])->name('admin.engineer-works.destroy');
});

/*
|--------------------------------------------------------------------------
| محادثات الاستشارات
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:customer,engineer,admin',
])->group(function () {
    Route::get('/consultations/{consultation}/messages', [
        ConsultationMessageController::class,
        'index',
    ])->name('consultations.messages.index');

    Route::post('/consultations/{consultation}/messages', [
        ConsultationMessageController::class,
        'store',
    ])->name('consultations.messages.store');
});

/*
|--------------------------------------------------------------------------
| إدارة المستخدمين — المدير فقط
|--------------------------------------------------------------------------
|
| أبقينا أسماء المسارات users.* حتى لا تنكسر الواجهات الحالية.
| أصبحت الروابط الفعلية تبدأ بـ /admin/users.
|
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->group(function () {
        Route::resource('users', UserController::class)
            ->except(['show']);
    });

/*
|--------------------------------------------------------------------------
| طلب الانضمام كمهندس
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:customer,engineer',
])->group(function () {
    Route::get('/become-engineer', [
        EngineerApplicationController::class,
        'create',
    ])->name('engineer-applications.create');

    Route::post('/become-engineer', [
        EngineerApplicationController::class,
        'store',
    ])->name('engineer-applications.store');
});

/*
|--------------------------------------------------------------------------
| إدارة طلبات المهندسين — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->group(function () {
        Route::get('/engineer-applications', [
            EngineerApplicationController::class,
            'index',
        ])->name('engineer-applications.index');

        Route::patch('/engineer-applications/{engineerApplication}/approve', [
            EngineerApplicationController::class,
            'approve',
        ])->name('engineer-applications.approve');

        Route::patch('/engineer-applications/{engineerApplication}/reject', [
            EngineerApplicationController::class,
            'reject',
        ])->name('engineer-applications.reject');
    });

/*
|--------------------------------------------------------------------------
| تقييم المهندس المرتبط بالاستشارة
|--------------------------------------------------------------------------
|
| تم إعطاء هذا النظام رابطًا مختلفًا لمنع تعارضه مع ReviewController.
| أسماء المسارات engineer-reviews.* لم تتغير.
|
*/

Route::middleware([
    'auth',
    'verified',
    'role:customer,engineer',
])->group(function () {
    Route::get('/consultations/{consultation}/engineer-review', [
        EngineerReviewController::class,
        'create',
    ])->name('engineer-reviews.create');

    Route::post('/consultations/{consultation}/engineer-review', [
        EngineerReviewController::class,
        'store',
    ])->name('engineer-reviews.store');
});

/*
|--------------------------------------------------------------------------
| الفواتير
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/invoices/{invoice}', [
        InvoiceController::class,
        'show',
    ])->name('invoices.show');

    Route::get('/invoices/{invoice}/download', [
        InvoiceController::class,
        'download',
    ])->name('invoices.download');
});

/*
|--------------------------------------------------------------------------
| تقييمات العملاء
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:customer',
])->group(function () {
    Route::get('/consultations/{consultation}/review', [
        ReviewController::class,
        'create',
    ])->name('reviews.create');

    Route::post('/consultations/{consultation}/review', [
        ReviewController::class,
        'store',
    ])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| إدارة التقييمات — المدير فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])->group(function () {
    Route::get('/admin/reviews', [
        ReviewController::class,
        'index',
    ])->name('reviews.index');

    Route::patch('/admin/reviews/{review}/approve', [
        ReviewController::class,
        'approve',
    ])->name('reviews.approve');

    Route::patch('/admin/reviews/{review}/reject', [
        ReviewController::class,
        'reject',
    ])->name('reviews.reject');

    Route::patch('/admin/reviews/{review}/featured', [
        ReviewController::class,
        'toggleFeatured',
    ])->name('reviews.featured');

    Route::delete('/admin/reviews/{review}', [
        ReviewController::class,
        'destroy',
    ])->name('reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| التحقق العام من البريد الإلكتروني
|--------------------------------------------------------------------------
*/

Route::get('/email/verify-public/{id}/{hash}', [
    PublicEmailVerificationController::class,
    'verify',
])
    ->middleware([
        'signed:relative',
        'throttle:6,1',
    ])
    ->name('verification.public.verify');

Route::get('/email/verified-success', [
    PublicEmailVerificationController::class,
    'success',
])->name('verification.public.success');

Route::get('/email/verification-status', [
    PublicEmailVerificationController::class,
    'status',
])
    ->middleware(['auth', 'throttle:30,1'])
    ->name('verification.status');
/*
|--------------------------------------------------------------------------
| تنزيل الملفات الخاصة
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->group(function () {
    Route::get(
        '/consultations/{consultation}/files/customer',
        [
            SecureFileController::class,
            'consultationCustomerFile',
        ]
    )->name('consultations.files.customer');

    Route::get(
        '/consultations/{consultation}/files/engineer',
        [
            SecureFileController::class,
            'consultationEngineerFile',
        ]
    )->name('consultations.files.engineer');

    Route::get(
        '/consultations/{consultation}/messages/{message}/attachment',
        [
            SecureFileController::class,
            'messageAttachment',
        ]
    )->name('consultations.messages.attachment');

    Route::get(
        '/payments/{payment}/receipt',
        [
            SecureFileController::class,
            'paymentReceipt',
        ]
    )->name('payments.receipt');
});

/*
|--------------------------------------------------------------------------
| المحادثات الموحدة
|--------------------------------------------------------------------------
|
| المحادثات المباشرة يبدأها المدير.
| محادثات الاستشارات لا تُفتح للعميل والمهندس إلا بعد تأكيد الدفع.
|
*/

Route::middleware([
    'auth',
    'verified',
])->group(function () {
    Route::get('/conversations', [
        ConversationController::class,
        'index',
    ])->name('conversations.index');

    Route::post('/admin/conversations/direct/{user}', [
        ConversationController::class,
        'startDirect',
    ])
        ->whereNumber('user')
        ->name('admin.conversations.start');

    Route::get('/conversations/{conversation}', [
        ConversationController::class,
        'show',
    ])
        ->whereNumber('conversation')
        ->name('conversations.show');

    Route::post('/conversations/{conversation}/messages', [
        ConversationMessageController::class,
        'store',
    ])
        ->whereNumber('conversation')
        ->name('conversations.messages.store');

    Route::delete(
        '/conversations/{conversation}/messages/{message}',
        [
            ConversationMessageController::class,
            'destroy',
        ]
    )
        ->whereNumber('conversation')
        ->whereNumber('message')
        ->scopeBindings()
        ->name('conversations.messages.destroy');

    Route::get(
        '/conversations/{conversation}/messages/{message}/attachment',
        [
            ConversationFileController::class,
            'show',
        ]
    )
        ->whereNumber('conversation')
        ->whereNumber('message')
        ->scopeBindings()
        ->name('conversations.messages.attachment');

    Route::get(
        '/conversations/{conversation}/messages/{message}/download',
        [
            ConversationFileController::class,
            'download',
        ]
    )
        ->whereNumber('conversation')
        ->whereNumber('message')
        ->scopeBindings()
        ->name('conversations.messages.download');
});
Route::middleware('auth')->group(function () {
    Route::get(
        '/support',
        [SupportTicketController::class, 'index']
    )->name('support.index');

    Route::get(
        '/support/create',
        [SupportTicketController::class, 'create']
    )->name('support.create');

    Route::post(
        '/support',
        [SupportTicketController::class, 'store']
    )->name('support.store');

    Route::get(
        '/support/{supportTicket}',
        [SupportTicketController::class, 'show']
    )->name('support.show');

    Route::post(
        '/support/{supportTicket}/messages',
        [SupportMessageController::class, 'store']
    )->name('support.messages.store');

    Route::get(
        '/support-messages/{supportMessage}/attachment',
        [SupportMessageController::class, 'attachment']
    )->name('support.messages.attachment');

    Route::patch(
        '/support/{supportTicket}/status',
        [SupportTicketController::class, 'updateStatus']
    )->name('support.status.update');

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get(
                '/support',
                [AdminSupportController::class, 'index']
            )->name('support.index');

            Route::get(
                '/support/settings',
                [AdminSupportController::class, 'settings']
            )->name('support.settings');

            Route::patch(
                '/support/settings',
                [AdminSupportController::class, 'updateSettings']
            )->name('support.settings.update');
        });
});
Route::get(
    '/employee/support',
    [SupportTicketController::class, 'employeeIndex']
)->middleware('auth')
 ->name('employee.support.index');
 /*
|--------------------------------------------------------------------------
| طلب تسجيل مكتب هندسي
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:customer,engineer',
])->group(function () {
    Route::get(
        '/office-application',
        [
            OfficeApplicationController::class,
            'create',
        ]
    )->name('office-applications.create');

    Route::post(
        '/office-application',
        [
            OfficeApplicationController::class,
            'store',
        ]
    )->name('office-applications.store');

    Route::get(
        '/office-application/status',
        [
            OfficeApplicationController::class,
            'status',
        ]
    )->name('office-applications.status');
});

/*
|--------------------------------------------------------------------------
| إدارة طلبات المكاتب — مدير النظام
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/office-applications',
            [
                AdminOfficeController::class,
                'applicationsIndex',
            ]
        )->name('office-applications.index');

        Route::get(
            '/office-applications/{officeApplication}',
            [
                AdminOfficeController::class,
                'applicationShow',
            ]
        )
            ->whereNumber('officeApplication')
            ->name('office-applications.show');

        Route::patch(
            '/office-applications/{officeApplication}/review',
            [
                AdminOfficeController::class,
                'reviewApplication',
            ]
        )
            ->whereNumber('officeApplication')
            ->name('office-applications.review');
    });

/*
|--------------------------------------------------------------------------
| اشتراك المكتب الهندسي
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:office_owner',
])->group(function () {
    Route::get(
        '/office/subscription',
        [
            OfficeSubscriptionController::class,
            'show',
        ]
    )->name('office.subscription');

    Route::post(
        '/office/subscription',
        [
            OfficeSubscriptionController::class,
            'store',
        ]
    )->name('office.subscription.store');
});
/*
|--------------------------------------------------------------------------
| إدارة اشتراكات المكاتب — مدير النظام
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/office-subscriptions',
            [
                AdminOfficeController::class,
                'subscriptionsIndex',
            ]
        )->name('office-subscriptions.index');

        Route::patch(
            '/office-subscriptions/{officeSubscription}/review',
            [
                AdminOfficeController::class,
                'reviewSubscription',
            ]
        )
            ->whereNumber('officeSubscription')
            ->name('office-subscriptions.review');
    });
    /*
|--------------------------------------------------------------------------
| إدارة المكاتب الهندسية — مدير النظام
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/offices',
            [
                AdminOfficeController::class,
                'officesIndex',
            ]
        )->name('offices.index');

        Route::get(
            '/offices/{office}',
            [
                AdminOfficeController::class,
                'officeShow',
            ]
        )->name('offices.show');

        Route::patch(
            '/offices/{office}/status',
            [
                AdminOfficeController::class,
                'updateOfficeStatus',
            ]
        )->name('offices.status');
    });
    /*
|--------------------------------------------------------------------------
| المكاتب الهندسية للمهندسين
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| عرض الملفات الشخصية للمكاتب — المدير والمهندسون
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin,engineer',
])->group(function () {
    Route::get(
        '/engineering-offices',
        [
            EngineeringOfficeController::class,
            'index',
        ]
    )->name('engineering-offices.index');

    Route::get(
        '/engineering-offices/{office}',
        [
            EngineeringOfficeController::class,
            'show',
        ]
    )->name('engineering-offices.show');
});

/*
|--------------------------------------------------------------------------
| طلبات انضمام المهندسين إلى المكاتب — المهندس فقط
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:engineer',
])->group(function () {
    Route::get(
        '/engineering-offices/{office}/join',
        [
            OfficeMembershipApplicationController::class,
            'create',
        ]
    )->name('office-membership-applications.create');

    Route::post(
        '/engineering-offices/{office}/join',
        [
            OfficeMembershipApplicationController::class,
            'store',
        ]
    )->name('office-membership-applications.store');

    Route::get(
        '/my-office-applications',
        [
            OfficeMembershipApplicationController::class,
            'mine',
        ]
    )->name('office-membership-applications.mine');
});

/*
|--------------------------------------------------------------------------
| إدارة طلبات انضمام المهندسين
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->group(function () {
    /*
    | يمكن للمالك أو المدير مشاهدة الطلبات حتى عند توقف المكتب،
    | حتى يعرف الطلبات الموجودة وحالة المكتب.
    */
    Route::get(
        '/office/membership-applications',
        [
            OfficeMembershipApplicationController::class,
            'index',
        ]
    )->name('office-membership-applications.index');

    Route::get(
        '/office/membership-applications/{officeMembershipApplication}',
        [
            OfficeMembershipApplicationController::class,
            'show',
        ]
    )->name('office-membership-applications.show');


    Route::get(
        '/office/membership-applications/{officeMembershipApplication}/file/{type}',
        [
            OfficeMembershipApplicationController::class,
            'file',
        ]
    )
        ->whereIn('type', ['cv', 'certificate'])
        ->name('office-membership-applications.file');


    /*
    | قبول أو رفض الطلب يحتاج مكتبًا فعالًا واشتراكًا ساريًا.
    */
    Route::patch(
        '/office/membership-applications/{officeMembershipApplication}/review',
        [
            OfficeMembershipApplicationController::class,
            'review',
        ]
    )
        ->middleware('office.operational')
        ->name('office-membership-applications.review');
});
/*
|--------------------------------------------------------------------------
| استشارات المكتب الهندسي
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->prefix('office')->name('office.')->group(function () {
    Route::get(
        '/consultations',
        [
            ConsultationOfficeAssignmentController::class,
            'index',
        ]
    )->name('consultations.index');

    Route::patch(
        '/consultations/{consultation}/assign-engineer',
        [
            ConsultationOfficeAssignmentController::class,
            'assignEngineer',
        ]
    )
        ->middleware('office.operational')
        ->name('consultations.assign-engineer');
});
/*
|--------------------------------------------------------------------------
| تحويل الاستشارات إلى المكاتب — مدير النظام
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/consultations/{consultation}/assign-office',
            [
                ConsultationOfficeAssignmentController::class,
                'adminAssignForm',
            ]
        )->name('consultation-office.form');

        Route::patch(
            '/consultations/{consultation}/assign-office',
            [
                ConsultationOfficeAssignmentController::class,
                'adminAssign',
            ]
        )->name('consultation-office.assign');

        Route::delete(
            '/consultations/{consultation}/assign-office',
            [
                ConsultationOfficeAssignmentController::class,
                'adminUnassign',
            ]
        )->name('consultation-office.unassign');
    });
    /*
|--------------------------------------------------------------------------
| الملف الشخصي للمكتب
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->prefix('office')->name('office.')->group(function () {
    Route::get(
        '/profile',
        [
            EngineeringOfficeController::class,
            'profile',
        ]
    )->name('profile');

    Route::patch(
        '/profile',
        [
            EngineeringOfficeController::class,
            'updateProfile',
        ]
    )->name('profile.update');
});
/*
|--------------------------------------------------------------------------
| لوحة تحكم المكتب
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->get(
    '/office/dashboard',
    [
        EngineeringOfficeController::class,
        'dashboard',
    ]
)->name('office.dashboard');
/*
|--------------------------------------------------------------------------
| إدارة أعضاء المكتب
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])
    ->prefix('office')
    ->name('office.')
    ->group(function () {
        /*
        | يمكن مشاهدة الأعضاء حتى لو كان الاشتراك منتهيًا
        | أو كان المكتب موقوفًا.
        */
        Route::get(
            '/members',
            [
                OfficeMemberController::class,
                'index',
            ]
        )->name('members.index');

        /*
        | تعديل بيانات العضو يحتاج مكتبًا فعالًا
        | واشتراكًا شهريًا ساريًا.
        */
        Route::patch(
            '/members/{officeMember}',
            [
                OfficeMemberController::class,
                'update',
            ]
        )
            ->middleware('office.operational')
            ->name('members.update');

        /*
        | إزالة عضو من المكتب تحتاج مكتبًا فعالًا
        | واشتراكًا شهريًا ساريًا.
        */
        Route::delete(
            '/members/{officeMember}',
            [
                OfficeMemberController::class,
                'destroy',
            ]
        )
            ->middleware('office.operational')
            ->name('members.destroy');
    });
    /*
|--------------------------------------------------------------------------
| إيصالات اشتراكات المكاتب
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->get(
    '/office-subscriptions/{officeSubscription}/receipt',
    [
        OfficeApplicationFileController::class,
        'subscriptionReceipt',
    ]
)->name('office-subscriptions.receipt');

/*
|--------------------------------------------------------------------------
| المساعد الذكي للزوار
|--------------------------------------------------------------------------
*/
Route::post(
    '/smart-assistant/guest/ask',
    [SupportBotController::class, 'guestAsk']
)
    ->middleware('throttle:20,1')
    ->name('support-bot.guest.ask');

Route::middleware([
    'auth',
    'verified',
])->prefix('support-bot')
    ->name('support-bot.')
    ->group(function () {
        Route::post(
            '/start',
            [SupportBotController::class, 'start']
        )->name('start');

        Route::post(
            '/send',
            [SupportBotController::class, 'send']
        )->name('send');

        Route::get(
            '/tickets/{ticket}/messages',
            [SupportBotController::class, 'messages']
        )->name('messages');

        Route::post(
            '/tickets/{ticket}/resolve',
            [SupportBotController::class, 'resolve']
        )->name('resolve');

        Route::post(
            '/tickets/{ticket}/transfer',
            [SupportBotController::class, 'transfer']
        )->name('transfer');
    });
    Route::middleware([
    'auth',
    'verified',
])->prefix('employee')
    ->name('employee.')
    ->group(function () {
        Route::get(
            '/support-tickets',
            [EmployeeSupportTicketController::class, 'index']
        )->name('support-tickets.index');

        Route::get(
            '/support-tickets/{ticket}',
            [EmployeeSupportTicketController::class, 'show']
        )->name('support-tickets.show');

        Route::post(
            '/support-tickets/{ticket}/claim',
            [EmployeeSupportTicketController::class, 'claim']
        )->name('support-tickets.claim');

        Route::post(
            '/support-tickets/{ticket}/reply',
            [EmployeeSupportTicketController::class, 'reply']
        )->name('support-tickets.reply');

        Route::post(
            '/support-tickets/{ticket}/resolve',
            [EmployeeSupportTicketController::class, 'resolve']
        )->name('support-tickets.resolve');

        Route::post(
            '/support-tickets/{ticket}/close',
            [EmployeeSupportTicketController::class, 'close']
        )->name('support-tickets.close');
    });
Route::view(
    '/privacy-policy',
    'privacy-policy'
)->name('privacy-policy');

Route::view(
    '/terms-and-conditions',
    'terms-and-conditions'
)->name('terms-and-conditions');
Route::middleware(['auth'])->group(function () {
    Route::view(
        '/support-center',
        'support-center'
    )->name('support.center');
});
Route::middleware([
    'auth',
    'verified',
    'role:admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/moderation',
            [ModerationController::class, 'index']
        )->name('moderation.index');

        Route::get(
            '/moderation/warnings/{warning}',
            [ModerationController::class, 'show']
        )->name('moderation.show');

        Route::patch(
            '/moderation/warnings/{warning}/confirm',
            [ModerationController::class, 'confirm']
        )->name('moderation.confirm');

        Route::patch(
            '/moderation/warnings/{warning}/cancel',
            [ModerationController::class, 'cancel']
        )->name('moderation.cancel');

        Route::patch(
            '/moderation/users/{user}/reactivate',
            [ModerationController::class, 'reactivate']
        )->name('moderation.reactivate');

        Route::patch(
            '/moderation/users/{user}/keep-suspended',
            [
                ModerationController::class,
                'keepSuspended',
            ]
        )->name('moderation.keep-suspended');
    });
    Route::middleware([
    'auth',
])
    ->prefix('moderation')
    ->name('moderation.')
    ->group(function () {
        Route::get(
            '/appeal',
            [
                ModerationAppealController::class,
                'create',
            ]
        )->name('appeal.create');

        Route::post(
            '/appeal',
            [
                ModerationAppealController::class,
                'store',
            ]
        )
            ->middleware('throttle:3,60')
            ->name('appeal.store');

        Route::delete(
            '/appeal/{appeal}',
            [
                ModerationAppealController::class,
                'cancel',
            ]
        )->name('appeal.cancel');
    });
    Route::prefix('admin/moderation-appeals')
    ->name('admin.moderation-appeals.')
    ->group(function () {
        Route::get(
            '/',
            [
                AdminModerationAppealController::class,
                'index',
            ]
        )->name('index');

        Route::get(
            '/{appeal}',
            [
                AdminModerationAppealController::class,
                'show',
            ]
        )->name('show');

        Route::patch(
            '/{appeal}/start-review',
            [
                AdminModerationAppealController::class,
                'startReview',
            ]
        )->name('start-review');

        Route::patch(
            '/{appeal}/approve',
            [
                AdminModerationAppealController::class,
                'approve',
            ]
        )->name('approve');

        Route::patch(
            '/{appeal}/reject',
            [
                AdminModerationAppealController::class,
                'reject',
            ]
        )->name('reject');
    });
require __DIR__.'/auth.php';
