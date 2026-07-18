<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\EngineerWork;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'engineer' => $this->engineerDashboard($user),
            'customer' => $this->customerDashboard($user),
            'employee' => $this->employeeDashboard(),
            default => abort(403),
        };
    }

    private function adminDashboard()
    {
        $statistics = [
            'users' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'engineers' => User::where('role', 'engineer')->count(),
            'employees' => User::where('role', 'employee')->count(),

            'consultations' => Consultation::count(),

            'pending_consultations' => Consultation::where(
                'status',
                'pending'
            )->count(),

            'in_progress_consultations' => Consultation::where(
                'status',
                'in_progress'
            )->count(),

            'completed_consultations' => Consultation::where(
                'status',
                'completed'
            )->count(),

            'pending_payments' => Payment::where(
                'status',
                'pending'
            )->count(),

            'total_revenue' => Payment::whereIn(
                'status',
                ['paid', 'completed']
            )->sum('amount'),
        ];

        $latestConsultations = Consultation::with([
            'customer',
            'engineer',
            'consultationType',
        ])
            ->latest()
            ->take(6)
            ->get();

        $consultationsByStatus = [
            'waiting_payment' => Consultation::where(
                'status',
                'waiting_payment'
            )->count(),

            'pending' => Consultation::where(
                'status',
                'pending'
            )->count(),

            'in_progress' => Consultation::where(
                'status',
                'in_progress'
            )->count(),

            'completed' => Consultation::where(
                'status',
                'completed'
            )->count(),

            'cancelled' => Consultation::where(
                'status',
                'cancelled'
            )->count(),
        ];

        return view('dashboard', compact(
            'statistics',
            'latestConsultations',
            'consultationsByStatus'
        ));
    }

    private function engineerDashboard(User $user)
    {
        $baseQuery = Consultation::where(
            'engineer_id',
            $user->id
        )->where('payment_status', 'paid');

        $statistics = [
            'all' => (clone $baseQuery)->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'in_progress' => (clone $baseQuery)
                ->where('status', 'in_progress')
                ->count(),

            'completed' => (clone $baseQuery)
                ->where('status', 'completed')
                ->count(),

            'works' => EngineerWork::where(
                'engineer_id',
                $user->id
            )->count(),

            'approved_works' => EngineerWork::where(
                'engineer_id',
                $user->id
            )
                ->where('status', 'approved')
                ->count(),

            'unread_notifications' => $user
                ->unreadNotifications()
                ->count(),
        ];

        $latestConsultations = Consultation::with([
            'customer',
            'consultationType',
        ])
            ->where('engineer_id', $user->id)
            ->where('payment_status', 'paid')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'statistics',
            'latestConsultations'
        ));
    }

    private function customerDashboard(User $user)
    {
        $baseQuery = Consultation::where(
            'customer_id',
            $user->id
        );

        $statistics = [
            'all' => (clone $baseQuery)->count(),

            'waiting_payment' => (clone $baseQuery)
                ->where('status', 'waiting_payment')
                ->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'in_progress' => (clone $baseQuery)
                ->where('status', 'in_progress')
                ->count(),

            'completed' => (clone $baseQuery)
                ->where('status', 'completed')
                ->count(),

            'unread_notifications' => $user
                ->unreadNotifications()
                ->count(),
        ];

        $latestConsultations = Consultation::with([
            'engineer',
            'consultationType',
        ])
            ->where('customer_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'statistics',
            'latestConsultations'
        ));
    }

    private function employeeDashboard()
    {
        $statistics = [
            'consultations' => Consultation::count(),

            'pending_consultations' => Consultation::where(
                'status',
                'pending'
            )->count(),

            'in_progress_consultations' => Consultation::where(
                'status',
                'in_progress'
            )->count(),

            'pending_payments' => Payment::where(
                'status',
                'pending'
            )->count(),

            'customers' => User::where(
                'role',
                'customer'
            )->count(),

            'engineers' => User::where(
                'role',
                'engineer'
            )->where('status', 'active')
                ->count(),
        ];

        $latestConsultations = Consultation::with([
            'customer',
            'engineer',
            'consultationType',
        ])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'statistics',
            'latestConsultations'
        ));
    }
}
