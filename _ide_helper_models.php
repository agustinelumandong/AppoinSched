<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $appointment_id
 * @property string|null $request_for
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $purpose
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Appointments $appointment
 * @property-read string $complete_address
 * @property-read string $full_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereRequestFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentDetails whereUpdatedAt($value)
 */
	class AppointmentDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $office_id
 * @property \Illuminate\Support\Carbon $booking_date
 * @property string $booking_time
 * @property string|null $reference_number
 * @property string|null $purpose
 * @property string|null $notes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppointmentDetails|null $appointmentDetails
 * @property-read \App\Models\AppointmentDetails|null $details
 * @property-read \App\Models\Offices $office
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\AppointmentsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereUserId($value)
 */
	class Appointments extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\DocumentRequestDetails|null $documentRequestDetail
 * @property-read string $attendant_full_name
 * @property-read string $formatted_birth_length
 * @property-read string $formatted_birth_weight
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BirthCertificateDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BirthCertificateDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BirthCertificateDetails query()
 */
	class BirthCertificateDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\DocumentRequestDetails|null $documentRequestDetail
 * @property-read string $deceased_father_full_name
 * @property-read string $deceased_full_name
 * @property-read string $deceased_mother_full_name
 * @property-read string $formatted_burial_date_time
 * @property-read string $formatted_death_date_time
 * @property-read string $informant_full_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeathCertificateDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeathCertificateDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeathCertificateDetails query()
 */
	class DeathCertificateDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $staff_id
 * @property int $office_id
 * @property int $service_id
 * @property string|null $to_whom
 * @property string|null $purpose
 * @property string|null $reference_number
 * @property string $status
 * @property string|null $remarks
 * @property string $payment_status
 * @property string|null $payment_reference
 * @property string|null $payment_method
 * @property string|null $payment_proof_path
 * @property string|null $payment_date
 * @property string|null $requested_date
 * @property string|null $completed_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DocumentRequestDetails|null $details
 * @property-read \App\Models\Offices $office
 * @property-read \App\Models\Services $service
 * @property-read \App\Models\User|null $staff
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest completed()
 * @method static \Database\Factories\DocumentRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest noShow()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest recent($limit = 5)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest search($term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest wherePaymentProofPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereRequestedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereToWhom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequest whereUserId($value)
 */
	class DocumentRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $document_request_id
 * @property string $request_for
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $suffix
 * @property string|null $email
 * @property string|null $contact_no
 * @property string|null $contact_first_name
 * @property string|null $contact_last_name
 * @property string|null $contact_middle_name
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $sex_at_birth
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $place_of_birth
 * @property string|null $civil_status
 * @property string|null $religion
 * @property string|null $nationality
 * @property string|null $government_id_type
 * @property string|null $government_id_image_path
 * @property string|null $relationship
 * @property string|null $address_type
 * @property string|null $address_line_1
 * @property string|null $address_line_2
 * @property string|null $region
 * @property string|null $province
 * @property string|null $city
 * @property string|null $barangay
 * @property string|null $street
 * @property string|null $zip_code
 * @property string|null $temporary_address_type
 * @property string|null $temporary_address_line_1
 * @property string|null $temporary_address_line_2
 * @property string|null $temporary_region
 * @property string|null $temporary_province
 * @property string|null $temporary_city
 * @property string|null $temporary_barangay
 * @property string|null $father_last_name
 * @property string|null $father_first_name
 * @property string|null $father_middle_name
 * @property string|null $father_suffix
 * @property \Illuminate\Support\Carbon|null $father_birthdate
 * @property string|null $father_nationality
 * @property string|null $father_religion
 * @property string|null $father_contact_no
 * @property string|null $mother_last_name
 * @property string|null $mother_first_name
 * @property string|null $mother_middle_name
 * @property string|null $mother_suffix
 * @property \Illuminate\Support\Carbon|null $mother_birthdate
 * @property string|null $mother_nationality
 * @property string|null $mother_religion
 * @property string|null $mother_contact_no
 * @property string|null $deceased_last_name
 * @property string|null $deceased_first_name
 * @property string|null $deceased_middle_name
 * @property string|null $deceased_sex
 * @property string|null $deceased_religion
 * @property int|null $deceased_age
 * @property string|null $deceased_place_of_birth
 * @property \Illuminate\Support\Carbon|null $deceased_date_of_birth
 * @property string|null $deceased_civil_status
 * @property string|null $deceased_residence
 * @property string|null $deceased_occupation
 * @property \Illuminate\Support\Carbon|null $death_date
 * @property string|null $death_time
 * @property string|null $death_place
 * @property string|null $relationship_to_deceased
 * @property string|null $deceased_father_last_name
 * @property string|null $deceased_father_first_name
 * @property string|null $deceased_father_middle_name
 * @property string|null $deceased_mother_last_name
 * @property string|null $deceased_mother_first_name
 * @property string|null $deceased_mother_middle_name
 * @property string|null $burial_cemetery_name
 * @property string|null $burial_cemetery_address
 * @property string|null $informant_name
 * @property string|null $informant_address
 * @property string|null $informant_relationship
 * @property string|null $informant_contact_no
 * @property string|null $groom_first_name
 * @property string|null $groom_middle_name
 * @property string|null $groom_last_name
 * @property string|null $groom_suffix
 * @property int|null $groom_age
 * @property string|null $groom_date_of_birth
 * @property string|null $groom_place_of_birth
 * @property string|null $groom_sex
 * @property string|null $groom_citizenship
 * @property string|null $groom_residence
 * @property string|null $groom_religion
 * @property string|null $groom_civil_status
 * @property string|null $groom_father_first_name
 * @property string|null $groom_father_middle_name
 * @property string|null $groom_father_last_name
 * @property string|null $groom_father_suffix
 * @property string|null $groom_father_citizenship
 * @property string|null $groom_father_residence
 * @property string|null $groom_mother_first_name
 * @property string|null $groom_mother_middle_name
 * @property string|null $groom_mother_last_name
 * @property string|null $groom_mother_citizenship
 * @property string|null $groom_mother_residence
 * @property string|null $bride_first_name
 * @property string|null $bride_middle_name
 * @property string|null $bride_last_name
 * @property string|null $bride_suffix
 * @property int|null $bride_age
 * @property string|null $bride_date_of_birth
 * @property string|null $bride_place_of_birth
 * @property string|null $bride_sex
 * @property string|null $bride_citizenship
 * @property string|null $bride_residence
 * @property string|null $bride_religion
 * @property string|null $bride_civil_status
 * @property string|null $bride_father_first_name
 * @property string|null $bride_father_middle_name
 * @property string|null $bride_father_last_name
 * @property string|null $bride_father_suffix
 * @property string|null $bride_father_citizenship
 * @property string|null $bride_father_residence
 * @property string|null $bride_mother_first_name
 * @property string|null $bride_mother_middle_name
 * @property string|null $bride_mother_last_name
 * @property string|null $bride_mother_citizenship
 * @property string|null $bride_mother_residence
 * @property string|null $establishment_name
 * @property string|null $establishment_address
 * @property string|null $establishment_purpose
 * @property string|null $consent_person
 * @property string|null $consent_relationship
 * @property string|null $consent_citizenship
 * @property string|null $consent_residence
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BirthCertificateDetails|null $birthCertificateDetails
 * @property-read \App\Models\DeathCertificateDetails|null $deathCertificateDetails
 * @property-read \App\Models\DocumentRequest $documentRequest
 * @property-read string $complete_address
 * @property-read string $contact_full_name
 * @property-read string $deceased_full_name
 * @property-read string $father_full_name
 * @property-read string $full_name
 * @property-read string $mother_full_name
 * @property-read \App\Models\MarriageLicenseDetails|null $marriageLicenseDetails
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereAddressType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBarangay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideCivilStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFatherCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFatherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFatherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFatherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFatherResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFatherSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideMotherCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideMotherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideMotherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideMotherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideMotherResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBridePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBrideSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBurialCemeteryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereBurialCemeteryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereCivilStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereConsentCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereConsentPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereConsentRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereConsentResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereContactFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereContactLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereContactMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeathDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeathPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeathTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedCivilStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedFatherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedFatherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedFatherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedMotherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedMotherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedMotherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedPlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDeceasedSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereDocumentRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereEstablishmentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereEstablishmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereEstablishmentPurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFatherSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGovernmentIdImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGovernmentIdType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomCivilStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFatherCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFatherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFatherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFatherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFatherResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFatherSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomMotherCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomMotherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomMotherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomMotherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomMotherResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomPlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereGroomSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereInformantAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereInformantContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereInformantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereInformantRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereMotherSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereRelationshipToDeceased($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereRequestFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereSexAtBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryAddressType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryBarangay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereTemporaryRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentRequestDetails whereZipCode($value)
 */
	class DocumentRequestDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\DocumentRequestDetails|null $documentRequestDetail
 * @property-read int $age_difference
 * @property-read string $bride_father_full_name
 * @property-read string $bride_full_name
 * @property-read string $bride_mother_full_name
 * @property-read string $consent_person_full_name
 * @property-read string $formatted_marriage_date_time
 * @property-read string $groom_father_full_name
 * @property-read string $groom_full_name
 * @property-read string $groom_mother_full_name
 * @property-read string $officiant_full_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarriageLicenseDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarriageLicenseDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarriageLicenseDetails query()
 */
	class MarriageLicenseDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Services> $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offices whereUpdatedAt($value)
 */
	class Offices extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $suffix
 * @property string|null $date_of_birth
 * @property string|null $place_of_birth
 * @property string|null $sex_at_birth
 * @property string|null $civil_status
 * @property string|null $religion
 * @property string|null $nationality
 * @property string|null $contact_no
 * @property string|null $government_id_type
 * @property string|null $government_id_image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAddresses> $userAddresses
 * @property-read int|null $user_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserFamily> $userFamilies
 * @property-read int|null $user_families_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereCivilStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereGovernmentIdImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereGovernmentIdType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereSexAtBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalInformation whereUserId($value)
 */
	class PersonalInformation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $office_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $price
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointments> $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DocumentRequest> $documentRequests
 * @property-read int|null $document_requests_count
 * @property-read \App\Models\Offices $office
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Services whereUpdatedAt($value)
 */
	class Services extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string $email
 * @property string|null $email_otp
 * @property \Illuminate\Support\Carbon|null $email_otp_expires_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property array<array-key, mixed>|null $notification_settings
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DocumentRequest> $assignedDocumentRequests
 * @property-read int|null $assigned_document_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DocumentRequest> $documentRequests
 * @property-read int|null $document_requests_count
 * @property-read string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\PersonalInformation|null $personalInformation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAddresses> $userAddresses
 * @property-read int|null $user_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserFamily> $userFamilies
 * @property-read int|null $user_families_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailOtpExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $personal_information_id
 * @property string|null $address_type
 * @property string|null $address_line_1
 * @property string|null $address_line_2
 * @property string|null $region
 * @property string|null $province
 * @property string|null $city
 * @property string|null $barangay
 * @property string|null $street
 * @property string|null $zip_code
 * @property string|null $temporary_address_type
 * @property string|null $temporary_address_line_1
 * @property string|null $temporary_address_line_2
 * @property string|null $temporary_region
 * @property string|null $temporary_province
 * @property string|null $temporary_city
 * @property string|null $temporary_barangay
 * @property string|null $temporary_street
 * @property string|null $temporary_zip_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $complete_address
 * @property-read string $short_address
 * @property-read \App\Models\PersonalInformation $personalInformation
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses permanent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses primary()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses temporary()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereAddressType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereBarangay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses wherePersonalInformationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryAddressType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryBarangay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereTemporaryZipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddresses whereZipCode($value)
 */
	class UserAddresses extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $personal_information_id
 * @property string|null $father_last_name
 * @property string|null $father_first_name
 * @property string|null $father_middle_name
 * @property string|null $father_suffix
 * @property string|null $father_birthdate
 * @property string|null $father_nationality
 * @property string|null $father_religion
 * @property string|null $father_contact_no
 * @property string|null $mother_last_name
 * @property string|null $mother_first_name
 * @property string|null $mother_middle_name
 * @property string|null $mother_suffix
 * @property string|null $mother_birthdate
 * @property string|null $mother_nationality
 * @property string|null $mother_religion
 * @property string|null $mother_contact_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $father_name
 * @property-read mixed $mother_name
 * @property-read \App\Models\PersonalInformation $personalInformation
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereFatherSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereMotherSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily wherePersonalInformationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFamily whereUserId($value)
 */
	class UserFamily extends \Eloquent {}
}

