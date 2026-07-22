<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\EngineerApplication;
use App\Models\EngineeringSpecialty;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EngineerApplicationController extends Controller
{
    public function create(Request $request)
    {
        abort_unless(
            $request->user()->role === 'customer',
            403
        );

        $hasPendingApplication = EngineerApplication::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->exists();

        if ($hasPendingApplication) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'لديك طلب انضمام سابق قيد المعالجة.'
                );
        }

        $specialties = EngineeringSpecialty::query()
            ->orderBy('name')
            ->get();

        return view(
            'engineer-applications.create',
            compact('specialties')
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            $request->user()->role === 'customer',
            403
        );

        $hasPendingApplication = EngineerApplication::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->exists();

        if ($hasPendingApplication) {
            return back()->withErrors([
                'application' =>
                    'لديك طلب انضمام سابق قيد المعالجة.',
            ]);
        }

        $validated = $request->validate([
            'specialty_id' => [
                'required',
                'exists:engineering_specialties,id',
            ],

            'certificate_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'cv_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],

            'payment_receipt' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
            ],
        ]);

        $certificatePath = $request
            ->file('certificate_file')
            ->store(
                'engineer-applications/certificates',
                'public'
            );

        $receiptPath = $request
            ->file('payment_receipt')
            ->store(
                'engineer-applications/receipts',
                'public'
            );

        $cvPath = null;

        if ($request->hasFile('cv_file')) {
            $cvPath = $request
                ->file('cv_file')
                ->store(
                    'engineer-applications/cv',
                    'public'
                );
        }

        EngineerApplication::create([
            'user_id' => $request->user()->id,
            'specialty_id' => $validated['specialty_id'],
            'certificate_file' => $certificatePath,
            'cv_file' => $cvPath,
            'payment_receipt' => $receiptPath,
            'amount' => 50,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'تم إرسال طلب الانضمام وإيصال الدفع للإدارة.'
            );
    }

    public function index()
    {
        $applications = EngineerApplication::with([
            'user',
            'specialty',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'engineer-applications.index',
            compact('applications')
        );
    }

    public function approve(
        Request $request,
        EngineerApplication $engineerApplication
    ) {
        $request->validate([
            'admin_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if ($engineerApplication->status === 'approved') {
            return back()->with(
                'success',
                'تمت الموافقة على هذا الطلب سابقًا.'
            );
        }

        DB::transaction(function () use (
            $request,
            $engineerApplication
        ) {
            $application = EngineerApplication::query()
                ->with('user')
                ->lockForUpdate()
                ->findOrFail($engineerApplication->id);

            $application->update([
                'payment_status' => 'paid',
                'status' => 'approved',
                'admin_note' => $request->admin_note,
            ]);

            $application->user->update([
                'role' => 'engineer',
                'status' => 'active',
            ]);

            EmployeeProfile::updateOrCreate(
                [
                    'user_id' => $application->user_id,
                ],
                [
                    'specialty_id' =>
                        $application->specialty_id,
                ]
            );

            $application->user->notify(
                new SystemNotification(
                    'تم قبول طلب الانضمام',
                    'تم تأكيد الدفع وتحويل حسابك إلى حساب مهندس.',
                    '/dashboard'
                )
            );
        });

        return back()->with(
            'success',
            'تم قبول الدفع وتحويل المستخدم إلى مهندس.'
        );
    }

    public function reject(
        Request $request,
        EngineerApplication $engineerApplication
    ) {
        $validated = $request->validate([
            'admin_note' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $engineerApplication->update([
            'payment_status' => 'rejected',
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
        ]);

        $engineerApplication->user?->notify(
            new SystemNotification(
                'تم رفض طلب الانضمام',
                $validated['admin_note'],
                '/dashboard'
            )
        );

        return back()->with(
            'success',
            'تم رفض الطلب.'
        );
    }
}
