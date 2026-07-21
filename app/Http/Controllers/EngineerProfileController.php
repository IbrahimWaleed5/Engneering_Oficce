<?php

namespace App\Http\Controllers;

use App\Models\User;

class EngineerProfileController extends Controller
{
    public function show(User $user)
    {
        abort_unless(
            $user->role === 'engineer'
            && $user->status === 'active',
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
        ]);

        return view(
            'engineers.show',
            compact('user')
        );
    }
}
