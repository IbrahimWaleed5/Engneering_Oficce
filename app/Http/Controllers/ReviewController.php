<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * عرض جميع التقييمات للإدارة.
     */
    public function index(Request $request): View
    {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        $reviews = Review::with([
            'customer',
            'consultation',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'reviews.index',
            compact('reviews')
        );
    }

    /**
     * صفحة إضافة تقييم.
     */
    public function create(
        Request $request,
        Consultation $consultation
    ): View|RedirectResponse {
        abort_unless(
            $consultation->customer_id === $request->user()->id,
            403
        );

        if ($consultation->status !== 'completed') {
            return back()->with(
                'error',
                'يمكنك تقييم الاستشارة بعد اكتمالها فقط.'
            );
        }

        if ($consultation->review()->exists()) {
            return back()->with(
                'error',
                'لقد أضفت تقييمًا لهذه الاستشارة سابقًا.'
            );
        }

        return view(
            'reviews.create',
            compact('consultation')
        );
    }

    /**
     * حفظ تقييم العميل.
     */
    public function store(
        Request $request,
        Consultation $consultation
    ): RedirectResponse {
        abort_unless(
            $consultation->customer_id === $request->user()->id,
            403
        );

        if ($consultation->status !== 'completed') {
            return back()->with(
                'error',
                'لا يمكن تقييم استشارة غير مكتملة.'
            );
        }

        if ($consultation->review()->exists()) {
            return back()->with(
                'error',
                'لقد قيّمت هذه الاستشارة سابقًا.'
            );
        }

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'comment' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ], [
            'rating.required' => 'يرجى اختيار عدد النجوم.',
            'rating.between' => 'التقييم يجب أن يكون من نجمة إلى 5 نجوم.',
            'comment.required' => 'يرجى كتابة رأيك أو ملاحظتك.',
            'comment.min' => 'يجب ألا يقل التعليق عن 5 أحرف.',
            'comment.max' => 'يجب ألا يزيد التعليق عن 2000 حرف.',
        ]);

        Review::create([
            'consultation_id' => $consultation->id,
            'customer_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
            'is_featured' => false,
        ]);

        return redirect()
            ->route('consultations.my')
            ->with(
                'success',
                'شكرًا لك، تم إرسال تقييمك إلى الإدارة للمراجعة.'
            );
    }

    /**
     * الموافقة على التقييم.
     */
    public function approve(
        Request $request,
        Review $review
    ): RedirectResponse {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        $review->update([
            'status' => 'approved',
        ]);

        return back()->with(
            'success',
            'تمت الموافقة على التقييم.'
        );
    }

    /**
     * رفض التقييم.
     */
    public function reject(
        Request $request,
        Review $review
    ): RedirectResponse {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        $review->update([
            'status' => 'rejected',
            'is_featured' => false,
        ]);

        return back()->with(
            'success',
            'تم رفض التقييم.'
        );
    }

    /**
     * إظهار أو إخفاء التقييم في الصفحة الرئيسية.
     */
    public function toggleFeatured(
        Request $request,
        Review $review
    ): RedirectResponse {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        if ($review->status !== 'approved') {
            return back()->with(
                'error',
                'يجب الموافقة على التقييم أولًا.'
            );
        }

        $review->update([
            'is_featured' => !$review->is_featured,
        ]);

        return back()->with(
            'success',
            $review->is_featured
                ? 'تم إظهار التقييم في الموقع.'
                : 'تم إخفاء التقييم من الموقع.'
        );
    }

    /**
     * حذف التقييم.
     */
    public function destroy(
        Request $request,
        Review $review
    ): RedirectResponse {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        $review->delete();

        return back()->with(
            'success',
            'تم حذف التقييم.'
        );
    }
}
