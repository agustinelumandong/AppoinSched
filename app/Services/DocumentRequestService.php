<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\DocumentRequestDetails;
use App\Models\BirthCertificateDetails;
use App\Models\DeathCertificateDetails;
use App\Models\MarriageLicenseDetails;
use Illuminate\Support\Facades\DB;

class DocumentRequestService
{
    /**
     * Create a new document request with appropriate certificate details
     */
    public function createDocumentRequest(array $requestData, array $detailsData, ?string $certificateType = null): DocumentRequest
    {
        return DB::transaction(function () use ($requestData, $detailsData, $certificateType) {
            // Create the main document request
            $documentRequest = DocumentRequest::create($requestData);

            // Create the document request details
            $detailsData['document_request_id'] = $documentRequest->id;
            $detailsData['certificate_type'] = $certificateType;
            $documentRequestDetails = DocumentRequestDetails::create($detailsData);

            // Create certificate-specific details based on type
            if ($certificateType) {
                $this->createCertificateSpecificDetails($documentRequestDetails, $certificateType, $detailsData);
            }

            return $documentRequest->load('details');
        });
    }

    /**
     * Update a document request and its related details
     */
    public function updateDocumentRequest(DocumentRequest $documentRequest, array $requestData, array $detailsData): DocumentRequest
    {
        return DB::transaction(function () use ($documentRequest, $requestData, $detailsData) {
            // Update the main document request
            $documentRequest->update($requestData);

            // Update or create document request details
            $documentRequestDetails = $documentRequest->details;
            if ($documentRequestDetails) {
                $documentRequestDetails->update($detailsData);
            } else {
                $detailsData['document_request_id'] = $documentRequest->id;
                $documentRequestDetails = DocumentRequestDetails::create($detailsData);
            }

            // Update certificate-specific details if they exist
            if ($documentRequestDetails->certificate_type) {
                $this->updateCertificateSpecificDetails($documentRequestDetails, $documentRequestDetails->certificate_type, $detailsData);
            }

            return $documentRequest->load('details');
        });
    }

    /**
     * Get certificate-specific details based on type
     */
    public function getCertificateDetails(DocumentRequestDetails $documentRequestDetails): ?object
    {
        return match ($documentRequestDetails->certificate_type) {
            'birth' => $documentRequestDetails->birthCertificateDetails,
            'death' => $documentRequestDetails->deathCertificateDetails,
            'marriage' => $documentRequestDetails->marriageLicenseDetails,
            default => null,
        };
    }

    /**
     * Create certificate-specific details
     */
    private function createCertificateSpecificDetails(DocumentRequestDetails $documentRequestDetails, string $certificateType, array $detailsData): void
    {
        $certificateData = $this->extractCertificateSpecificData($detailsData, $certificateType);
        $certificateData['document_request_detail_id'] = $documentRequestDetails->id;

        match ($certificateType) {
            'birth' => BirthCertificateDetails::create($certificateData),
            'death' => DeathCertificateDetails::create($certificateData),
            'marriage' => MarriageLicenseDetails::create($certificateData),
        };
    }

    /**
     * Update certificate-specific details
     */
    private function updateCertificateSpecificDetails(DocumentRequestDetails $documentRequestDetails, string $certificateType, array $detailsData): void
    {
        $certificateData = $this->extractCertificateSpecificData($detailsData, $certificateType);

        $certificateDetails = match ($certificateType) {
            'birth' => $documentRequestDetails->birthCertificateDetails,
            'death' => $documentRequestDetails->deathCertificateDetails,
            'marriage' => $documentRequestDetails->marriageLicenseDetails,
        };

        if ($certificateDetails) {
            $certificateDetails->update($certificateData);
        } else {
            $certificateData['document_request_detail_id'] = $documentRequestDetails->id;
            match ($certificateType) {
                'birth' => BirthCertificateDetails::create($certificateData),
                'death' => DeathCertificateDetails::create($certificateData),
                'marriage' => MarriageLicenseDetails::create($certificateData),
            };
        }
    }

