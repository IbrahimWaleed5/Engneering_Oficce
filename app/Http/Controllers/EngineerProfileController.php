<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\View\View;

class EngineerProfileController extends Controller
{
    public function show(User $user): View
    {
        abort_unless(
            $user->role === 'engineer'
            && $user->status === 'active',
            404
        );

        $currentUser = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | السماح بفتح ملف المهندس
        |--------------------------------------------------------------------------
        |
        | الملف العام يحتاج عضوية مهندس فعالة.
        | لكن العميل الذي لديه استشارة مع المهندس يستطيع فتح ملفه
        | حتى لو انتهت العضوية بعد إنشاء الاستشارة.
        |
        */

        $hasConsultationAccess = false;

        if ($currentUser) {
            $hasConsultationAccess =
                $currentUser->role === 'admin'
                || (int) $currentUser->id === (int) $user->id
                || Consultation::query()
                    ->where('engineer_id', $user->id)
                    ->where('customer_id', $currentUser->id)
                    ->exists();
        }

        abort_unless(
            $user->hasActiveEngineerMembership()
            || $hasConsultationAccess,
            404
        );

        $user->load([
            'employeeProfile.specialty',

            'engineerWorks' => function ($query) {
                $query
                    ->where('status', 'approved')
                    ->with([
                        'coverImage',
                        'images',
                    ])
                    ->latest();
            },

            'receivedEngineerReviews' => function ($query) {
                $query
                    ->with('customer')
                    ->latest();
            },
        ]);

        $user->loadAvg(
            'receivedEngineerReviews',
            'rating'
        );

        $user->loadCount(
            'receivedEngineerReviews'
        );

        $reviewableConsultation = null;

        if (
            $currentUser
            && (int) $currentUser->id !== (int) $user->id
        ) {
            $reviewableConsultation = Consultation::query()
                ->where(
                    'customer_id',
                    $currentUser->id
                )
                ->where(
                    'engineer_id',
                    $user->id
                )
                ->where(
                    'status',
                    'completed'
                )
                ->where(
                    'payment_status',
                    'paid'
                )
                ->whereDoesntHave('review')
                ->latest()
                ->first();
        }

        return view(
            'engineers.show',
            compact(
                'user',
                'reviewableConsultation'
            )
        );
    }
}
