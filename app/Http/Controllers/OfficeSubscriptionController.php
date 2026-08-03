<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeSubscriptionRequest;
use App\Models\OfficeSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OfficeSubscriptionController extends Controller
{
    public function show(): View
    {
        $user = request()->user();
        $office = $user->ownedOffice;

        abort_unless(
            $office !== null,
            404,
            'لا يوجد مكتب مرتبط بهذا الحساب.'
        );

        $subscriptions = $office
            ->subscriptions()
            ->latest()
            ->paginate(15);

        $latestSubscription = $office
            ->subscriptions()
            ->latest()
            ->first();

        return view(
            'office.subscription',
            compact(
                'office',
                'subscriptions',
                'latestSubscription'
            )
        );
    }

    public function store(
        StoreOfficeSubscriptionRequest $request
    ): RedirectResponse {
        $user = $request->user();
        $office = $user->ownedOffice;

        abort_unless(
            $office !== null,
            404,
            'لا يوجد مكتب مرتبط بهذا الحساب.'
        );

        if (
            in_array(
                $office->status,
                ['closed', 'rejected'],
                true
            )
        ) {
            return back()->with(
                'error',
                'لا يمكن دفع اشتراك مكتب مغلق أو مرفوض.'
            );
        }

        $hasSubscriptionUnderReview = $office
            ->subscriptions()
            ->where('status', 'under_review')
            ->exists();

        if ($hasSubscriptionUnderReview) {
            return back()->with(
                'error',
                'يوجد إيصال اشتراك قيد المراجعة بالفعل.'
            );
        }

        if (
            $office->subscription_status === 'active'
            && $office->subscription_ends_at?->isFuture()
        ) {
            return back()->with(
                'error',
                'اشتراك المكتب ما زال فعالًا حتى '
                . $office->subscription_ends_at->format('Y-m-d')
                . '.'
            );
        }

        $receiptPath = $request
            ->file('receipt')
            ->store(
                'office-subscriptions/'
                . $office->id
                . '/receipts'
            );

        try {
            $pendingSubscription = $office
                ->subscriptions()
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingSubscription) {
                $pendingSubscription->update([
                    'amount' =>
                        $office->monthly_subscription_amount,

                    'currency' =>
                        $office->subscription_currency,

                    'status' => 'under_review',

                    'payment_method' =>
                        $request->validated(
                            'payment_method'
                        ),

                    'payment_reference' =>
                        $request->validated(
                            'payment_reference'
                        ),

                    'receipt_path' => $receiptPath,

                    'paid_at' => now(),

                    'notes' =>
                        $request->validated('notes'),
                ]);
            } else {
                OfficeSubscription::create([
                    'office_id' => $office->id,

                    'amount' =>
                        $office->monthly_subscription_amount,

                    'currency' =>
                        $office->subscription_currency,

                    'status' => 'under_review',

                    'payment_method' =>
                        $request->validated(
                            'payment_method'
                        ),

                    'payment_reference' =>
                        $request->validated(
                            'payment_reference'
                        ),

                    'receipt_path' => $receiptPath,

                    'paid_at' => now(),

                    'notes' =>
                        $request->validated('notes'),
                ]);
            }

            $office->update([
                'subscription_status' => 'pending',
            ]);
        } catch (\Throwable $exception) {
            Storage::delete($receiptPath);

            throw $exception;
        }

        return redirect()
            ->route('office.subscription')
            ->with(
                'success',
                'تم رفع إيصال الاشتراك وإرساله لمدير النظام للمراجعة.'
            );
    }
}
