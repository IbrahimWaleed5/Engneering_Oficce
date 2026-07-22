<?php

namespace App\Http\Controllers;

use App\Models\User;

class EngineerProfileController extends Controller
{
    public function show(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | السماح بعرض المهندس النشط فقط
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $user->role === 'engineer'
            && $user->status === 'active'
            && $user->hasActiveEngineerMembership(),
            404
        );

        /*
        |--------------------------------------------------------------------------
        | تحميل بيانات المهندس والأعمال والتقييمات
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | حساب متوسط التقييم وعدد التقييمات
        |--------------------------------------------------------------------------
        */

        $user->loadAvg(
            'receivedEngineerReviews',
            'rating'
        );

        $user->loadCount(
            'receivedEngineerReviews'
        );

        return view(
            'engineers.show',
            compact('user')
        );
    }
}
