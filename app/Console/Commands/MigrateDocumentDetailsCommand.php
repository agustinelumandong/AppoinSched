<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\BirthCertificateDetails;
use App\Models\DeathCertificateDetails;
use App\Models\MarriageLicenseDetails;

class MigrateDocumentDetailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:document-details {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing document details from backup table to certificate-specific tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!Schema::hasTable('document_request_details_backup')) {
            $this->error('Backup table document_request_details_backup does not exist. Please run the document details refactoring migration first.');
            return 1;
        }

        $this->info('Starting document details migration...');

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        try {
            DB::beginTransaction();

            // Get all records from backup table
            $backupRecords = DB::table('document_request_details_backup')->get();

            $this->info("Found {$backupRecords->count()} document detail records to migrate");

            $migratedCount = 0;
            $skippedCount = 0;

            foreach ($backupRecords as $record) {
                // Determine certificate type based on the data
                $certificateType = $this->determineCertificateType($record);

                if (!$certificateType) {
                    $skippedCount++;
                    $this->warn("Skipping record ID {$record->id} - unable to determine certificate type");
                    continue;
                }

                // Create certificate-specific details
                $certificateData = $this->extractCertificateData($record, $certificateType);
                $certificateData['document_request_detail_id'] = $record->id;

                if (!$this->option('dry-run')) {
                    match ($certificateType) {
                        'birth' => BirthCertificateDetails::create($certificateData),
                        'death' => DeathCertificateDetails::create($certificateData),
                        'marriage' => MarriageLicenseDetails::create($certificateData),
                    };
                }

                $migratedCount++;
                $this->line("Migrated {$certificateType} certificate details for record ID {$record->id}");
            }

            if (!$this->option('dry-run')) {
                DB::commit();
                $this->info("Migration completed successfully!");
                $this->info("Migrated: {$migratedCount} certificate details");
                $this->info("Skipped: {$skippedCount} records");
            } else {
                DB::rollBack();
                $this->info("DRY RUN completed!");
                $this->info("Would migrate: {$migratedCount} certificate details");
                $this->info("Would skip: {$skippedCount} records");
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Migration failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Determine certificate type based on record data
     */
    private function determineCertificateType(object $record): ?string
    {
        // Check for death certificate indicators
        if (
            !empty($record->deceased_first_name) ||
            !empty($record->deceased_last_name) ||
            !empty($record->death_date) ||
            !empty($record->burial_cemetery_name)
        ) {
            return 'death';
        }

        // Check for marriage certificate indicators
        if (
            !empty($record->groom_first_name) ||
            !empty($record->bride_first_name) ||
            !empty($record->marriage_date) ||
            !empty($record->officiant_name)
        ) {
            return 'marriage';
        }

        // Check for birth certificate indicators
        if (
            !empty($record->hospital_name) ||
            !empty($record->attendant_name) ||
            !empty($record->birth_weight) ||
            !empty($record->birth_place_type)
        ) {
            return 'birth';
        }

        // Default to birth certificate if no specific indicators
        return 'birth';
    }

    /**
     * Extract certificate-specific data from backup record
     */
    private function extractCertificateData(object $record, string $certificateType): array
    {
        return match ($certificateType) {
            'birth' => [
                'hospital_name' => $record->hospital_name ?? null,
                'hospital_address' => $record->hospital_address ?? null,
                'attendant_name' => $record->attendant_name ?? null,
                'attendant_qualification' => $record->attendant_qualification ?? null,
                'attendant_license_no' => $record->attendant_license_no ?? null,
                'attendant_address' => $record->attendant_address ?? null,
                'birth_weight' => $record->birth_weight ?? null,
                'birth_length' => $record->birth_length ?? null,
                'birth_order' => $record->birth_order ?? null,
                'total_children' => $record->total_children ?? null,
                'birth_place_type' => $record->birth_place_type ?? null,
                'birth_notes' => $record->birth_notes ?? null,
            ],
            'death' => [
                'deceased_last_name' => $record->deceased_last_name ?? null,
                'deceased_first_name' => $record->deceased_first_name ?? null,
                'deceased_middle_name' => $record->deceased_middle_name ?? null,
                'deceased_sex' => $record->deceased_sex ?? null,
                'deceased_religion' => $record->deceased_religion ?? null,
                'deceased_age' => $record->deceased_age ?? null,
                'deceased_place_of_birth' => $record->deceased_place_of_birth ?? null,
                'deceased_date_of_birth' => $record->deceased_date_of_birth ?? null,
                'deceased_civil_status' => $record->deceased_civil_status ?? null,
                'deceased_residence' => $record->deceased_residence ?? null,
                'deceased_occupation' => $record->deceased_occupation ?? null,
                'death_date' => $record->death_date ?? null,
                'death_time' => $record->death_time ?? null,
                'death_place' => $record->death_place ?? null,
                'cause_of_death' => $record->cause_of_death ?? null,
                'death_certificate_no' => $record->death_certificate_no ?? null,
                'relationship_to_deceased' => $record->relationship_to_deceased ?? null,
                'deceased_father_last_name' => $record->deceased_father_last_name ?? null,
                'deceased_father_first_name' => $record->deceased_father_first_name ?? null,
                'deceased_father_middle_name' => $record->deceased_father_middle_name ?? null,
                'deceased_mother_last_name' => $record->deceased_mother_last_name ?? null,
                'deceased_mother_first_name' => $record->deceased_mother_first_name ?? null,
                'deceased_mother_middle_name' => $record->deceased_mother_middle_name ?? null,
                'burial_cemetery_name' => $record->burial_cemetery_name ?? null,
                'burial_cemetery_address' => $record->burial_cemetery_address ?? null,
                'burial_date' => $record->burial_date ?? null,
                'burial_time' => $record->burial_time ?? null,
                'informant_name' => $record->informant_name ?? null,
                'informant_address' => $record->informant_address ?? null,
                'informant_relationship' => $record->informant_relationship ?? null,
                'informant_contact_no' => $record->informant_contact_no ?? null,
                'informant_id_type' => $record->informant_id_type ?? null,
                'informant_id_number' => $record->informant_id_number ?? null,
                'attending_physician' => $record->attending_physician ?? null,
                'physician_license_no' => $record->physician_license_no ?? null,
                'physician_address' => $record->physician_address ?? null,
                'certification_date' => $record->certification_date ?? null,
            ],
            'marriage' => [
                'marriage_date' => $record->marriage_date ?? null,
                'marriage_time' => $record->marriage_time ?? null,
                'marriage_place' => $record->marriage_place ?? null,
                'marriage_venue' => $record->marriage_venue ?? null,
                'marriage_venue_address' => $record->marriage_venue_address ?? null,
                'groom_first_name' => $record->groom_first_name ?? null,
                'groom_middle_name' => $record->groom_middle_name ?? null,
                'groom_last_name' => $record->groom_last_name ?? null,
                'groom_suffix' => $record->groom_suffix ?? null,
                'groom_age' => $record->groom_age ?? null,
                'groom_date_of_birth' => $record->groom_date_of_birth ?? null,
                'groom_place_of_birth' => $record->groom_place_of_birth ?? null,
                'groom_sex' => $record->groom_sex ?? null,
                'groom_citizenship' => $record->groom_citizenship ?? null,
                'groom_residence' => $record->groom_residence ?? null,
                'groom_religion' => $record->groom_religion ?? null,
                'groom_civil_status' => $record->groom_civil_status ?? null,
                'groom_father_first_name' => $record->groom_father_first_name ?? null,
                'groom_father_middle_name' => $record->groom_father_middle_name ?? null,
                'groom_father_last_name' => $record->groom_father_last_name ?? null,
                'groom_father_suffix' => $record->groom_father_suffix ?? null,
                'groom_father_citizenship' => $record->groom_father_citizenship ?? null,
                'groom_father_residence' => $record->groom_father_residence ?? null,
                'groom_mother_first_name' => $record->groom_mother_first_name ?? null,
                'groom_mother_middle_name' => $record->groom_mother_middle_name ?? null,
                'groom_mother_last_name' => $record->groom_mother_last_name ?? null,
                'groom_mother_citizenship' => $record->groom_mother_citizenship ?? null,
                'groom_mother_residence' => $record->groom_mother_residence ?? null,
                'bride_first_name' => $record->bride_first_name ?? null,
                'bride_middle_name' => $record->bride_middle_name ?? null,
                'bride_last_name' => $record->bride_last_name ?? null,
                'bride_suffix' => $record->bride_suffix ?? null,
                'bride_age' => $record->bride_age ?? null,
                'bride_date_of_birth' => $record->bride_date_of_birth ?? null,
                'bride_place_of_birth' => $record->bride_place_of_birth ?? null,
                'bride_sex' => $record->bride_sex ?? null,
                'bride_citizenship' => $record->bride_citizenship ?? null,
                'bride_residence' => $record->bride_residence ?? null,
                'bride_religion' => $record->bride_religion ?? null,
                'bride_civil_status' => $record->bride_civil_status ?? null,
                'bride_father_first_name' => $record->bride_father_first_name ?? null,
                'bride_father_middle_name' => $record->bride_father_middle_name ?? null,
                'bride_father_last_name' => $record->bride_father_last_name ?? null,
                'bride_father_suffix' => $record->bride_father_suffix ?? null,
                'bride_father_citizenship' => $record->bride_father_citizenship ?? null,
                'bride_father_residence' => $record->bride_father_residence ?? null,
                'bride_mother_first_name' => $record->bride_mother_first_name ?? null,
                'bride_mother_middle_name' => $record->bride_mother_middle_name ?? null,
                'bride_mother_last_name' => $record->bride_mother_last_name ?? null,
                'bride_mother_citizenship' => $record->bride_mother_citizenship ?? null,
                'bride_mother_residence' => $record->bride_mother_residence ?? null,
                'establishment_name' => $record->establishment_name ?? null,
                'establishment_address' => $record->establishment_address ?? null,
                'establishment_purpose' => $record->establishment_purpose ?? null,
                'consent_person' => $record->consent_person ?? null,
                'consent_relationship' => $record->consent_relationship ?? null,
                'consent_citizenship' => $record->consent_citizenship ?? null,
                'consent_residence' => $record->consent_residence ?? null,
                'license_number' => $record->license_number ?? null,
                'license_issued_date' => $record->license_issued_date ?? null,
                'license_issued_place' => $record->license_issued_place ?? null,
                'witness1_name' => $record->witness1_name ?? null,
                'witness1_address' => $record->witness1_address ?? null,
                'witness2_name' => $record->witness2_name ?? null,
                'witness2_address' => $record->witness2_address ?? null,
                'officiant_name' => $record->officiant_name ?? null,
                'officiant_title' => $record->officiant_title ?? null,
                'officiant_license_no' => $record->officiant_license_no ?? null,
                'officiant_address' => $record->officiant_address ?? null,
            ],
        };
    }
}
