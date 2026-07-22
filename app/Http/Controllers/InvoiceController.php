<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * تحميل الفاتورة بصيغة PDF.
     */
    public function download(
        Request $request,
        Invoice $invoice
    ) {
        $invoice->load([
            'payment',
            'consultation.consultationType',
            'consultation.engineer',
            'customer',
        ]);

        $user = $request->user();

        abort_unless(
            $user->role === 'admin'
            || (int) $invoice->customer_id
                === (int) $user->id,
            403
        );

        $pdf = Pdf::loadView(
            'invoices.pdf',
            compact('invoice')
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            $invoice->invoice_number . '.pdf'
        );
    }

    /**
     * عرض الفاتورة داخل المتصفح.
     */
    public function show(
        Request $request,
        Invoice $invoice
    ) {
        $invoice->load([
            'payment',
            'consultation.consultationType',
            'consultation.engineer',
            'customer',
        ]);

        $user = $request->user();

        abort_unless(
            $user->role === 'admin'
            || (int) $invoice->customer_id
                === (int) $user->id,
            403
        );

        $pdf = Pdf::loadView(
            'invoices.pdf',
            compact('invoice')
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream(
            $invoice->invoice_number . '.pdf'
        );
    }
}
