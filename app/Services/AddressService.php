<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserAddresses;
use App\Models\PersonalInformation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressService
{
    /**
     * Create or update an address for a user
     */
    public function createOrUpdateAddress(int $personalInformationId, array $addressData, ?int $addressId = null): UserAddresses
    {
        return DB::transaction(function () use ($personalInformationId, $addressData, $addressId) {
            $addressData['personal_information_id'] = $personalInformationId;

            if ($addressId) {
                $address = UserAddresses::findOrFail($addressId);
                $address->update($addressData);
            } else {
                $address = UserAddresses::create($addressData);
            }

            // If this is set as primary, unset other primary addresses
            if ($address->is_primary) {
                $this->setAsPrimary($address);
            }

            return $address;
        });
    }

    /**
     * Get addresses by type for a personal information record
     */
    public function getAddressesByType(int $personalInformationId, string $addressType): Collection
    {
        return UserAddresses::where('personal_information_id', $personalInformationId)
            ->where('address_type', $addressType)
            ->get();
    }

    /**
     * Get all addresses for a personal information record
     */
    public function getAllAddresses(int $personalInformationId): Collection
    {
        return UserAddresses::where('personal_information_id', $personalInformationId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get the primary address for a personal information record
     */
    public function getPrimaryAddress(int $personalInformationId): ?UserAddresses
    {
        return UserAddresses::where('personal_information_id', $personalInformationId)
            ->where('is_primary', true)
            ->first();
    }

    /**
     * Set an address as primary and unset others
     */
    public function setAsPrimary(UserAddresses $address): void
    {
        DB::transaction(function () use ($address) {
            // Unset other primary addresses for this personal information
            UserAddresses::where('personal_information_id', $address->personal_information_id)
                ->where('id', '!=', $address->id)
                ->update(['is_primary' => false]);

            // Set this address as primary
            $address->update(['is_primary' => true]);
        });
    }

    /**
     * Delete an address
     */
    public function deleteAddress(int $addressId): bool
    {
        $address = UserAddresses::findOrFail($addressId);

        return DB::transaction(function () use ($address) {
            $wasPrimary = $address->is_primary;
            $personalInformationId = $address->personal_information_id;

            $deleted = $address->delete();

            // If the deleted address was primary, set another address as primary
            if ($wasPrimary && $deleted) {
                $this->setNextPrimaryAddress($personalInformationId);
            }

            return $deleted;
        });
    }

    /**
     * Set the next available address as primary
     */
    private function setNextPrimaryAddress(int $personalInformationId): void
    {
        $nextAddress = UserAddresses::where('personal_information_id', $personalInformationId)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($nextAddress) {
            $nextAddress->update(['is_primary' => true]);
        }
    }

    /**
     * Get formatted address string
     */
    public function getFormattedAddress(UserAddresses $address): string
    {
        return $address->complete_address;
    }

    /**
     * Get short address (city, province only)
     */
    public function getShortAddress(UserAddresses $address): string
    {
        return $address->short_address;
    }

    /**
     * Validate address data
     */
    public function validateAddressData(array $addressData): array
    {
        $errors = [];

        // Required fields validation
        if (empty($addressData['address_line_1'])) {
            $errors[] = 'Address line 1 is required';
        }

        if (empty($addressData['city'])) {
            $errors[] = 'City is required';
        }

        if (empty($addressData['province'])) {
            $errors[] = 'Province is required';
        }

        if (empty($addressData['region'])) {
            $errors[] = 'Region is required';
        }

        // Address type validation
        if (!in_array($addressData['address_type'] ?? '', ['Permanent', 'Temporary'])) {
            $errors[] = 'Invalid address type';
        }

        return $errors;
    }

    /**
     * Get address statistics for a personal information record
     */
    public function getAddressStatistics(int $personalInformationId): array
    {
        $addresses = UserAddresses::where('personal_information_id', $personalInformationId)->get();

        return [
            'total' => $addresses->count(),
            'permanent' => $addresses->where('address_type', 'Permanent')->count(),
            'temporary' => $addresses->where('address_type', 'Temporary')->count(),
            'has_primary' => $addresses->where('is_primary', true)->count() > 0,
        ];
    }

    /**
     * Check if user has complete address information
     */
    public function hasCompleteAddress(int $personalInformationId): bool
    {
        $primaryAddress = $this->getPrimaryAddress($personalInformationId);

        if (!$primaryAddress) {
            return false;
        }

        return !empty($primaryAddress->address_line_1) &&
            !empty($primaryAddress->region) &&
            !empty($primaryAddress->province) &&
            !empty($primaryAddress->city) &&
            !empty($primaryAddress->barangay) &&
            !empty($primaryAddress->zip_code);
    }
}
