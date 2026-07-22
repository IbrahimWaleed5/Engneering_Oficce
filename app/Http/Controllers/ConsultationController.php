<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض جميع الاستشارات
    |--------------------------------------------------------------------------
    */

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
            $query->where(
                'status',
                $request->status
            );
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

        $engineers = $this
            ->activeEngineersQuery()
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

    /*
    |--------------------------------------------------------------------------
    | صفحة إنشاء استشارة عامة
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $types = ConsultationType::query()
            ->orderBy('name')
            ->get();

        $engineer = null;

        return view(
            'consultations.create',
            compact(
                'types',
                'engineer'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | صفحة إنشاء استشارة لمهندس محدد
    |--------------------------------------------------------------------------
    */

    public function createForEngineer(
        Request $request,
        User $engineer
    ) {
        abort_unless(
            $engineer->hasActiveEngineerMembership(),
            404,
            'هذا المهندس غير نشط حاليًا.'
        );

        if (
            (int) $engineer->id
            === (int) $request->user()->id
        ) {
            return redirect()
                ->route('engineer.works.public')
                ->with(
                    'error',
                    'لا يمكنك طلب استشارة من نفسك.'
                );
        }

        $types = ConsultationType::query()
            ->orderBy('name')
            ->get();

        return view(
            'consultations.create',
            compact(
                'types',
                'engineer'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | حفظ الاستشارة
    |--------------------------------------------------------------------------
    */

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

        /*
         * منع المستخدم من اختيار نفسه كمهندس.
         */
        if (
            ! empty($validated['engineer_id'])
            && (int) $validated['engineer_id']
                === (int) $request->user()->id
        ) {
            return back()
                ->withErrors([
                    'engineer_id' =>
                        'لا يمكنك طلب استشارة من نفسك.',
                ])
                ->withInput();
        }

        $type = ConsultationType::findOrFail(
            $validated['consultation_type_id']
        );

        $engineer = null;

        /*
         * التأكد من أن المهندس المختار نشط
         * واشتراكه لم ينتهِ.
         */
        if (! empty($validated['engineer_id'])) {
            $engineer = $this
                ->activeEngineersQuery()
                ->where(
                    'id',
                    $validated['engineer_id']
                )
                ->where(
                    'id',
                    '!=',
                    $request->user()->id
                )
                ->first();

            if (! $engineer) {
                return back()
                    ->withErrors([
                        'engineer_id' =>
                            'المهندس المختار غير نشط أو انتهى اشتراكه.',
                    ])
                    ->withInput();
            }
        }

        $filePath = null;

        if ($request->hasFile('customer_file')) {
            $filePath = $request
                ->file('customer_file')
                ->store(
                    'consultations',
                    'public'
                );
        }

        $consultation = Consultation::create([
            'consultation_number' =>
                'CONS-' . time(),

            'customer_id' =>
                $request->user()->id,

            'consultation_type_id' =>
                $type->id,

            'engineer_id' =>
                $engineer?->id,

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'],

            'final_price' =>
                $type->price,

            'status' =>
                'waiting_payment',

            'payment_status' =>
                'unpaid',

            'customer_file' =>
                $filePath,
        ]);

        return redirect()
            ->route(
                'payments.create',
                $consultation
            )
            ->with(
                'success',
                'تم حفظ الطلب. أكمل الدفع لإرساله إلى المهندس.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | استشارات المستخدم كعميل
    |--------------------------------------------------------------------------
    */

    public function myConsultations(
        Request $request
    ) {
        $consultations = Consultation::with([
            'consultationType',
            'engineer',
            'review',
            'invoice',
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

    /*
    |--------------------------------------------------------------------------
    | صفحة تعيين المهندس
    |--------------------------------------------------------------------------
    */

    public function assignForm(
        Consultation $consultation
    ) {
        if (
            $consultation->payment_status
            !== 'paid'
        ) {
            return redirect()
                ->route('payments.index')
                ->with(
                    'error',
                    'لا يمكن تعيين مهندس قبل تأكيد الدفع.'
                );
        }

        $engineers = $this
            ->activeEngineersQuery()
            ->orderBy('name')
            ->get();

        return view(
            'consultations.assign',
            compact(
                'consultation',
                'engineers'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تعيين المهندس وتحديث حالة الاستشارة
    |--------------------------------------------------------------------------
    */

    public function assignEngineer(
        Request $request,
        Consultation $consultation
    ) {
        if (
            $consultation->payment_status
            !== 'paid'
        ) {
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

            'started_at' => [
                'required',
                'date',
            ],

            'expected_delivery_at' => [
                'required',
                'date',
                'after:started_at',
            ],
        ]);

        $engineer = null;

        if (! empty($validated['engineer_id'])) {
            $engineer = $this
                ->activeEngineersQuery()
                ->where(
                    'id',
                    $validated['engineer_id']
                )
                ->first();

            if (! $engineer) {
                return back()
                    ->withErrors([
                        'engineer_id' =>
                            'المهندس المختار غير نشط أو انتهى اشتراكه.',
                    ])
                    ->withInput();
            }
        }

        $consultation->update([
            'engineer_id' =>
                $engineer?->id,

            'status' =>
                $validated['status'],

            'started_at' =>
                $validated['started_at'],

            'expected_delivery_at' =>
                $validated['expected_delivery_at'],

            'delivered_at' =>
                null,
        ]);

        if ($engineer) {
            $engineer->notify(
                new SystemNotification(
                    'تم تعيين استشارة لك',
                    'تم تعيين الاستشارة رقم '
                        . $consultation->consultation_number
                        . ' لك.',
                    '/engineer/consultations'
                )
            );
        }

        return redirect()
            ->route('consultations.index')
            ->with(
                'success',
                'تم تعيين المهندس وتحديث حالة الاستشارة.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | الاستشارات المسندة للمهندس
    |--------------------------------------------------------------------------
    */

    public function engineerConsultations(
        Request $request
    ) {
        abort_unless(
            $request
                ->user()
                ->hasActiveEngineerMembership(),
            403,
            'حساب المهندس غير نشط. يجب تجديد الاشتراك.'
        );

        $consultations = Consultation::with([
            'customer',
            'consultationType',
        ])
            ->where(
                'engineer_id',
                $request->user()->id
            )
            ->where(
                'payment_status',
                'paid'
            )
            ->latest()
            ->get();

        return view(
            'consultations.engineer',
            compact('consultations')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | رفع الملف النهائي
    |--------------------------------------------------------------------------
    */

    public function uploadEngineerFile(
        Request $request,
        Consultation $consultation
    ) {
        $isAdmin =
            $request->user()->role === 'admin';

        $isAssignedEngineer =
            (int) $consultation->engineer_id
            === (int) $request->user()->id;

        abort_unless(
            $isAdmin || $isAssignedEngineer,
            403
        );

        /*
         * المدير يستطيع رفع الملف.
         * المهندس يجب أن يكون اشتراكه نشطًا.
         */
        if (
            ! $isAdmin
            && ! $request
                ->user()
                ->hasActiveEngineerMembership()
        ) {
            return back()->withErrors([
                'engineer_file' =>
                    'حساب المهندس غير نشط. يجب تجديد الاشتراك أولًا.',
            ]);
        }

        if (
            $consultation->payment_status
            !== 'paid'
        ) {
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
        ]);

        $filePath = $request
            ->file('engineer_file')
            ->store(
                'consultations',
                'public'
            );

        /*
         * عند رفع الملف تصبح الاستشارة مكتملة،
         * وبذلك يستطيع العميل تقييم المهندس.
         */
        $consultation->update([
            'engineer_file' =>
                $filePath,

            'status' =>
                'completed',

            'delivered_at' =>
                now(),
        ]);

        $consultation->load('customer');

        if ($consultation->customer) {
            $consultation->customer->notify(
                new SystemNotification(
                    'الملف النهائي جاهز',
                    'تم رفع الملف النهائي للاستشارة رقم '
                        . $consultation->consultation_number
                        . '. يمكنك الآن تقييم المهندس.',
                    '/my-consultations'
                )
            );
        }

        return back()->with(
            'success',
            'تم رفع الملف النهائي وأصبحت الاستشارة مكتملة.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | محادثة الاستشارة
    |--------------------------------------------------------------------------
    */

    public function chat(
        Request $request,
        Consultation $consultation
    ) {
        abort_unless(
            (int) $request->user()->id
                === (int) $consultation->customer_id

            || (int) $request->user()->id
                === (int) $consultation->engineer_id

            || $request->user()->role
                === 'admin',
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

    /*
    |--------------------------------------------------------------------------
    | استعلام المهندسين النشطين
    |--------------------------------------------------------------------------
    */

    private function activeEngineersQuery()
    {
        return User::query()
            ->where(
                'role',
                'engineer'
            )
            ->where(
                'status',
                'active'
            )
            ->where(
                'engineer_membership_status',
                'active'
            )
            ->whereNotNull(
                'engineer_active_until'
            )
            ->where(
                'engineer_active_until',
                '>',
                now()
            );
    }
}
