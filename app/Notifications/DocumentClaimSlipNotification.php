<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DocumentRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentClaimSlipNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public DocumentRequest $documentRequest
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Generate PDF for the claim slip
        $pdf = $this->generateClaimSlipPdf();

        $mailMessage = (new MailMessage)
            ->subject('Your Document Request Claim Slip - ' . $this->documentRequest->reference_number)
            ->greeting('Hello ' . $this->documentRequest->user->name . '!')
            ->line('Your document request is ready for pickup.')
            ->line('Please find your claim slip attached to this email.')
            ->line('Important: Please bring this claim slip and all required documents when claiming your document.')
            ->action('View Document Request', route('client.documents'))
            ->line('Thank you for choosing our services!')
            ->attachData($pdf, 'document-claim-slip-' . $this->documentRequest->reference_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);

        return $mailMessage;
    }

    private function generateClaimSlipPdf(): string
    {
        $documentRequest = $this->documentRequest;
        $details = $documentRequest->details;

        // Get requester name (the user who made the request)
        $requestorName = $documentRequest->user->name;

        // Determine request type and document owner name
        $requestFor = $details->request_for ?? 'myself';
        $documentOwnerName = '';

        if ($requestFor === 'myself') {
            // If request is for the owner themselves, use details name
            $middleName = strtolower($details->middle_name ?? '') !== 'n/a' && !empty(trim($details->middle_name ?? '')) ? $details->middle_name : null;
            $nameParts = array_filter([$details->first_name, $middleName], function ($part) {
                return !empty($part) && strtolower(trim($part)) !== 'n/a';
            });
            $documentOwnerName = trim(implode(' ', $nameParts)) . ' ' . $details->last_name;
        } else {
            // If request is for someone else, use the details name as document owner
            $middleName = strtolower($details->middle_name ?? '') !== 'n/a' && !empty(trim($details->middle_name ?? '')) ? $details->middle_name : null;
            $nameParts = array_filter([$details->first_name, $middleName], function ($part) {
                return !empty($part) && strtolower(trim($part)) !== 'n/a';
            });
            $documentOwnerName = trim(implode(' ', $nameParts)) . ' ' . $details->last_name;
        }

        $data = [
            'reference_number' => $documentRequest->reference_number,
            'office' => $documentRequest->office,
            'service' => $documentRequest->service,
            'requestor_name' => $requestorName,
            'document_owner_name' => $documentOwnerName ?: $requestorName,
            'request_for' => $requestFor,
            'requested_date' => $documentRequest->requested_date,
        ];

        $pdf = Pdf::loadView('emails.document-claim-slip-pdf', $data);
        return $pdf->output();
    }
}

