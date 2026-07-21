<?php

namespace App\Http\Controllers;

use App\Models\EngineerWork;
use App\Models\EngineerWorkImage;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EngineerWorkController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض الأعمال المقبولة للعامة
    |--------------------------------------------------------------------------
    */

    public function publicIndex()
{
    $works = EngineerWork::with([
        'engineer.employeeProfile.specialty',
        'coverImage',
    ])
        ->where('status', 'approved')
        ->latest()
        ->paginate(12);

    return view(
        'engineer-works.public-index',
        compact('works')
    );
}

    /*
    |--------------------------------------------------------------------------
    | عرض تفاصيل العمل
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        EngineerWork $engineerWork
    ) {
        $user = $request->user();

        $isAllowed =
            $engineerWork->status === 'approved'
            || (
                $user
                && (
                    $user->id === $engineerWork->engineer_id
                    || $user->role === 'admin'
                )
            );

        abort_unless($isAllowed, 404);

        $engineerWork->load([
            'engineer.employeeProfile.specialty',
            'images',
        ]);

        return view(
            'engineer-works.show',
            compact('engineerWork')
        );

    }

    /*
    |--------------------------------------------------------------------------
    | عرض أعمال المهندس
    |--------------------------------------------------------------------------
    */

    public function myWorks(Request $request)
{
    $works = EngineerWork::with([
        'coverImage',
        'engineer.employeeProfile.specialty',
    ])
        ->where('engineer_id', $request->user()->id)
        ->latest()
        ->get();

    return view(
        'engineer-works.my-works',
        compact('works')
    );
}

    /*
    |--------------------------------------------------------------------------
    | صفحة إضافة عمل جديد
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('engineer-works.create');
    }

    /*
    |--------------------------------------------------------------------------
    | حفظ عمل جديد
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'completion_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . date('Y'),
            ],

            'project_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'images' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:512000',
            ],
            'pdf_file' => [
    'nullable',
    'file',
    'mimes:pdf',
    'max:51200',
],

'dwg_file' => [
    'nullable',
    'file',
    'mimes:dwg',
    'max:102400',
],
        ]);
        $pdfPath = null;
$dwgPath = null;

if ($request->hasFile('pdf_file')) {

    $pdfPath = $request
        ->file('pdf_file')
        ->store(
            'engineer-works/files',
            'public'
        );
}

if ($request->hasFile('dwg_file')) {

    $dwgPath = $request
        ->file('dwg_file')
        ->store(
            'engineer-works/files',
            'public'
        );
}

        $work = EngineerWork::create([
    'engineer_id' => $request->user()->id,

    'title' => $validated['title'],

    'description' =>
        $validated['description'] ?? null,

    'location' =>
        $validated['location'] ?? null,

    'completion_year' =>
        $validated['completion_year'] ?? null,

    'project_type' =>
        $validated['project_type'] ?? null,

    'pdf_file' => $pdfPath,

    'dwg_file' => $dwgPath,

    'status' => 'pending',

    'is_featured' => false,

    'admin_note' => null,
]);

        foreach (
            $request->file('images') as $index => $image
        ) {
            $path = $image->store(
                'engineer-works',
                'public'
            );

            EngineerWorkImage::create([
                'engineer_work_id' => $work->id,
                'image_path' => $path,
                'sort_order' => $index,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | إشعار المديرين بوجود عمل جديد
        |--------------------------------------------------------------------------
        */

        $admins = User::where('role', 'admin')
            ->where('status', 'active')
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new SystemNotification(
                    'عمل هندسي جديد',
                    'قام المهندس '
                        . $request->user()->name
                        . ' بإضافة عمل جديد بعنوان: '
                        . $work->title,
                    route('admin.engineer-works.index')
                )
            );
        }

        return redirect()
            ->route('engineer.works.mine')
            ->with(
                'success',
                'تم إرسال العمل للمراجعة وإشعار المدير.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | حذف العمل
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        EngineerWork $engineerWork
    ) {
        abort_unless(
            $engineerWork->engineer_id
                === $request->user()->id
            || $request->user()->role === 'admin',
            403
        );

        $engineerWork->load('images');

        foreach ($engineerWork->images as $image) {
            Storage::disk('public')
                ->delete($image->image_path);
        }

        $engineerWork->delete();

        return back()->with(
            'success',
            'تم حذف العمل بنجاح.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | مراجعة الأعمال للمدير
    |--------------------------------------------------------------------------
    */

public function index()
{
    $works = EngineerWork::with([
        'engineer.employeeProfile.specialty',
        'coverImage',
    ])
        ->latest()
        ->get();

    return view(
        'engineer-works.admin-index',
        compact('works')
    );
}

    /*
    |--------------------------------------------------------------------------
    | الموافقة على العمل
    |--------------------------------------------------------------------------
    */

    public function approve(
        EngineerWork $engineerWork
    ) {
        $engineerWork->update([
            'status' => 'approved',
            'admin_note' => null,
        ]);

        $engineerWork->load('engineer');

        if ($engineerWork->engineer) {
            $engineerWork->engineer->notify(
                new SystemNotification(
                    'تم قبول عملك',
                    'تمت الموافقة على العمل: '
                        . $engineerWork->title,
                    route(
                        'engineer.works.show',
                        $engineerWork
                    )
                )
            );
        }

        return back()->with(
            'success',
            'تمت الموافقة على العمل وإشعار المهندس.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | رفض العمل
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        EngineerWork $engineerWork
    ) {
        $validated = $request->validate([
            'admin_note' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'admin_note.required' =>
                'يرجى كتابة سبب رفض العمل.',

            'admin_note.max' =>
                'سبب الرفض يجب ألا يتجاوز 1000 حرف.',
        ]);

        $engineerWork->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
        ]);

        $engineerWork->load('engineer');

        if ($engineerWork->engineer) {
            $engineerWork->engineer->notify(
                new SystemNotification(
                    'تم رفض عملك',
                    'تم رفض العمل: '
                        . $engineerWork->title
                        . ' — السبب: '
                        . $validated['admin_note'],
                    route('engineer.works.mine')
                )
            );
        }

        return back()->with(
            'success',
            'تم رفض العمل وإرسال الملاحظة للمهندس.'
        );
    }
}
