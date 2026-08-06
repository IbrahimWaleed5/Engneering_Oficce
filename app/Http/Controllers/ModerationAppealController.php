<?php

namespace App\Http\Controllers;

use App\Models\ModerationAppeal;
use App\Models\UserWarning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ModerationAppealController extends Controller
{
    /**
     * عرض صفحة تعليق الحساب والطعن.
     */
    public function create(
        Request $request
    ): View {
        $user = $request->user();

        abort_unless(
            in_array(
                $user->status,
                [
                    'suspended_pending_review',
                    'suspended',
                    'blocked',
                ],
                true
            ),
            403,
            'حسابك غير معلّق.'
        );

        $latestWarning = UserWarning::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'active',
                'confirmed',
                'appealed',
            ])
            ->with('moderation')
            ->latest('id')
            ->first();

        $pendingAppeal = ModerationAppeal::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'pending',
                'under_review',
            ])
            ->latest('id')
            ->first();

        $latestAppeal = ModerationAppeal::query()
            ->where('user_id', $user->id)
            ->with('reviewer:id,name')
            ->latest('id')
            ->first();

        return view(
            'moderation.appeal',
            compact(
                'user',
                'latestWarning',
                'pendingAppeal',
                'latestAppeal'
            )
        );
    }

    /**
     * إرسال طعن جديد إلى الإدارة.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            in_array(
                $user->status,
                [
                    'suspended_pending_review',
                    'suspended',
                    'blocked',
                ],
                true
            ),
            403,
            'لا يمكنك إرسال طعن لأن الحساب غير معلّق.'
        );

        $existingAppeal = ModerationAppeal::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'pending',
                'under_review',
            ])
            ->exists();

        if ($existingAppeal) {
            return back()->with(
                'error',
                'لديك طعن قيد المراجعة بالفعل.'
            );
        }

        $data = $request->validate([
            'message' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ], [
            'message.required' =>
                'يجب كتابة رسالة الطعن.',

            'message.min' =>
                'يجب ألا تقل رسالة الطعن عن 20 حرفًا.',

            'message.max' =>
                'رسالة الطعن طويلة جدًا.',

            'attachment.mimes' =>
                'المرفق يجب أن يكون صورة أو ملف PDF.',

            'attachment.max' =>
                'حجم المرفق يجب ألا يتجاوز 5 ميجابايت.',
        ]);

        $latestWarning = UserWarning::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'active',
                'confirmed',
                'appealed',
            ])
            ->latest('id')
            ->first();

        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentMimeType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $attachmentPath = $file->store(
                'moderation-appeals',
                'public'
            );

            $attachmentOriginalName =
                $file->getClientOriginalName();

            $attachmentMimeType =
                $file->getMimeType();
        }

        $appeal = ModerationAppeal::create([
            'user_id' => $user->id,

            'user_warning_id' =>
                $latestWarning?->id,

            'status' => 'pending',

            'message' =>
                trim($data['message']),

            'attachment_path' =>
                $attachmentPath,

            'attachment_original_name' =>
                $attachmentOriginalName,

            'attachment_mime_type' =>
                $attachmentMimeType,
        ]);

        if ($latestWarning) {
            $latestWarning->update([
                'status' => 'appealed',
            ]);
        }

        return redirect()
            ->route('moderation.appeal.create')
            ->with(
                'success',
                'تم إرسال الطعن إلى الإدارة بنجاح، وسيتم إشعارك بعد مراجعته.'
            );
    }

    /**
     * إلغاء الطعن قبل بدء المراجعة.
     */
    public function cancel(
        Request $request,
        ModerationAppeal $appeal
    ): RedirectResponse {
        abort_unless(
            (int) $appeal->user_id ===
                (int) $request->user()->id,
            403
        );

        if ($appeal->status !== 'pending') {
            return back()->with(
                'error',
                'لا يمكن إلغاء الطعن بعد بدء مراجعته.'
            );
        }

        if ($appeal->attachment_path) {
            Storage::disk('public')->delete(
                $appeal->attachment_path
            );
        }

        $appeal->update([
            'status' => 'cancelled',
            'resolved_at' => now(),
        ]);

        if ($appeal->warning) {
            $appeal->warning->update([
                'status' => 'active',
            ]);
        }

        return back()->with(
            'success',
            'تم إلغاء طلب الطعن.'
        );
    }
}
