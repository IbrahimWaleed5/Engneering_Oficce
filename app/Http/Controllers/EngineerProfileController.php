<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;

class EngineerProfileController extends Controller
{
    public function show(User $user)
    {
        abort_unless(
            $user->role === 'engineer'
            && $user->status === 'active'
            && $user->hasActiveEngineerMembership(),
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
            auth()->check()
            && auth()->id() !== $user->id
        ) {
            $reviewableConsultation = Consultation::query()
                ->where(
                    'customer_id',
                    auth()->id()
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
