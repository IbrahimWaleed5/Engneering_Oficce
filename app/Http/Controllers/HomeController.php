<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Consultation;
use App\Models\EngineerWork;

class HomeController extends Controller
{
    public function index()
    {
        $statistics = [
            'engineers' => User::query()
                ->where('role', 'engineer')
                ->where('status', 'active')
                ->count(),

            'consultations' => Consultation::query()
                ->where('payment_status', 'paid')
                ->count(),

            'completed' => Consultation::query()
                ->where('status', 'completed')
                ->count(),

            'works' => EngineerWork::query()
                ->where('status', 'approved')
                ->count(),
        ];

        $latestWorks = EngineerWork::with([
            'engineer',
            'coverImage',
        ])
            ->where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        $featuredEngineers = User::query()
            ->with([
                'engineerWorks' => function ($query) {
                    $query
                        ->where('status', 'approved')
                        ->latest();
                },
            ])
            ->where('role', 'engineer')
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get();

        return view(
            'welcome',
            compact(
                'statistics',
                'latestWorks',
                'featuredEngineers'
            )
        );
    }
}
