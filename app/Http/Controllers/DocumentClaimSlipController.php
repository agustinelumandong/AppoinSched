<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentClaimSlipController extends Controller
{
    public function downloadPdf(string $reference_number): Response
    {
        $documentRequest = DocumentRequest::with(['office', 'service', 'user', 'details'])
            ->where('reference_number', $reference_number)
            ->first();

        if (!$documentRequest) {
            abort(404, 'Document request not found');
        }

        // Authorization check: user can only download their own claim slips
        if ($documentRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Only allow download for ready-for-pickup or complete statuses
        if (!in_array($documentRequest->status, ['ready-for-pickup', 'complete'])) {
            abort(403, 'Claim slip is only available for documents ready for pickup or completed');
        }

        // Generate PDF using same method as notification
        $pdf = $this->generateClaimSlipPdf($documentRequest);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="document-claim-slip-' . $reference_number . '.pdf"');
    }

    private function generateClaimSlipPdf(DocumentRequest $documentRequest): string
    {
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

