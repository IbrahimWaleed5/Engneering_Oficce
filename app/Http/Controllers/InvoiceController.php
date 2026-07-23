<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class InvoiceController extends Controller
{
    /**
     * تحميل الفاتورة بصيغة PDF.
     */
    public function download(
        Request $request,
        Invoice $invoice
    ) {
        $this->authorizeInvoice(
            $request,
            $invoice
        );

        $invoice->load([
            'payment',
            'consultation.consultationType',
            'consultation.engineer',
            'customer',
        ]);

        $pdfContent = $this
            ->generatePdf($invoice)
            ->Output(
                $invoice->invoice_number . '.pdf',
                Destination::STRING_RETURN
            );

        return response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',

                'Content-Disposition' =>
                    'attachment; filename="'
                    . $invoice->invoice_number
                    . '.pdf"',

                'Content-Length' =>
                    strlen($pdfContent),
            ]
        );
    }

    /**
     * عرض الفاتورة داخل المتصفح.
     */
    public function show(
        Request $request,
        Invoice $invoice
    ) {
        $this->authorizeInvoice(
            $request,
            $invoice
        );

        $invoice->load([
            'payment',
            'consultation.consultationType',
            'consultation.engineer',
            'customer',
        ]);

        $pdfContent = $this
            ->generatePdf($invoice)
            ->Output(
                $invoice->invoice_number . '.pdf',
                Destination::STRING_RETURN
            );

        return response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',

                'Content-Disposition' =>
                    'inline; filename="'
                    . $invoice->invoice_number
                    . '.pdf"',

                'Content-Length' =>
                    strlen($pdfContent),
            ]
        );
    }

    /**
     * التحقق من صلاحية مشاهدة الفاتورة.
     */
    private function authorizeInvoice(
        Request $request,
        Invoice $invoice
    ): void {
        $user = $request->user();

        abort_unless(
            $user
            && (
                $user->role === 'admin'
                || (int) $invoice->customer_id
                    === (int) $user->id
            ),
            403
        );
    }

    /**
     * إنشاء ملف PDF باستخدام mPDF.
     */
    private function generatePdf(
        Invoice $invoice
    ): Mpdf {
        $tempDirectory = storage_path(
            'app/mpdf-temp'
        );

        File::ensureDirectoryExists(
            $tempDirectory,
            0775,
            true
        );

        $html = view(
            'invoices.pdf',
            compact('invoice')
        )->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans',
            'directionality' => 'rtl',

            'autoScriptToLang' => true,
            'autoLangToFont' => true,

            'tempDir' => $tempDirectory,

            'margin_top' => 12,
            'margin_right' => 12,
            'margin_bottom' => 12,
            'margin_left' => 12,
        ]);

        $mpdf->SetDirectionality('rtl');

        $mpdf->SetTitle(
            'فاتورة ' . $invoice->invoice_number
        );

        $mpdf->SetAuthor(
            $invoice->office_name
                ?? 'مكتب الوليد الهندسي'
        );

        $mpdf->WriteHTML($html);

        return $mpdf;
    }
}
