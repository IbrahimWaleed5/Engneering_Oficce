<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeApplicationRequest;
use App\Models\OfficeApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OfficeApplicationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->ownedOffice()->exists()) {
            return redirect()
                ->route('office.dashboard')
                ->with(
                    'info',
                    'لديك مكتب مسجل بالفعل.'
                );
        }

        $pendingApplication = OfficeApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingApplication) {
            return redirect()
                ->route('office-applications.status')
                ->with(
                    'info',
                    'لديك طلب مكتب قيد المراجعة بالفعل.'
                );
        }

        return view('office-applications.create');
    }

    public function store(
        StoreOfficeApplicationRequest $request
    ): RedirectResponse {
        $user = $request->user();

        $hasPendingApplication = OfficeApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingApplication) {
            return redirect()
                ->route('office-applications.status')
                ->with(
                    'info',
                    'لديك طلب مكتب قيد المراجعة بالفعل.'
                );
        }

        $baseDirectory = 'office-applications/' . $user->id;

        $commercialRegistrationPath = $request
            ->file('commercial_registration_file')
            ->store($baseDirectory . '/commercial-registration');

        $licenseDocumentPath = $request
            ->file('license_document')
            ->store($baseDirectory . '/licenses');

        $paymentReceiptPath = $request
            ->file('payment_receipt')
            ->store($baseDirectory . '/payment-receipts');

        try {
            OfficeApplication::create([
                'user_id' => $user->id,
                'office_name' => $request->validated('office_name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
                'commercial_registration' =>
                    $request->validated('commercial_registration'),
                'license_number' =>
                    $request->validated('license_number'),
                'country' => $request->validated('country'),
                'city' => $request->validated('city'),
                'address' => $request->validated('address'),
                'notes' => $request->validated('notes'),
                'commercial_registration_path' =>
                    $commercialRegistrationPath,
                'license_document_path' =>
                    $licenseDocumentPath,
                'payment_method' =>
                    $request->validated('payment_method'),
                'payment_reference' =>
                    $request->validated('payment_reference'),
                'payment_receipt_path' =>
                    $paymentReceiptPath,
                'paid_at' => now(),
                'status' => 'pending',
            ]);
        } catch (\Throwable $exception) {
            Storage::delete([
                $commercialRegistrationPath,
                $licenseDocumentPath,
                $paymentReceiptPath,
            ]);

            throw $exception;
        }

        return redirect()
            ->route('office-applications.status')
            ->with(
                'success',
                'تم إرسال طلب المكتب وإيصال اشتراك 300 دولار بنجاح، وسيقوم مدير النظام بمراجعتهما.'
            );
    }

    public function status(): View
    {
        $application = OfficeApplication::query()
            ->where(
                'user_id',
                request()->user()->id
            )
            ->with('reviewer')
            ->latest()
            ->first();

        return view(
            'office-applications.status',
            compact('application')
        );
    }
}