    /**
     * Extract certificate-specific data from the full details data
     */
    private function extractCertificateSpecificData(array $detailsData, string $certificateType): array
    {
        return match ($certificateType) {
            'birth' => array_intersect_key($detailsData, array_flip([
                'hospital_name',
                'hospital_address',
                'attendant_name',
                'attendant_qualification',
                'attendant_license_no',
                'attendant_address',
                'birth_weight',
                'birth_length',
                'birth_order',
                'total_children',
                'birth_place_type',
                'birth_notes'
            ])),
            'death' => array_intersect_key($detailsData, array_flip([
                'deceased_last_name',
                'deceased_first_name',
                'deceased_middle_name',
                'deceased_sex',
                'deceased_religion',
                'deceased_age',
                'deceased_place_of_birth',
                'deceased_date_of_birth',
                'deceased_civil_status',
                'deceased_residence',
                'deceased_occupation',
                'death_date',
                'death_time',
                'death_place',
                'cause_of_death',
                'death_certificate_no',
                'relationship_to_deceased',
                'deceased_father_last_name',
                'deceased_father_first_name',
                'deceased_father_middle_name',
                'deceased_mother_last_name',
                'deceased_mother_first_name',
                'deceased_mother_middle_name',
                'burial_cemetery_name',
                'burial_cemetery_address',
                'burial_date',
                'burial_time',
                'informant_name',
                'informant_address',
                'informant_relationship',
                'informant_contact_no',
                'informant_id_type',
                'informant_id_number',
                'attending_physician',
                'physician_license_no',
                'physician_address',
                'certification_date'
            ])),
            'marriage' => array_intersect_key($detailsData, array_flip([
                'marriage_date',
                'marriage_time',
                'marriage_place',
                'marriage_venue',
                'marriage_venue_address',
                'groom_first_name',
                'groom_middle_name',
                'groom_last_name',
                'groom_suffix',
                'groom_age',
                'groom_date_of_birth',
                'groom_place_of_birth',
                'groom_sex',
                'groom_citizenship',
                'groom_residence',
                'groom_religion',
                'groom_civil_status',
                'groom_father_first_name',
                'groom_father_middle_name',
                'groom_father_last_name',
                'groom_father_suffix',
                'groom_father_citizenship',
                'groom_father_residence',
                'groom_mother_first_name',
                'groom_mother_middle_name',
                'groom_mother_last_name',
                'groom_mother_citizenship',
                'groom_mother_residence',
                'bride_first_name',
                'bride_middle_name',
                'bride_last_name',
                'bride_suffix',
                'bride_age',
                'bride_date_of_birth',
                'bride_place_of_birth',
                'bride_sex',
                'bride_citizenship',
                'bride_residence',
                'bride_religion',
                'bride_civil_status',
                'bride_father_first_name',
                'bride_father_middle_name',
                'bride_father_last_name',
                'bride_father_suffix',
                'bride_father_citizenship',
                'bride_father_residence',
                'bride_mother_first_name',
                'bride_mother_middle_name',
                'bride_mother_last_name',
                'bride_mother_citizenship',
                'bride_mother_residence',
                'establishment_name',
                'establishment_address',
                'establishment_purpose',
                'consent_person',
                'consent_relationship',
                'consent_citizenship',
                'consent_residence',
                'license_number',
                'license_issued_date',
                'license_issued_place',
                'witness1_name',
                'witness1_address',
                'witness2_name',
                'witness2_address',
                'officiant_name',
                'officiant_title',
                'officiant_license_no',
                'officiant_address'
            ])),
        };
    }

    /**
     * Get all document requests with their details and certificate-specific information
     */
    public function getDocumentRequestsWithDetails(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = DocumentRequest::with(['details', 'user', 'office', 'service']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['office_id'])) {
            $query->where('office_id', $filters['office_id']);
        }

        if (isset($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Get document request statistics
     */
    public function getDocumentRequestStatistics(): array
    {
        return [
            'total' => DocumentRequest::count(),
            'pending' => DocumentRequest::where('status', 'pending')->count(),
            'approved' => DocumentRequest::where('status', 'approved')->count(),
            'completed' => DocumentRequest::where('status', 'completed')->count(),
            'rejected' => DocumentRequest::where('status', 'rejected')->count(),
            'by_certificate_type' => DocumentRequestDetails::selectRaw('certificate_type, COUNT(*) as count')
                ->groupBy('certificate_type')
                ->pluck('count', 'certificate_type')
                ->toArray(),
        ];
    }
}
