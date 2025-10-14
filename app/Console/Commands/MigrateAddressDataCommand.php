<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAddressDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:address-data {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing address data from backup table to new normalized structure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!Schema::hasTable('user_addresses_backup')) {
            $this->error('Backup table user_addresses_backup does not exist. Please run the address normalization migration first.');
            return 1;
        }

        if (!Schema::hasTable('user_addresses')) {
            $this->error('New user_addresses table does not exist. Please run the address normalization migration first.');
            return 1;
        }

        $this->info('Starting address data migration...');

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        try {
            DB::beginTransaction();

            // Get all records from backup table
            $backupRecords = DB::table('user_addresses_backup')->get();

            $this->info("Found {$backupRecords->count()} address records to migrate");

            $migratedCount = 0;
            $skippedCount = 0;

            foreach ($backupRecords as $record) {
                $addresses = [];

                // Create permanent address if it has data
                if ($this->hasAddressData($record, false)) {
                    $addresses[] = [
                        'personal_information_id' => $record->personal_information_id,
                        'address_type' => 'Permanent',
                        'address_line_1' => $record->address_line_1,
                        'address_line_2' => $record->address_line_2,
                        'region' => $record->region,
                        'province' => $record->province,
                        'city' => $record->city,
                        'barangay' => $record->barangay,
                        'street' => $record->street,
                        'zip_code' => $record->zip_code,
                        'is_primary' => true, // First address is primary
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ];
                }

                // Create temporary address if it has data
                if ($this->hasAddressData($record, true)) {
                    $addresses[] = [
                        'personal_information_id' => $record->personal_information_id,
                        'address_type' => 'Temporary',
                        'address_line_1' => $record->temporary_address_line_1,
                        'address_line_2' => $record->temporary_address_line_2,
                        'region' => $record->temporary_region,
                        'province' => $record->temporary_province,
                        'city' => $record->temporary_city,
                        'barangay' => $record->temporary_barangay,
                        'street' => $record->temporary_street,
                        'zip_code' => $record->temporary_zip_code,
                        'is_primary' => empty($addresses), // Primary if no permanent address
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ];
                }

                if (empty($addresses)) {
                    $skippedCount++;
                    $this->warn("Skipping record ID {$record->id} - no address data found");
                    continue;
                }

                if (!$this->option('dry-run')) {
                    // Insert addresses
                    foreach ($addresses as $address) {
                        DB::table('user_addresses')->insert($address);
                    }
                }

                $migratedCount += count($addresses);
                $this->line("Migrated {$addresses} address(es) for personal_information_id {$record->personal_information_id}");
            }

            if (!$this->option('dry-run')) {
                DB::commit();
                $this->info("Migration completed successfully!");
                $this->info("Migrated: {$migratedCount} addresses");
                $this->info("Skipped: {$skippedCount} records");
            } else {
                DB::rollBack();
                $this->info("DRY RUN completed!");
                $this->info("Would migrate: {$migratedCount} addresses");
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
     * Check if a record has address data (permanent or temporary)
     */
    private function hasAddressData(object $record, bool $isTemporary): bool
    {
        $prefix = $isTemporary ? 'temporary_' : '';

        return !empty($record->{$prefix . 'address_line_1'}) ||
            !empty($record->{$prefix . 'region'}) ||
            !empty($record->{$prefix . 'province'}) ||
            !empty($record->{$prefix . 'city'}) ||
            !empty($record->{$prefix . 'barangay'});
    }
}
