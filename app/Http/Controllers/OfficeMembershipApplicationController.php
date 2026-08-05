<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewOfficeMembershipApplicationRequest;
use App\Http\Requests\StoreOfficeMembershipApplicationRequest;
use App\Models\EngineeringSpecialty;
use App\Models\Office;
use App\Models\OfficeMember;
use App\Models\OfficeMembershipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfficeMembershipApplicationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | نموذج طلب انضمام المهندس
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request,
        Office $office
    ): View|RedirectResponse {
        $engineer = $request->user();

        if (! $office->isOperational()) {
            return redirect()
                ->route(
                    'engineering-offices.show',
                    $office
                )
                ->with(
                    'error',
                    'هذا المكتب لا يستقبل طلبات انضمام حاليًا.'
                );
        }

        $isAlreadyMember = OfficeMember::query()
            ->where('office_id', $office->id)
            ->where('user_id', $engineer->id)
            ->where('status', 'active')
            ->exists();

        if ($isAlreadyMember) {
            return redirect()
                ->route(
                    'engineering-offices.show',
                    $office
                )
                ->with(
                    'info',
                    'أنت عضو بالفعل في هذا المكتب.'
                );
        }

        $hasPendingApplication =
            OfficeMembershipApplication::query()
                ->where('office_id', $office->id)
                ->where(
                    'engineer_id',
                    $engineer->id
                )
                ->where('status', 'pending')
                ->exists();

        if ($hasPendingApplication) {
            return redirect()
                ->route(
                    'engineering-offices.show',
                    $office
                )
                ->with(
                    'info',
                    'لديك طلب انضمام قيد المراجعة لهذا المكتب.'
                );
        }

        $specialties = EngineeringSpecialty::query()
            ->orderBy('name')
            ->get();

        return view(
            'office-membership-applications.create',
            compact(
                'office',
                'specialties'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | حفظ طلب الانضمام
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreOfficeMembershipApplicationRequest $request,
        Office $office
    ): RedirectResponse {
        $engineer = $request->user();

        if (! $office->isOperational()) {
            return back()->with(
                'error',
                'هذا المكتب لا يستقبل طلبات انضمام حاليًا.'
            );
        }

        $isAlreadyMember = OfficeMember::query()
            ->where('office_id', $office->id)
            ->where('user_id', $engineer->id)
            ->where('status', 'active')
            ->exists();

        if ($isAlreadyMember) {
            return redirect()
                ->route(
                    'engineering-offices.show',
                    $office
                )
                ->with(
                    'info',
                    'أنت عضو بالفعل في هذا المكتب.'
                );
        }

        $hasPendingApplication =
            OfficeMembershipApplication::query()
                ->where('office_id', $office->id)
                ->where(
                    'engineer_id',
                    $engineer->id
                )
                ->where('status', 'pending')
                ->exists();

        if ($hasPendingApplication) {
            return redirect()
                ->route(
                    'engineering-offices.show',
                    $office
                )
                ->with(
                    'info',
                    'لديك طلب انضمام قيد المراجعة لهذا المكتب.'
                );
        }

        $cvPath = $request
            ->file('cv')
            ->store(
                'office-membership-applications/'
                . $office->id
                . '/'
                . $engineer->id
                . '/cv'
            );

        $certificatePath = $request
            ->file('certificate')
            ->store(
                'office-membership-applications/'
                . $office->id
                . '/'
                . $engineer->id
                . '/certificates'
            );

        try {
            OfficeMembershipApplication::create([
                'office_id' => $office->id,

                'engineer_id' => $engineer->id,

                'specialty_id' =>
                    $request->validated(
                        'specialty_id'
                    ),

                'requested_position' =>
                    $request->validated(
                        'requested_position'
                    ),

                'years_of_experience' =>
                    $request->validated(
                        'years_of_experience'
                    ),

                'cv_path' => $cvPath,

                'certificate_path' =>
                    $certificatePath,

                'message' =>
                    $request->validated('message'),

                'status' => 'pending',
            ]);
        } catch (\Throwable $exception) {
            Storage::delete([
                $cvPath,
                $certificatePath,
            ]);

            throw $exception;
        }

        return redirect()
            ->route(
                'engineering-offices.show',
                $office
            )
            ->with(
                'success',
                'تم إرسال طلب الانضمام إلى مدير المكتب بنجاح.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | طلبات المهندس
    |--------------------------------------------------------------------------
    */

    public function mine(
        Request $request
    ): View {
        $applications =
            OfficeMembershipApplication::query()
                ->where(
                    'engineer_id',
                    $request->user()->id
                )
                ->with([
                    'office:id,name,slug,status,subscription_status',
                    'specialty',
                    'reviewer:id,name',
                ])
                ->latest()
                ->paginate(15)
                ->withQueryString();

        return view(
            'office-membership-applications.mine',
            compact('applications')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | طلبات الانضمام عند مدير المكتب
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {
        $managedOfficeMembership = $request
            ->user()
            ->managedOfficeMemberships()
            ->with('office')
            ->first();

        abort_unless(
            $managedOfficeMembership !== null,
            403,
            'ليس لديك صلاحية إدارة مكتب.'
        );

        $office = $managedOfficeMembership->office;

        $applications =
            OfficeMembershipApplication::query()
                ->where('office_id', $office->id)
                ->with([
                    'engineer:id,name,email,phone,profile_photo,role',
                    'specialty',
                    'reviewer:id,name',
                ])
                ->latest()
                ->paginate(20)
                ->withQueryString();

        $statistics = [
            'pending' =>
                OfficeMembershipApplication::query()
                    ->where(
                        'office_id',
                        $office->id
                    )
                    ->where('status', 'pending')
                    ->count(),

            'approved' =>
                OfficeMembershipApplication::query()
                    ->where(
                        'office_id',
                        $office->id
                    )
                    ->where('status', 'approved')
                    ->count(),

            'rejected' =>
                OfficeMembershipApplication::query()
                    ->where(
                        'office_id',
                        $office->id
                    )
                    ->where('status', 'rejected')
                    ->count(),
        ];

        return view(
            'office-membership-applications.index',
            compact(
                'office',
                'applications',
                'statistics'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تفاصيل الطلب عند مدير المكتب
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        OfficeMembershipApplication $officeMembershipApplication
    ): View {
        $managedOfficeMembership = $request
            ->user()
            ->managedOfficeMemberships()
            ->where(
                'office_id',
                $officeMembershipApplication->office_id
            )
            ->first();

        abort_unless(
            $managedOfficeMembership !== null,
            403,
            'لا يمكنك عرض طلبات هذا المكتب.'
        );

        $officeMembershipApplication->load([
            'office:id,name,slug,status',
            'engineer:id,name,email,phone,profile_photo,role',
            'specialty',
            'reviewer:id,name',
        ]);

        return view(
            'office-membership-applications.show',
            [
                'application' =>
                    $officeMembershipApplication,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | تحميل ملفات طلب الانضمام
    |--------------------------------------------------------------------------
    */

    public function file(
        Request $request,
        OfficeMembershipApplication $officeMembershipApplication,
        string $type
    ): StreamedResponse {
        abort_unless(
            in_array($type, ['cv', 'certificate'], true),
            404
        );

        $user = $request->user();

        $canManageOffice = $user
            ->managedOfficeMemberships()
            ->where(
                'office_id',
                $officeMembershipApplication->office_id
            )
            ->exists();

        abort_unless(
            $canManageOffice || $user->role === 'admin',
            403,
            'لا يمكنك الوصول إلى ملفات هذا الطلب.'
        );

        $path = match ($type) {
            'cv' => $officeMembershipApplication->cv_path,
            'certificate' =>
                $officeMembershipApplication->certificate_path,
        };

        abort_if(
            blank($path) || ! Storage::exists($path),
            404,
            'الملف غير موجود.'
        );

        $extension = pathinfo(
            $path,
            PATHINFO_EXTENSION
        );

        $baseName = match ($type) {
            'cv' => 'CV',
            'certificate' => 'Certificate',
        };

        $downloadName =
            $baseName
            . '-Application-'
            . $officeMembershipApplication->id
            . ($extension ? '.' . $extension : '');

        return Storage::download(
            $path,
            $downloadName
        );
    }

    /*
    |--------------------------------------------------------------------------
    | قبول أو رفض طلب المهندس
    |--------------------------------------------------------------------------
    */

    public function review(
        ReviewOfficeMembershipApplicationRequest $request,
        OfficeMembershipApplication $officeMembershipApplication
    ): RedirectResponse {
        $managedOfficeMembership = $request
            ->user()
            ->managedOfficeMemberships()
            ->where(
                'office_id',
                $officeMembershipApplication->office_id
            )
            ->first();

        abort_unless(
            $managedOfficeMembership !== null,
            403,
            'لا يمكنك مراجعة طلبات هذا المكتب.'
        );

        if (
            $officeMembershipApplication->status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'تمت مراجعة هذا الطلب سابقًا.'
            );
        }

        $office = $managedOfficeMembership->office;

        if (! $office->isOperational()) {
            return back()->with(
                'error',
                'لا يمكن قبول مهندسين لأن المكتب غير فعال حاليًا.'
            );
        }

        $decision = $request->validated('decision');

        if ($decision === 'reject') {
            $officeMembershipApplication->update([
                'status' => 'rejected',

                'reviewed_by' =>
                    $request->user()->id,

                'reviewed_at' => now(),

                'rejection_reason' =>
                    $request->validated(
                        'rejection_reason'
                    ),
            ]);

            return redirect()
                ->route(
                    'office-membership-applications.index'
                )
                ->with(
                    'success',
                    'تم رفض طلب انضمام المهندس.'
                );
        }

        DB::transaction(function () use (
            $officeMembershipApplication,
            $request
        ) {
            OfficeMember::updateOrCreate(
                [
                    'office_id' =>
                        $officeMembershipApplication
                            ->office_id,

                    'user_id' =>
                        $officeMembershipApplication
                            ->engineer_id,
                ],
                [
                    'specialty_id' =>
                        $officeMembershipApplication
                            ->specialty_id,

                    'position' =>
                        $request->validated(
                            'position'
                        ),

                    'office_role' => 'engineer',

                    'status' => 'active',

                    'approved_by' =>
                        $request->user()->id,

                    'joined_at' => now(),

                    'left_at' => null,
                ]
            );

            $officeMembershipApplication->update([
                'status' => 'approved',

                'reviewed_by' =>
                    $request->user()->id,

                'reviewed_at' => now(),

                'rejection_reason' => null,
            ]);
        });

        return redirect()
            ->route(
                'office-membership-applications.index'
            )
            ->with(
                'success',
                'تم قبول المهندس وإضافته إلى فريق المكتب.'
            );
    }
}
