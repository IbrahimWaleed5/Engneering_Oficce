<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SecureFileController extends Controller
{
    public function consultationCustomerFile(
        Consultation $consultation
    ): BinaryFileResponse {
        $this->authorize('downloadCustomerFile', $consultation);

        abort_unless($consultation->customer_file, 404);

        return $this->downloadStoredFile(
            $consultation->customer_file,
            'customer-file-' . $consultation->consultation_number
        );
    }

    public function consultationEngineerFile(
        Consultation $consultation
    ): BinaryFileResponse {
        $this->authorize('downloadEngineerFile', $consultation);

        abort_unless($consultation->engineer_file, 404);

        return $this->downloadStoredFile(
            $consultation->engineer_file,
            'engineer-file-' . $consultation->consultation_number
        );
    }

    public function messageAttachment(
        Consultation $consultation,
        ConsultationMessage $message
    ): BinaryFileResponse {
        abort_unless(
            (int) $message->consultation_id === (int) $consultation->id,
            404
        );

        $this->authorize('viewConversation', $consultation);

        abort_unless($message->attachment, 404);

        return $this->downloadStoredFile(
            $message->attachment,
            'message-attachment-' . $message->id
        );
    }

    public function paymentReceipt(
        Payment $payment
    ): BinaryFileResponse {
        $this->authorize('downloadReceipt', $payment);

        abort_unless($payment->receipt_image, 404);

        return $this->downloadStoredFile(
            $payment->receipt_image,
            'payment-receipt-' . $payment->id
        );
    }

    private function downloadStoredFile(
        string $path,
        string $downloadName
    ): BinaryFileResponse {
        foreach (['private', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);

            if (! $disk->exists($path)) {
                continue;
            }

            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $safeName = $downloadName
                . ($extension ? '.' . strtolower($extension) : '');

            return response()->download(
                $disk->path($path),
                $safeName,
                [
                    'X-Content-Type-Options' => 'nosniff',
                    'Cache-Control' => 'private, no-store, max-age=0',
                    'Pragma' => 'no-cache',
                ]
            );
        }

        abort(404);
    }
}
