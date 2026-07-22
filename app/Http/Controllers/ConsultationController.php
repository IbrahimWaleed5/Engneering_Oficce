<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;


class ConsultationController extends Controller
{
    public function index(Request $request)
{
    $query = Consultation::with([
        'customer',
        'engineer',
        'consultationType',
    ]);

    /*
    |--------------------------------------------------------------------------
    | البحث برقم الاستشارة أو اسم العميل
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
        $search = trim((string) $request->search);

        $query->where(function ($subQuery) use ($search) {
            $subQuery
                ->where(
                    'consultation_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'customer',
                    function ($customerQuery) use ($search) {
                        $customerQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | فلترة حسب الحالة
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | فلترة حسب المهندس
    |--------------------------------------------------------------------------
    */

    if ($request->filled('engineer_id')) {
        $query->where(
            'engineer_id',
            $request->engineer_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | فلترة من تاريخ
    |--------------------------------------------------------------------------
    */

    if ($request->filled('date_from')) {
        $query->whereDate(
            'created_at',
            '>=',
            $request->date_from
        );
    }

    /*
    |--------------------------------------------------------------------------
    | فلترة إلى تاريخ
    |--------------------------------------------------------------------------
    */

    if ($request->filled('date_to')) {
        $query->whereDate(
            'created_at',
            '<=',
            $request->date_to
        );
    }

    $consultations = $query
        ->latest()
        ->paginate(15)
        ->withQueryString();

    $engineers = User::query()
        ->where('role', 'engineer')
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    return view(
        'consultations.index',
        compact(
            'consultations',
            'engineers'
        )
    );
}

public function create()
{
    $types = ConsultationType::all();
    $engineer = null;

    return view(
        'consultations.create',
        compact('types', 'engineer')
    );
}
    public function createForEngineer(User $engineer)
{
    abort_unless(
        $engineer->role === 'engineer'
        && $engineer->status === 'active',
        404
    );

    $types = ConsultationType::all();

    return view(
        'consultations.create',
        compact('types', 'engineer')
    );
}

public function store(Request $request)
{
    $validated = $request->validate([
        'consultation_type_id' => [
            'required',
            'exists:consultation_types,id',
        ],

        'engineer_id' => [
            'nullable',
            'exists:users,id',
        ],

        'title' => [
            'required',
            'string',
            'max:255',
        ],

        'description' => [
            'required',
            'string',
        ],

        'customer_file' => [
            'nullable',
            'file',
            'mimes:pdf,jpg,jpeg,png,dwg',
            'max:512000',
        ],
    ]);

    $type = ConsultationType::findOrFail(
        $validated['consultation_type_id']
    );

    $engineer = null;

    if (!empty($validated['engineer_id'])) {
        $engineer = User::query()
            ->where('id', $validated['engineer_id'])
            ->where('role', 'engineer')
            ->where('status', 'active')
            ->firstOrFail();
    }

    $filePath = null;

    if ($request->hasFile('customer_file')) {
        $filePath = $request
            ->file('customer_file')
            ->store('consultations', 'public');
    }

    $consultation = Consultation::create([
        'consultation_number' => 'CONS-' . time(),
        'customer_id' => $request->user()->id,
        'consultation_type_id' => $type->id,
        'engineer_id' => $engineer?->id,
        'title' => $validated['title'],
        'description' => $validated['description'],
        'final_price' => $type->price,
        'status' => 'waiting_payment',
        'payment_status' => 'unpaid',
        'customer_file' => $filePath,
    ]);

    return redirect()
        ->route('payments.create', $consultation)
        ->with(
            'success',
            'تم حفظ الطلب. أكمل الدفع لإرساله إلى المهندس.'
        );
}
    public function myConsultations(Request $request)
    {
        $consultations = Consultation::with([
            'consultationType',
            'engineer',
        ])
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->latest()
            ->get();

        return view(
            'consultations.my-consultations',
            compact('consultations')
        );
    }

    public function assignForm(
    Consultation $consultation
) {
    if ($consultation->payment_status !== 'paid') {
        return redirect()
            ->route('payments.index')
            ->with(
                'error',
                'لا يمكن تعيين مهندس قبل تأكيد الدفع.'
            );
    }

    $engineers = User::query()
        ->where('role', 'engineer')
        ->where('status', 'active')
        ->get();

    return view(
        'consultations.assign',
        compact('consultation', 'engineers')
    );
}

    public function assignEngineer(
        Request $request,
        Consultation $consultation
    ) {
        if ($consultation->payment_status !== 'paid') {
    abort(
        403,
        'لا يمكن تعيين مهندس لاستشارة غير مدفوعة.'
    );
}
        $validated = $request->validate([
            'engineer_id' => [
                'nullable',
                'exists:users,id',
            ],

            'status' => [
                'required',
                'in:pending,in_progress,completed,cancelled',
            ],
        ]);

        $engineer = null;

        if (!empty($validated['engineer_id'])) {
            $engineer = User::findOrFail(
                $validated['engineer_id']
            );

            if ($engineer->role !== 'engineer') {
                return back()
                    ->withErrors([
                        'engineer_id' =>
                            'المستخدم المختار ليس مهندسًا.',
                    ])
                    ->withInput();
            }
        }

        $consultation->update([
            'engineer_id' =>
                $validated['engineer_id'] ?? null,

            'status' =>
                $validated['status'],
        ]);

        if ($engineer) {
            $engineer->notify(
                new SystemNotification(
                    'تم تعيين استشارة لك',
                    'تم تعيين الاستشارة رقم ' .
                    $consultation->consultation_number .
                    ' لك.',
                    '/engineer/consultations'
                )
            );
        }

        return redirect('/consultations')
            ->with(
                'success',
                'تم تعيين المهندس وتحديث حالة الاستشارة'
            );
    }

   public function engineerConsultations(
    Request $request
) {
    $consultations = Consultation::with([
        'customer',
        'consultationType',
    ])
        ->where('engineer_id', $request->user()->id)
        ->where('payment_status', 'paid')
        ->latest()
        ->get();

    return view(
        'consultations.engineer',
        compact('consultations')
    );
}

    public function uploadEngineerFile(
        Request $request,
        Consultation $consultation
    ) {
        abort_unless(
            $request->user()->role === 'admin'
            || $consultation->engineer_id
                === $request->user()->id,
            403
        );
        if ($consultation->payment_status !== 'paid') {
    return back()->withErrors([
        'engineer_file' =>
            'لا يمكن رفع الملف النهائي قبل تأكيد الدفع.',
    ]);
}

        $request->validate([
            'engineer_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,dwg',
                'max:512000',
            ],
            'engineer_id' => [
    'nullable',
    'exists:users,id',
],
        ]);


        $filePath = $request
            ->file('engineer_file')
            ->store('consultations', 'public');

        $consultation->update([
            'engineer_file' => $filePath,
            'status' => 'completed',
        ]);

        $consultation->load('customer');

        if ($consultation->customer) {
            $consultation->customer->notify(
                new SystemNotification(
                    'الملف النهائي جاهز',
                    'تم رفع الملف النهائي للاستشارة رقم ' .
                    $consultation->consultation_number . '.',
                    '/my-consultations'
                )
            );
        }

        return back()->with(
            'success',
            'تم رفع الملف النهائي'
        );
    }
    public function chat(
    Request $request,
    Consultation $consultation
) {
    abort_unless(
        $request->user()->id === $consultation->customer_id
        || $request->user()->id === $consultation->engineer_id
        || $request->user()->role === 'admin',
        403
    );

    $consultation->load([
        'customer',
        'engineer',
        'consultationType',
        'messages.sender',
    ]);

    return view(
        'consultations.chat',
        compact('consultation')
    );
}
}
