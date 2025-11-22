<?php

declare(strict_types=1);

use App\Enums\AdminNotificationEvent;
use App\Enums\RequestNotificationEvent;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestDetails;
use App\Models\Offices;
use App\Models\PersonalInformation;
use App\Models\Services;
use App\Models\User;
use App\Models\UserAddresses;
use App\Models\UserFamily;
use App\Notifications\AdminEventNotification;
use App\Notifications\RequestEventNotification;
use App\Services\PhilippineLocationsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Title('Document Request')] class extends Component {
    use WithFileUploads;

    public int $step = 1;

    public string $to_whom = 'myself';

    public string $purpose = '';

    public string $relationship = '';

    public string $relationship_others = '';

    // User Information
    public string $first_name = '';

    public string $last_name = '';

    public string $middle_name = '';

    public string $email = '';

    public string $phone = '';

    public string $suffix = '';

    public string $father_first_name = '';

    public string $father_middle_name = '';

    public string $father_last_name = '';

    public string $father_suffix = '';

    public string $father_birthdate = '';

    public string $father_nationality = '';

    public string $father_religion = '';

    public string $father_contact_no = '';

    public string $purpose_others = '';

    public string $mother_first_name = '';

    public string $mother_middle_name = '';

    public string $mother_last_name = '';

    public string $mother_suffix = '';

    public string $mother_birthdate = '';

    public string $mother_nationality = '';

    public string $mother_religion = '';

    public string $mother_contact_no = '';

    public string $sex_at_birth = '';

    public string $date_of_birth = '';

    public string $place_of_birth = '';

    public string $civil_status = 'Single';

    public string $religion = '';

    public string $nationality = 'Filipino';

    public string $address_type = 'Permanent';

    public string $address_line_1 = '';

    public string $address_line_2 = '';

    public string $government_id_type = '';

    public mixed $government_id_image_path = null;

    public mixed $government_id_image_file = null;

    public ?string $reference_number = '';

    public string $payment_reference = '';

    public bool $father_is_unknown = false;

    public bool $mother_is_unknown = false;

    public bool $deceased_is_unknown = true;

    public bool $same_as_personal_address = false;

    // Death Certificate specific fields
    public string $deceased_last_name = '';

    public string $deceased_first_name = '';

    public string $deceased_middle_name = '';

    public string $deceased_father_last_name = '';

    public string $deceased_father_first_name = '';

    public string $deceased_father_middle_name = '';

    public string $deceased_mother_last_name = '';

    public string $deceased_mother_first_name = '';

    public string $deceased_mother_middle_name = '';

    // Contact Information
    public string $contact_first_name = '';

    public string $contact_last_name = '';

    public string $contact_middle_name = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    public ?int $id = null;

    public bool $isLoading = false;

    public Carbon $requested_date;

    public Offices $office;

    public Services $service;

    public PersonalInformation $personalInformation;

    public UserFamily $userFamilies;

    public UserAddresses $userAddresses;

    public $selectedPaymentMethod = null;

    public $paymentProofImage = null;

    // Marriage License Modular Fields
    // Groom Personal Info
    public string $groom_first_name = '';

    public string $groom_middle_name = '';

    public string $groom_last_name = '';

    public string $groom_suffix = '';

    public ?int $groom_age;

    public string $groom_date_of_birth = '';

    public string $groom_place_of_birth = '';

    public string $groom_sex = '';

    public string $groom_citizenship = '';

    public string $groom_residence = '';

    public string $groom_religion = '';

    public string $groom_civil_status = '';

    // Groom Father
    public string $groom_father_first_name = '';

    public string $groom_father_middle_name = '';

    public string $groom_father_last_name = '';

    public string $groom_father_suffix = '';

    public string $groom_father_citizenship = '';

    public string $groom_father_residence = '';

    // Groom Mother
    public string $groom_mother_first_name = '';

    public string $groom_mother_middle_name = '';

    public string $groom_mother_last_name = '';

    public string $groom_mother_suffix = '';

    public string $groom_mother_citizenship = '';

    public string $groom_mother_residence = '';

    // Bride Personal Info
    public string $bride_first_name = '';

    public string $bride_middle_name = '';

    public string $bride_last_name = '';

    public string $bride_suffix = '';

    public ?int $bride_age;

    public string $bride_date_of_birth = '';

    public string $bride_place_of_birth = '';

    public string $bride_sex = '';

    public string $bride_citizenship = '';

    public string $bride_residence = '';

    public string $bride_religion = '';

    public string $bride_civil_status = '';

    // Bride Father
    public string $bride_father_first_name = '';

    public string $bride_father_middle_name = '';

    public string $bride_father_last_name = '';

    public string $bride_father_suffix = '';

    public string $bride_father_citizenship = '';

    public string $bride_father_residence = '';

    // Bride Mother
    public string $bride_mother_first_name = '';

    public string $bride_mother_middle_name = '';

    public string $bride_mother_last_name = '';

    public string $bride_mother_suffix = '';

    public string $bride_mother_citizenship = '';

    public string $bride_mother_residence = '';

    public bool $editPersonDetails = false;

    // Special Permit Fields
    public string $establishment_name = '';

    public string $establishment_address = '';

    public string $establishment_purpose = '';

    // Address Information
    public string $address = '';

    public string $region = '';

    public string $province = '';

    public string $city = '';

    public string $barangay = '';

    public string $street = '';

    public string $zip_code = '';

    public string $country = '';

    // Present Address properties
    // public string $temporary_address_type = '';
    // public string $temporary_address_line_1 = '';
    // public string $temporary_address_line_2 = '';
    // public string $temporary_region = '';
    // public string $temporary_province = '';
    // public string $temporary_city = '';
    // public string $temporary_barangay = '';
    // public string $temporary_street = '';
    // public string $temporary_zip_code = '';

    public array $regions = [];

    public array $provinces = [];

    public array $cities = [];

    public array $barangays = [];

    public string $serviceSelected = '';

    public bool $termsAccepted = false;

    public function mount(Offices $office, Services $service, PersonalInformation $personalInformation, UserFamily $userFamilies, UserAddresses $userAddresses, PhilippineLocationsService $locations, ?string $reference_number = null): void
    {
        $this->office = $office;
        $this->service = $service;
        $this->personalInformation = $personalInformation;
        $this->userFamilies = $userFamilies;
        $this->userAddresses = $userAddresses;
        $this->editPersonDetails = false;
        // Pre-populate dependent dropdowns if values exist

        // Initialize user and userAddresses like userinfo.blade.php
        $this->user = auth()->user();
        $this->userAddresses = $this->user->userAddresses->first();

        $this->address = $this->userAddresses->address_line_1 ?? '';
        $this->region = $this->userAddresses->region ?? '';
        $this->province = $this->userAddresses->province ?? '';
        $this->city = $this->userAddresses->city ?? '';
        $this->barangay = $this->userAddresses->barangay ?? '';
        $this->street = $this->userAddresses->street ?? '';
        $this->zip_code = $this->userAddresses->zip_code ?? '';

        if ($this->region) {
            $this->provinces = $locations->getProvinces($this->region);
        }
        if ($this->region && $this->province) {
            $this->cities = $locations->getMunicipalities($this->region, $this->province);
        }
        if ($this->region && $this->province && $this->city) {
            $this->barangays = $locations->getBarangays($this->region, $this->province, $this->city);
        }
        $this->regions = $locations->getRegions();
        // Check if user has complete profile
        if (!auth()->user()->hasCompleteProfile()) {
            session()->flash('warning', 'Please complete your profile information before making a document request.');
            $this->redirect(route('userinfo'));
        }

        // Use request()->string('reference_number') if not passed directly
        $refNo = $reference_number ?? (request()->has('reference_number') ? request()->string('reference_number') : null);
        if ($refNo) {
            $existingRequest = DocumentRequest::where('reference_number', $refNo)
                ->where('user_id', auth()->id())
                ->with('details')
                ->first();
            if ($existingRequest) {
                $this->id = $existingRequest->id;
                $this->service = $existingRequest->service;
                $this->reference_number = $existingRequest->reference_number;
                $this->to_whom = $existingRequest->to_whom;
                $this->purpose = $existingRequest->purpose;
                $this->payment_reference = $existingRequest->payment_reference ?? '';
                $this->status = $existingRequest->status ?? '';
                $this->payment_status = $existingRequest->payment_status ?? '';
                // Set step based on payment status
                if ($existingRequest->payment_status === 'unpaid') {
                    $this->step = 8;
                } elseif (in_array($existingRequest->payment_status, ['paid', 'completed'])) {
                    $this->step = 9;
                } else {
                    $this->step = 1; // fallback
                }
                // Populate ALL fields from details if available
                if ($existingRequest->details && !$this->service->slug === 'special-permit') {
                    foreach ($existingRequest->details->toArray() as $field => $value) {
                        if (property_exists($this, $field)) {
                            $this->{$field} = $value;
                        }
                    }
                } else {
                    $this->details = $existingRequest->details;

                    // Map details fields to component properties if they exist
                    if ($this->details) {
                        $fields = [
                            'request_for' => 'to_whom',
                            'purpose' => 'purpose',
                            'establishment_name' => 'establishment_name',
                            'establishment_address' => 'establishment_address',
                            'establishment_purpose' => 'establishment_purpose',
                            'payment_reference' => 'payment_reference',
                            'status' => 'status',
                            'payment_status' => 'payment_status',
                        ];

                        foreach ($fields as $detailField => $property) {
                            if (property_exists($this, $property) && isset($this->details->{$detailField})) {
                                $this->{$property} = $this->details->{$detailField};
                            }
                        }
                    }

                    // Set step based on payment status
                    if ($this->payment_status === 'unpaid') {
                        $this->step = 8;
                    } elseif (in_array($this->payment_status, ['paid', 'completed'], true)) {
                        $this->step = 9;
                    } else {
                        $this->step = 1;
                    }
                }

                return;
            } else {
                session()->flash('error', 'Document request not found or you do not have access.');
                $this->redirect(route('client.documents'));
            }
        }

        $this->updatedFatherIsUnknown($this->father_is_unknown);
        $this->updatedMotherIsUnknown($this->mother_is_unknown);
        if ($this->service->title !== 'Death Certificate') {
            $this->updatedDeceasedIsUnknown($this->deceased_is_unknown);
        }

        // Only populate user data if "myself" is selected
        if ($this->to_whom === 'myself') {
            $this->populateUserData();
        }
    }

    public function termsAccepted()
    {
        if ($this->termsAccepted) {
            $this->step = 2;
            $this->isLoading = true;
        } else {
            session()->flash('error', 'You must accept the terms and conditions to proceed.');
        }
    }

    public function updatedServiceSelected($value)
    {
        $this->service = Services::where('slug', $value)->first();
        $this->serviceSelected = $value;
    }

    public function updatedRegion(PhilippineLocationsService $locations)
    {
        $this->provinces = $locations->getProvinces($this->region);
        $this->province = '';
        $this->cities = [];
        $this->city = '';
        $this->barangays = [];
        $this->barangay = '';
    }

    public function updatedProvince(PhilippineLocationsService $locations)
    {
        $this->cities = $locations->getMunicipalities($this->region, $this->province);
        $this->city = '';
        $this->barangays = [];
        $this->barangay = '';
    }

    public function updatedCity(PhilippineLocationsService $locations)
    {
        $this->barangays = $locations->getBarangays($this->region, $this->province, $this->city);
        $this->barangay = '';
    }

    public function populateUserData(): void
    {
        $userData = auth()->user()->getDocumentRequestFormData();
        // Populate form fields with user data (temporary data for this request only)
        foreach ($userData as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }

    public function updatedToWhom(): void
    {
        if ($this->to_whom === 'myself') {
            $this->populateUserData();
        } else {
            $this->clearPersonalData();
        }
    }

    public function clearPersonalData(): void
    {
        // Clear all personal information fields
        $this->last_name = '';
        $this->first_name = '';
        $this->middle_name = '';
        $this->email = '';
        $this->phone = '';
        $this->suffix = '';
        $this->sex_at_birth = '';
        $this->date_of_birth = '';
        $this->place_of_birth = '';
        $this->civil_status = 'Single';
        $this->religion = '';
        $this->nationality = 'Filipino';
        $this->address_type = 'Permanent';
        $this->address_line_1 = '';
        $this->address_line_2 = '';
        $this->region = '';
        $this->province = '';
        $this->city = '';
        $this->barangay = '';
        $this->street = '';
        $this->zip_code = '';
        $this->father_last_name = '';
        $this->father_first_name = '';
        $this->father_middle_name = '';
        $this->father_suffix = '';
        $this->father_birthdate = '';
        $this->father_nationality = '';
        $this->father_religion = '';
        $this->father_contact_no = '';
        $this->mother_last_name = '';
        $this->mother_first_name = '';
        $this->mother_middle_name = '';
        $this->mother_suffix = '';
        $this->mother_birthdate = '';
        $this->mother_nationality = '';
        $this->mother_religion = '';
        $this->mother_contact_no = '';
        $this->government_id_type = '';
        $this->government_id_image_path = null;
        // Clear death certificate fields
        $this->deceased_last_name = '';
        $this->deceased_first_name = '';
        $this->deceased_middle_name = '';
    }

    public function updatedSameAsPersonalAddress($value)
    {
        if ($value) {
            $user = auth()->user();
            $address = $user->userAddresses->first();
            $this->address_type = $address->address_type ?? 'Permanent';
            $this->address_line_1 = $address->address_line_1 ?? '';
            $this->address_line_2 = $address->address_line_2 ?? '';
            $this->region = $address->region ?? '';
            $this->province = $address->province ?? '';
            $this->city = $address->city ?? '';
            $this->barangay = $address->barangay ?? '';
            $this->street = $address->street ?? '';
            $this->zip_code = $address->zip_code ?? '';
        } else {
            $this->address_type = '';
            $this->address_line_1 = '';
            $this->address_line_2 = '';
            $this->region = '';
            $this->province = '';
            $this->city = '';
            $this->barangay = '';
            $this->street = '';
            $this->zip_code = '';
        }
    }

    public function initializeContactInfo(): void
    {
        if ($this->to_whom === 'myself') {
            $this->contact_first_name = $this->first_name;
            $this->contact_last_name = $this->last_name;
            $this->contact_middle_name = $this->middle_name;
            $this->contact_email = $this->email;
            $this->contact_phone = $this->phone;
        } else {
            $user = auth()->user();
            $this->contact_first_name = $user->first_name ?? '';
            $this->contact_last_name = $user->last_name ?? '';
            $this->contact_middle_name = $user->middle_name ?? '';
            $this->contact_email = $user->email ?? '';
            $this->contact_phone = preg_replace('/[^0-9]/', '', $user->phone ?? '');
        }
    }

    public function updatedFatherIsUnknown(bool $value): void
    {
        if ($value) {
            $this->father_last_name = 'N/A';
            $this->father_first_name = 'N/A';
            $this->father_middle_name = 'N/A';
            $this->father_suffix = 'N/A';
            $this->father_birthdate = '0001-01-01';
            $this->father_nationality = 'N/A';
            $this->father_religion = 'N/A';
            $this->father_contact_no = 'N/A';
        } else {
            $this->father_last_name = '';
            $this->father_first_name = '';
            $this->father_middle_name = '';
            $this->father_suffix = '';
            $this->father_birthdate = '';
            $this->father_nationality = '';
            $this->father_religion = '';
            $this->father_contact_no = '';
        }
    }

    public function updatedMotherIsUnknown(bool $value): void
    {
        if ($value) {
            $this->mother_last_name = 'N/A';
            $this->mother_first_name = 'N/A';
            $this->mother_middle_name = 'N/A';
            $this->mother_suffix = 'N/A';
            $this->mother_birthdate = '0001-01-01';
            $this->mother_nationality = 'N/A';
            $this->mother_religion = 'N/A';
            $this->mother_contact_no = 'N/A';
        } else {
            $this->mother_last_name = '';
            $this->mother_first_name = '';
            $this->mother_middle_name = '';
            $this->mother_suffix = '';
            $this->mother_birthdate = '';
            $this->mother_nationality = '';
            $this->mother_religion = '';
            $this->mother_contact_no = '';
        }
    }

    public function updatedDeceasedIsUnknown(bool $value): void
    {
        if ($value) {
            $this->deceased_last_name = 'N/A';
            $this->deceased_first_name = 'N/A';
            $this->deceased_middle_name = 'N/A';
        } else {
            $this->deceased_last_name = '';
            $this->deceased_first_name = '';
            $this->deceased_middle_name = '';
        }
    }

    public function nextStep()
    {
        switch ($this->step) {
            case 2:
                $this->isLoading = true;
                $this->validate([
                    'serviceSelected' => 'required',
                ]);
                break;
            case 3:
                $this->isLoading = true;
                if ($this->service->slug === 'special-permit') {
                    // For special permit, skip to_whom validation as it's not needed
                    // The special permit form is self-contained
                } else {
                    $this->validate([
                        'to_whom' => 'required',
                        'relationship' => $this->to_whom === 'someone_else' ? 'required|in:my_father,my_mother,my_son,my_daughter,others,my_wife' : 'nullable|in:my_father,my_mother,my_son,my_daughter,others,my_wife',
                    ]);
                }
                do {
                    $this->reference_number = 'DOC-' . strtoupper(Str::random(10));
                } while (DocumentRequest::where('reference_number', $this->reference_number)->exists());
                break;
            case 4:
                $this->isLoading = true;
                if ($this->service->slug === 'special-permit') {
                    // For special permit, skip purpose validation as it's handled in the special permit form
                } else {
                    $this->validate([
                        'purpose' => 'required',
                    ]);
                }
                break;
            case 5:
                $this->isLoading = true;
                if ($this->service->slug === 'death-certificate') {
                    $rules = [
                        'deceased_last_name' => 'required|string|max:255',
                        'deceased_first_name' => 'required|string|max:255',
                        'deceased_middle_name' => 'nullable|string|max:255',
                        // Parental Information
                        'deceased_father_last_name' => 'required|string|max:100',
                        'deceased_father_first_name' => 'required|string|max:100',
                        'deceased_father_middle_name' => 'nullable|string|max:100',
                        'deceased_mother_last_name' => 'required|string|max:100',
                        'deceased_mother_first_name' => 'required|string|max:100',
                        'deceased_mother_middle_name' => 'nullable|string|max:100',
                    ];
                } elseif ($this->service->slug === 'marriage-certificate') {
                    $rules = $this->marriageLicenseRules();
                } elseif ($this->service->slug === 'special-permit') {
                    $rules = [
                        'establishment_name' => 'required|string|max:255',
                        'establishment_address' => 'required|string|max:500',
                        'establishment_purpose' => 'required|string|max:500',
                    ];
                } else {
                    $rules = [
                        'first_name' => 'required|string|max:255',
                        'middle_name' => 'nullable|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'suffix' => 'nullable|string|max:10',
                        'email' => 'required|email|max:255',
                        'phone' => 'required|numeric|digits:11',
                        'sex_at_birth' => 'required|in:Male,Female',
                        'date_of_birth' => 'required|date|before:today',
                        'place_of_birth' => 'required|string|max:255',
                        'civil_status' => 'required|in:Single,Married,Widowed,Divorced,Separated',
                        'nationality' => 'required|string|max:255',
                        'government_id_type' => 'nullable|string|max:100',
                        'government_id_image_file' => 'nullable|file|mimes:pdf|max:5120', // 5MB max for PDF
                        'address_type' => 'required|in:Permanent,Temporary',
                        'address_line_1' => 'required|string|max:255',
                        'region' => 'required|string|max:255',
                        'province' => 'required|string|max:255',
                        'city' => 'required|string|max:255',
                        'barangay' => 'required|string|max:255',
                        'zip_code' => 'required|string|max:10',
                    ];
                    $messages = [
                        'phone.digits' => 'The contact number must be exactly 11 digits.',
                        'phone.required' => 'The contact number field is required.',
                        'phone.numeric' => 'The contact number must contain only numbers.',

                        // 'temporary_address_type' => 'required|in:Permanent,Temporary',
                        // 'temporary_address_line_1' => 'required|string|max:255',
                        // 'temporary_address_line_2' => 'required|string|max:255',
                        // 'temporary_region' => 'required|string|max:255',
                        // 'temporary_province' => 'required|string|max:255',
                        // 'temporary_city' => 'required|string|max:255',
                        // 'temporary_barangay' => 'required|string|max:255',
                        // 'temporary_street' => 'required|string|max:255',
                        // 'temporary_zip_code' => 'required|string|max:10',
                    ];

                    if (($this->to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $this->service->slug !== 'death-certificate') {
                        if (!$this->father_is_unknown && !$this->mother_is_unknown) {
                            $rules = array_merge($rules, [
                                'father_last_name' => 'required|string|max:255',
                                'father_first_name' => 'required|string|max:255',
                                'father_middle_name' => 'nullable|string|max:255',
                                'father_suffix' => 'nullable|string|max:10',
                                'father_birthdate' => 'nullable|date',
                                'father_nationality' => 'required|string|max:255',
                                'father_religion' => 'required|string|max:255',
                                'father_contact_no' => ['required', 'string', 'regex:/^(\d{11}|N\/A)$/'],   // 11 digits or N/A
                            ]);

                            $rules = array_merge($rules, [
                                'mother_last_name' => 'required|string|max:255',
                                'mother_first_name' => 'required|string|max:255',
                                'mother_middle_name' => 'nullable|string|max:255',
                                'mother_suffix' => 'nullable|string|max:10',
                                'mother_birthdate' => 'nullable|date',
                                'mother_nationality' => 'required|string|max:255',
                                'mother_religion' => 'required|string|max:255',
                                'mother_contact_no' => ['required', 'string', 'regex:/^(\d{11}|N\/A)$/'],   // 11 digits or N/A
                            ]);
                        }
                    }
                }

                $this->validate($rules, $messages ?? []);

                // Add info redundancy validation for someone else (skip for special permit)
                if ($this->to_whom === 'someone_else' && $this->service->slug !== 'special-permit') {
                    $currentUser = auth()->user();
                    $userData = $currentUser->getAppointmentFormData();

                    $isRedundant = true;

                    // Compare names (case-insensitive)
                    if (strtolower(trim($this->first_name)) !== strtolower(trim($userData['first_name'] ?? ''))) {
                        $isRedundant = false;
                    }
                    if (strtolower(trim($this->last_name)) !== strtolower(trim($userData['last_name'] ?? ''))) {
                        $isRedundant = false;
                    }
                    if (!empty($this->middle_name) && strtolower(trim($this->middle_name)) !== strtolower(trim($userData['middle_name'] ?? ''))) {
                        $isRedundant = false;
                    }

                    if ($isRedundant) {
                        $relationshipName = ucfirst($this->relationship);
                        $this->addError('info_redundancy', "INFO REDUNDANCY: You are entering your own information for a document that is for {$relationshipName}. Please enter the correct information for {$relationshipName} instead of your own details.");

                        return;
                    }
                }

                if ($this->service->slug === 'special-permit') {
                    // For special permit, skip family defaults and contact info initialization
                    // as the special permit form is self-contained
                    // Go to step 7 (confirmation) for special permit
                    $this->step = 7;
                    $this->isLoading = false;

                    return;
                } else {
                    $this->fillMarriageCertificateData();
                    $this->fillFamilyDefaults();
                    $this->initializeContactInfo();
                }
                break;
            case 6:
                $this->isLoading = true;
                if ($this->service->slug === 'special-permit') {
                    // For special permit, skip contact validation as it's not needed
                    // The special permit form is self-contained
                } else {
                    $this->validate([
                        'contact_first_name' => 'required|string|max:255',
                        'contact_last_name' => 'required|string|max:255',
                        'contact_email' => 'required|email|max:255',
                        'contact_phone' => 'required|numeric|digits:11',
                    ], [
                        'contact_phone.digits' => 'The phone number must be exactly 11 digits.',
                        'contact_phone.required' => 'The phone number field is required.',
                        'contact_phone.numeric' => 'The phone number must contain only numbers.',
                    ]);
                }
                break;
            case 7:
                $this->isLoading = true;
                if ($this->service->slug === 'death-certificate') {
                    // For death certificate, validate contact information (requester's details)
                    $this->validate([
                        'contact_last_name' => 'required|string|max:255',
                        'contact_first_name' => 'required|string|max:255',
                        'contact_middle_name' => 'nullable|string|max:255',
                        'contact_email' => 'required|email|max:255',
                        'contact_phone' => 'required|numeric|digits:11',
                    ]);
                } elseif ($this->service->slug === 'special-permit') {
                    // For special permit, skip contact validation as it's not needed
                    // The special permit form is self-contained
                } else {
                    // For other document types, validate contact information
                    $this->validate([
                        'contact_first_name' => 'required|string|max:255',
                        'contact_last_name' => 'required|string|max:255',
                        'contact_email' => 'required|email|max:255',
                        'contact_phone' => 'required|numeric|digits:11',
                    ], [
                        'contact_phone.digits' => 'The phone number must be exactly 11 digits.',
                        'contact_phone.required' => 'The phone number field is required.',
                        'contact_phone.numeric' => 'The phone number must contain only numbers.',
                    ]);
                }
                break;
        }
        $this->step++;
        $this->isLoading = false;
    }

    public function validateContactInfo(): void
    {
        if ($this->service->slug === 'special-permit') {
            // For special permit, skip contact validation as it's not needed
            // The special permit form is self-contained
            return;
        }

        $this->validate([
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|numeric|digits_between:7,25',
        ]);
    }

    private function fillFamilyDefaults(): void
    {
        // Father's Information - check each field individually
        if (empty($this->father_last_name)) {
            $this->father_last_name = 'N/A';
        }
        if (empty($this->father_first_name)) {
            $this->father_first_name = 'N/A';
        }
        if (empty($this->father_middle_name)) {
            $this->father_middle_name = 'N/A';
        }
        if (empty($this->father_suffix)) {
            $this->father_suffix = 'N/A';
        }
        if (empty($this->father_birthdate)) {
            $this->father_birthdate = '0001-01-01';
        }
        if (empty($this->father_nationality)) {
            $this->father_nationality = 'N/A';
        }
        if (empty($this->father_religion)) {
            $this->father_religion = 'N/A';
        }
        if (empty($this->father_contact_no)) {
            $this->father_contact_no = 'N/A';
        }

        // Mother's Information - check each field individually
        if (empty($this->mother_last_name)) {
            $this->mother_last_name = 'N/A';
        }
        if (empty($this->mother_first_name)) {
            $this->mother_first_name = 'N/A';
        }
        if (empty($this->mother_middle_name)) {
            $this->mother_middle_name = 'N/A';
        }
        if (empty($this->mother_suffix)) {
            $this->mother_suffix = 'N/A';
        }
        if (empty($this->mother_birthdate)) {
            $this->mother_birthdate = '0001-01-01';
        }
        if (empty($this->mother_nationality)) {
            $this->mother_nationality = 'N/A';
        }
        if (empty($this->mother_religion)) {
            $this->mother_religion = 'N/A';
        }
        if (empty($this->mother_contact_no)) {
            $this->mother_contact_no = 'N/A';
        }

        // Death Certificate fields - check each field individually
        if (empty($this->deceased_last_name)) {
            $this->deceased_last_name = 'N/A';
        }
        if (empty($this->deceased_first_name)) {
            $this->deceased_first_name = 'N/A';
        }
        if (empty($this->deceased_middle_name)) {
            $this->deceased_middle_name = 'N/A';
        }
        if (empty($this->deceased_father_last_name)) {
            $this->deceased_father_last_name = 'N/A';
        }
        if (empty($this->deceased_father_first_name)) {
            $this->deceased_father_first_name = 'N/A';
        }
        if (empty($this->deceased_father_middle_name)) {
            $this->deceased_father_middle_name = 'N/A';
        }
        if (empty($this->deceased_mother_last_name)) {
            $this->deceased_mother_last_name = 'N/A';
        }
        if (empty($this->deceased_mother_first_name)) {
            $this->deceased_mother_first_name = 'N/A';
        }
        if (empty($this->deceased_mother_middle_name)) {
            $this->deceased_mother_middle_name = 'N/A';
        }
    }

    private function fillMarriageCertificateData(): void
    {
        // Set sensible defaults for marriage certificate request fields if empty

        // Groom Information
        $this->groom_first_name = $this->groom_first_name ?: 'N/A';
        $this->groom_middle_name = $this->groom_middle_name ?: 'N/A';
        $this->groom_last_name = $this->groom_last_name ?: 'N/A';
        $this->groom_suffix = $this->groom_suffix ?: 'N/A';
        $this->groom_age = $this->groom_age ?? 0;
        $this->groom_date_of_birth = $this->groom_date_of_birth ?: '0001-01-01';
        $this->groom_place_of_birth = $this->groom_place_of_birth ?: 'N/A';
        $this->groom_sex = $this->groom_sex ?: 'Male';
        $this->groom_citizenship = $this->groom_citizenship ?: 'N/A';
        $this->groom_residence = $this->groom_residence ?: 'N/A';
        $this->groom_religion = $this->groom_religion ?: 'N/A';
        $this->groom_civil_status = $this->groom_civil_status ?: 'N/A';

        // Groom's Father Information
        $this->groom_father_first_name = $this->groom_father_first_name ?: 'N/A';
        $this->groom_father_middle_name = $this->groom_father_middle_name ?: 'N/A';
        $this->groom_father_last_name = $this->groom_father_last_name ?: 'N/A';
        $this->groom_father_suffix = $this->groom_father_suffix ?: 'N/A';
        $this->groom_father_citizenship = $this->groom_father_citizenship ?: 'N/A';
        $this->groom_father_residence = $this->groom_father_residence ?: 'N/A';

        // Groom's Mother Information
        $this->groom_mother_first_name = $this->groom_mother_first_name ?: 'N/A';
        $this->groom_mother_middle_name = $this->groom_mother_middle_name ?: 'N/A';
        $this->groom_mother_last_name = $this->groom_mother_last_name ?: 'N/A';
        $this->groom_mother_suffix = $this->groom_mother_suffix ?: 'N/A';
        $this->groom_mother_citizenship = $this->groom_mother_citizenship ?: 'N/A';
        $this->groom_mother_residence = $this->groom_mother_residence ?: 'N/A';

        // Bride Information
        $this->bride_first_name = $this->bride_first_name ?: 'N/A';
        $this->bride_middle_name = $this->bride_middle_name ?: 'N/A';
        $this->bride_last_name = $this->bride_last_name ?: 'N/A';
        $this->bride_suffix = $this->bride_suffix ?: 'N/A';
        $this->bride_age = $this->bride_age ?? 0;
        $this->bride_date_of_birth = $this->bride_date_of_birth ?: '0001-01-01';
        $this->bride_place_of_birth = $this->bride_place_of_birth ?: 'N/A';
        $this->bride_sex = $this->bride_sex ?: 'Female';
        $this->bride_citizenship = $this->bride_citizenship ?: 'N/A';
        $this->bride_residence = $this->bride_residence ?: 'N/A';
        $this->bride_religion = $this->bride_religion ?: 'N/A';
        $this->bride_civil_status = $this->bride_civil_status ?: 'N/A';

        // Bride's Father Information
        $this->bride_father_first_name = $this->bride_father_first_name ?: 'N/A';
        $this->bride_father_middle_name = $this->bride_father_middle_name ?: 'N/A';
        $this->bride_father_last_name = $this->bride_father_last_name ?: 'N/A';
        $this->bride_father_suffix = $this->bride_father_suffix ?: 'N/A';
        $this->bride_father_citizenship = $this->bride_father_citizenship ?: 'N/A';
        $this->bride_father_residence = $this->bride_father_residence ?: 'N/A';

        // Bride's Mother Information
        $this->bride_mother_first_name = $this->bride_mother_first_name ?: 'N/A';
        $this->bride_mother_middle_name = $this->bride_mother_middle_name ?: 'N/A';
        $this->bride_mother_last_name = $this->bride_mother_last_name ?: 'N/A';
        $this->bride_mother_suffix = $this->bride_mother_suffix ?: 'N/A';
        $this->bride_mother_citizenship = $this->bride_mother_citizenship ?: 'N/A';
        $this->bride_mother_residence = $this->bride_mother_residence ?: 'N/A';
    }

    // Check if the service requires family information
    private function requiresFamilyInfo(): bool
    {
        $familyRequiredServices = ['birth-certificate', 'marriage-certificate'];

        return in_array($this->service->slug, $familyRequiredServices);
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            // For special permit, handle step progression differently
            if ($this->service->slug === 'special-permit' && $this->step === 8) {
                $this->step = 7; // Go back to confirmation
            } elseif ($this->service->slug === 'special-permit' && $this->step === 7) {
                $this->step = 5; // Go back to special permit form
            } else {
                $this->step--;
            }
        } else {
            $this->step = 1;
        }
        $this->isLoading = true;
    }

    public function submitDocumentRequest()
    {
        try {
            $this->isLoading = true;

            DB::beginTransaction();

            // Create the main document request
            $documentRequest = DocumentRequest::create([
                'user_id' => auth()->id(),
                'office_id' => $this->office->id,
                'service_id' => $this->service->id,
                'status' => 'pending',
                'to_whom' => $this->to_whom,
                'purpose' => $this->purpose === 'others' ? $this->purpose_others : $this->purpose,
                'requested_date' => now(),
                'reference_number' => $this->reference_number,
                'payment_status' => 'unpaid', // Set initial payment status
            ]);

            // Handle file upload for government ID PDF
            $governmentIdImagePath = $this->government_id_image_path; // Keep existing path if no new upload
            if ($this->government_id_image_file) {
                // New file uploaded, store it
                $governmentIdImagePath = $this->government_id_image_file->storeAs('government-ids', auth()->id() . '_' . $this->government_id_image_file->getClientOriginalName(), 'public');
            }

            // Prepare all data in one array
            $detailsData = [
                'document_request_id' => $documentRequest->id,
                'request_for' => $this->to_whom === 'myself' ? 'myself' : 'someone_else',
            ];

            // If Marriage License, use modular fields
            if ($this->service->slug === 'marriage-certificate') {
                $detailsData = array_merge($detailsData, [
                    // Groom
                    'groom_first_name' => $this->groom_first_name,
                    'groom_middle_name' => !empty(trim($this->groom_middle_name)) ? $this->groom_middle_name : 'N/A',
                    'groom_last_name' => $this->groom_last_name,
                    'groom_suffix' => !empty(trim($this->groom_suffix)) ? $this->groom_suffix : 'N/A',
                    'groom_age' => $this->groom_age ?? null,
                    'groom_date_of_birth' => $this->groom_date_of_birth,
                    'groom_place_of_birth' => $this->groom_place_of_birth,
                    'groom_sex' => $this->groom_sex,
                    'groom_citizenship' => $this->groom_citizenship,
                    'groom_residence' => $this->groom_residence,
                    'groom_religion' => $this->groom_religion,
                    'groom_civil_status' => $this->groom_civil_status,
                    // Groom Father
                    'groom_father_first_name' => $this->groom_father_first_name,
                    'groom_father_middle_name' => !empty(trim($this->groom_father_middle_name)) ? $this->groom_father_middle_name : 'N/A',
                    'groom_father_last_name' => $this->groom_father_last_name,
                    'groom_father_suffix' => !empty(trim($this->groom_father_suffix)) ? $this->groom_father_suffix : 'N/A',
                    'groom_father_citizenship' => $this->groom_father_citizenship,
                    'groom_father_residence' => $this->groom_father_residence,
                    // Groom Mother
                    'groom_mother_first_name' => $this->groom_mother_first_name,
                    'groom_mother_middle_name' => !empty(trim($this->groom_mother_middle_name)) ? $this->groom_mother_middle_name : 'N/A',
                    'groom_mother_last_name' => $this->groom_mother_last_name,
                    'groom_mother_suffix' => !empty(trim($this->groom_mother_suffix)) ? $this->groom_mother_suffix : 'N/A',
                    'groom_mother_citizenship' => $this->groom_mother_citizenship,
                    'groom_mother_residence' => $this->groom_mother_residence,
                    // Bride
                    'bride_first_name' => $this->bride_first_name,
                    'bride_middle_name' => !empty(trim($this->bride_middle_name)) ? $this->bride_middle_name : 'N/A',
                    'bride_last_name' => $this->bride_last_name,
                    'bride_suffix' => !empty(trim($this->bride_suffix)) ? $this->bride_suffix : 'N/A',
                    'bride_age' => $this->bride_age ?? null,
                    'bride_date_of_birth' => $this->bride_date_of_birth,
                    'bride_place_of_birth' => $this->bride_place_of_birth,
                    'bride_sex' => $this->bride_sex,
                    'bride_citizenship' => $this->bride_citizenship,
                    'bride_residence' => $this->bride_residence,
                    'bride_religion' => $this->bride_religion,
                    'bride_civil_status' => $this->bride_civil_status,
                    // Bride Father
                    'bride_father_first_name' => $this->bride_father_first_name,
                    'bride_father_middle_name' => !empty(trim($this->bride_father_middle_name)) ? $this->bride_father_middle_name : 'N/A',
                    'bride_father_last_name' => $this->bride_father_last_name,
                    'bride_father_suffix' => !empty(trim($this->bride_father_suffix)) ? $this->bride_father_suffix : 'N/A',
                    'bride_father_citizenship' => $this->bride_father_citizenship,
                    'bride_father_residence' => $this->bride_father_residence,
                    // Bride Mother
                    'bride_mother_first_name' => $this->bride_mother_first_name,
                    'bride_mother_middle_name' => !empty(trim($this->bride_mother_middle_name)) ? $this->bride_mother_middle_name : 'N/A',
                    'bride_mother_last_name' => $this->bride_mother_last_name,
                    'bride_mother_suffix' => !empty(trim($this->bride_mother_suffix)) ? $this->bride_mother_suffix : 'N/A',
                    'bride_mother_citizenship' => $this->bride_mother_citizenship,
                    'bride_mother_residence' => $this->bride_mother_residence,
                ]);
            }

            // If Special Permit, use special permit fields
            if ($this->service->slug === 'special-permit') {
                $detailsData = array_merge($detailsData, [
                    'establishment_name' => $this->establishment_name,
                    'establishment_address' => $this->establishment_address,
                    'establishment_purpose' => $this->establishment_purpose,
                ]);
            }

            // Existing logic for other document types (excluding special permit and death certificate)
            if ($this->service->slug !== 'special-permit' && $this->service->slug !== 'death-certificate') {
                $detailsData = array_merge($detailsData, [
                    // Personal Information
                    'last_name' => $this->last_name,
                    'first_name' => $this->first_name,
                    'middle_name' => !empty(trim($this->middle_name)) ? $this->middle_name : 'N/A',
                    'suffix' => !empty(trim($this->suffix)) ? $this->suffix : 'N/A',
                    'email' => $this->email,
                    'contact_no' => $this->phone,
                    'sex_at_birth' => $this->sex_at_birth,
                    'date_of_birth' => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
                    'place_of_birth' => $this->place_of_birth,
                    'civil_status' => $this->civil_status,
                    'religion' => !empty(trim($this->religion)) ? $this->religion : 'N/A',
                    'nationality' => $this->nationality,
                    'government_id_type' => !empty(trim($this->government_id_type)) ? $this->government_id_type : 'N/A',
                    'government_id_image_path' => $governmentIdImagePath,
                    'address_type' => $this->address_type,
                    'address_line_1' => $this->address_line_1,
                    'address_line_2' => !empty(trim($this->address_line_2)) ? $this->address_line_2 : 'N/A',
                    'region' => $this->region,
                    'province' => $this->province,
                    'city' => $this->city,
                    'barangay' => $this->barangay,
                    'street' => !empty(trim($this->street)) ? $this->street : 'N/A',
                    'zip_code' => !empty(trim($this->zip_code)) ? $this->zip_code : 'N/A',

                    //
                    // 'temporary_address_type' => $this->temporary_address_type,
                    // 'temporary_address_line_1' => $this->temporary_address_line_1,
                    // 'temporary_address_line_2' => $this->temporary_address_line_2,
                    // 'temporary_region' => $this->temporary_region,
                    // 'temporary_province' => $this->temporary_province,
                    // 'temporary_city' => $this->temporary_city,
                    // 'temporary_barangay' => $this->temporary_barangay,
                    // 'temporary_street' => $this->temporary_street,
                    // 'temporary_zip_code' => $this->temporary_zip_code,

                    'father_last_name' => $this->father_last_name,
                    'father_first_name' => $this->father_first_name,
                    'father_middle_name' => !empty(trim($this->father_middle_name)) ? $this->father_middle_name : 'N/A',
                    'father_suffix' => !empty(trim($this->father_suffix)) ? $this->father_suffix : 'N/A',
                    'father_birthdate' => $this->father_birthdate ? Carbon::parse($this->father_birthdate) : null,
                    'father_nationality' => $this->father_nationality,
                    'father_religion' => $this->father_religion,
                    'father_contact_no' => $this->father_contact_no,
                    'mother_last_name' => $this->mother_last_name,
                    'mother_first_name' => $this->mother_first_name,
                    'mother_middle_name' => !empty(trim($this->mother_middle_name)) ? $this->mother_middle_name : 'N/A',
                    'mother_suffix' => !empty(trim($this->mother_suffix)) ? $this->mother_suffix : 'N/A',
                    'mother_birthdate' => $this->mother_birthdate ? Carbon::parse($this->mother_birthdate) : null,
                    'mother_nationality' => $this->mother_nationality,
                    'mother_religion' => $this->mother_religion,
                    'mother_contact_no' => $this->mother_contact_no,
                ]);
            }

            // Death Certificate specific fields
            if ($this->service->slug === 'death-certificate') {
                $detailsData = array_merge($detailsData, [
                    'deceased_last_name' => $this->deceased_last_name,
                    'deceased_first_name' => $this->deceased_first_name,
                    'deceased_middle_name' => !empty(trim($this->deceased_middle_name)) ? $this->deceased_middle_name : 'N/A',
                    'deceased_father_last_name' => $this->deceased_father_last_name,
                    'deceased_father_first_name' => $this->deceased_father_first_name,
                    'deceased_father_middle_name' => !empty(trim($this->deceased_father_middle_name)) ? $this->deceased_father_middle_name : 'N/A',
                    'deceased_mother_last_name' => $this->deceased_mother_last_name,
                    'deceased_mother_first_name' => $this->deceased_mother_first_name,
                    'deceased_mother_middle_name' => !empty(trim($this->deceased_mother_middle_name)) ? $this->deceased_mother_middle_name : 'N/A',
                ]);
            }

            // Contact Information (for third-party requests, excluding special permit)
            if ($this->service->slug !== 'special-permit') {
                $detailsData = array_merge($detailsData, [
                    'contact_first_name' => $this->contact_first_name,
                    'contact_last_name' => $this->contact_last_name,
                    'contact_middle_name' => !empty(trim($this->contact_middle_name)) ? $this->contact_middle_name : 'N/A',
                    'contact_email' => $this->contact_email,
                    'contact_phone' => $this->contact_phone,
                ]);
            }
            // Create the document request details
            // dd($detailsData);
            DocumentRequestDetails::create($detailsData);

            // Store the document ID for the payment step
            $this->id = $documentRequest->id;

            DB::commit();

            // Prepare notification summary based on service type
            if ($this->service->slug === 'special-permit') {
                $summary = $this->service->title . ' for ' . $this->establishment_name;
            } elseif ($this->service->slug === 'death-certificate') {
                $summary = $this->service->title . ' for ' . $this->deceased_first_name . ' ' . $this->deceased_last_name;
            } else {
                $summary = $this->service->title . ' for ' . $this->first_name . ' ' . $this->last_name;
            }

            // Send notification to user
            auth()
                ->user()
                ->notify(
                    new RequestEventNotification(RequestNotificationEvent::Submitted, [
                        'reference_no' => $this->reference_number,
                        'summary' => $summary,
                    ]),
                );

            // Send notification to staff
            $staffs = User::getStaffsByOfficeId($this->office->id);
            if ($staffs->count() > 0) {
                foreach ($staffs as $staff) {
                    $staff->notify(
                        new AdminEventNotification(AdminNotificationEvent::UserSubmittedDocumentRequest, [
                            'reference_no' => $this->reference_number,
                            'summary' => $summary,
                        ]),
                    );
                }
            }

            session()->flash('success', 'Document request submitted successfully!');

            // Move to payment step instead of redirecting
            $this->step = 8;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Document Request Submission Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting your request. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function editPersonDetailsBtn()
    {
        $this->editPersonDetails = true;
    }

    public function lockPersonDetailsBtn()
    {
        $this->editPersonDetails = false;
    }

    public function completePayment()
    {
        try {
            $this->isLoading = true;

            // Validate inputs
            $validatedData = $this->validate([
                'selectedPaymentMethod' => 'required|in:gcash,bdo,maya',
                'paymentProofImage' => 'required|image|max:2048',
            ]);

            // Try to find the document request by reference number if ID is missing
            if (!empty($this->reference_number)) {
                // Find by reference number instead
                $documentRequest = DocumentRequest::where('reference_number', $this->reference_number)
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$documentRequest) {
                    throw new \Exception('Document request not found. Please check your request and try again.');
                }

                // Update the ID for future use
                $this->id = $documentRequest->id;
            } else {
                throw new \Exception('Document request information is missing. Please try again.');
            }

            // Save payment proof
            $paymentProofPath = null;
            try {
                $paymentProofPath = $this->paymentProofImage->storeAs('payment-proofs', 'payment_' . $this->reference_number . '.' . $this->paymentProofImage->getClientOriginalExtension(), 'public');
            } catch (\Exception $fileException) {
                Log::error('File upload error: ' . $fileException->getMessage());
                // Continue without file path if there's an error
                $paymentProofPath = 'payment-pending';
            }

            // Update payment status using the model
            // Payment proof upload sets status to 'unpaid' - staff will verify and update to 'paid' or 'failed'
            $documentRequest->payment_status = 'unpaid';

            // Only set these fields if they exist in the database
            $documentRequest->payment_method = $this->selectedPaymentMethod;

            if ($paymentProofPath) {
                $documentRequest->payment_proof_path = $paymentProofPath;
            }

            $documentRequest->payment_date = now();
            $documentRequest->save();

            // Prepare notification summary based on service type
            if ($this->service->slug === 'special-permit') {
                $summary = $this->service->title . ' for ' . $this->establishment_name;
            } elseif ($this->service->slug === 'death-certificate') {
                // Get deceased name from document request details
                $details = $documentRequest->details;
                if ($details) {
                    $summary = $this->service->title . ' for ' . $details->deceased_first_name . ' ' . $details->deceased_last_name;
                } else {
                    $summary = $this->service->title . ' for ' . ($this->deceased_first_name ?? '') . ' ' . ($this->deceased_last_name ?? '');
                }
            } else {
                $summary = $this->service->title . ' for ' . $this->first_name . ' ' . $this->last_name;
            }

            // Send notification to staff
            $staffs = User::getStaffsByOfficeId($this->office->id);
            if ($staffs->count() > 0) {
                foreach ($staffs as $staff) {
                    $staff->notify(
                        new AdminEventNotification(AdminNotificationEvent::UserPayedDocumentRequest, [
                            'reference_no' => $this->reference_number,
                            'summary' => $summary,
                            'payment_method' => $this->selectedPaymentMethod,
                        ]),
                    );
                }
            }

            // Move to success page
            $this->step = 9;
        } catch (\Exception $e) {
            Log::error('Payment Processing Error: ' . $e->getMessage());
            session()->flash('error', 'Payment processing failed: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function getGovernmentIdImageUrl()
    {
        if ($this->government_id_image_file) {
            return $this->government_id_image_file->temporaryUrl();
        }

        if ($this->government_id_image_path) {
            return asset('storage/' . $this->government_id_image_path);
        }

        return null;
    }

    // Sample validation rules for marriage license modular fields
    public function marriageLicenseRules(): array
    {
        return [
            // Groom
            'groom_first_name' => 'required|string|max:255',
            'groom_middle_name' => 'nullable|string|max:255',
            'groom_last_name' => 'required|string|max:255',
            'groom_suffix' => 'nullable|string|max:10',
            'groom_age' => 'required|numeric|min:18',
            'groom_date_of_birth' => 'required|date',
            'groom_place_of_birth' => 'required|string|max:255',
            'groom_sex' => 'required|in:Male,Female',
            'groom_citizenship' => 'required|string|max:255',
            'groom_residence' => 'required|string|max:255',
            'groom_religion' => 'required|string|max:255',
            'groom_civil_status' => 'required|string|max:50',
            // Groom Father
            'groom_father_first_name' => 'required|string|max:255',
            'groom_father_middle_name' => 'nullable|string|max:255',
            'groom_father_last_name' => 'required|string|max:255',
            'groom_father_suffix' => 'nullable|string|max:10',
            'groom_father_citizenship' => 'required|string|max:255',
            'groom_father_residence' => 'required|string|max:255',
            // Groom Mother
            'groom_mother_first_name' => 'required|string|max:255',
            'groom_mother_middle_name' => 'nullable|string|max:255',
            'groom_mother_last_name' => 'required|string|max:255',
            'groom_mother_suffix' => 'nullable|string|max:10',
            'groom_mother_citizenship' => 'required|string|max:255',
            'groom_mother_residence' => 'required|string|max:255',
            // Bride
            'bride_first_name' => 'required|string|max:255',
            'bride_middle_name' => 'nullable|string|max:255',
            'bride_last_name' => 'required|string|max:255',
            'bride_suffix' => 'nullable|string|max:10',
            'bride_age' => 'required|numeric|min:18',
            'bride_date_of_birth' => 'required|date',
            'bride_place_of_birth' => 'required|string|max:255',
            'bride_sex' => 'required|in:Male,Female',
            'bride_citizenship' => 'required|string|max:255',
            'bride_residence' => 'required|string|max:255',
            'bride_religion' => 'required|string|max:255',
            'bride_civil_status' => 'required|string|max:50',
            // Bride Father
            'bride_father_first_name' => 'required|string|max:255',
            'bride_father_middle_name' => 'nullable|string|max:255',
            'bride_father_last_name' => 'required|string|max:255',
            'bride_father_suffix' => 'nullable|string|max:10',
            'bride_father_citizenship' => 'required|string|max:255',
            'bride_father_residence' => 'required|string|max:255',
            // Bride Mother
            'bride_mother_first_name' => 'required|string|max:255',
            'bride_mother_middle_name' => 'nullable|string|max:255',
            'bride_mother_last_name' => 'required|string|max:255',
            'bride_mother_suffix' => 'nullable|string|max:10',
            'bride_mother_citizenship' => 'required|string|max:255',
            'bride_mother_residence' => 'required|string|max:255',
        ];
    }

    public function with()
    {
        return [
            'services' => $this->office->services()->where('is_active', 1)->get(),
        ];
    }
}; ?>


<div class="card shadow-xl border-none border-gray-200" style="border-radius: 1rem;">
    <h1 class="text-2xl text-base-content mt-3 py-2 text-center">Request a Document on <span
            class="font-bold">{{ $this->office->name }}</span> Office
    </h1>

    {{-- Stepper Header --}}
    <div class="px-5 py-2 mt-5">
        <ul class="steps steps-horizontal w-full">
            <li class="step {{ $step >= 1 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Accept Terms</div>
                    <div class="step-description text-sm text-gray-500">Accept Terms and Conditions</div>
                </div>
            </li>
            <li class="step {{ $step >= 2 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Certificate</div>
                    <div class="step-description text-sm text-gray-500">Select a Certificate Type</div>
                </div>

            </li>
            <li class="step {{ $step >= 3 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">To Whom?</div>
                    <div class="step-description text-sm text-gray-500">For Yourself or Someone Else?</div>
                </div>

            </li>
            <li class="step {{ $step >= 4 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Purpose Request</div>
                    <div class="step-description text-sm text-gray-500">State your purpose</div>
                </div>

            </li>
            <li class="step {{ $step >= 5 ? 'step-info' : '' }}">
                <div class="step-content">
                    @php
                        if ($this->service->slug === 'marriage-certificate') {
                            $stepTitle = 'Marriage License';
                            $stepDescription = 'Marriage License Information';
                        } elseif ($this->service->slug === 'death-certificate') {
                            $stepTitle = 'Death Certificate';
                            $stepDescription = 'Death Certificate Information';
                        } elseif ($this->service->slug === 'special-permit') {
                            $stepTitle = 'Special Permit';
                            $stepDescription = 'Special Permit Information';
                        } else {
                            $stepTitle = 'Personal Information';
                            $stepDescription = 'Your/Someone details';
                        }
                    @endphp
                    <div class="step-title">{{ $stepTitle }}</div>
                    <div class="step-description text-sm text-gray-500">{{ $stepDescription }}</div>
                </div>

            </li>
            @if ($this->service->slug !== 'special-permit')
                <li class="step {{ $step >= 6 ? 'step-info' : '' }}">
                    <div class="step-content">
                        <div class="step-title">Contact Information</div>
                        <div class="step-description text-sm text-gray-500">How to reach you</div>
                    </div>
                </li>
            @endif
            <li class="step {{ $step >= 7 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Confirmation</div>
                    <div class="step-description text-sm text-gray-500">Review & Submit</div>
                </div>
            </li>
            <li class="step {{ $step >= 8 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Payment</div>
                    <div class="step-description text-sm text-gray-500">Complete your transaction</div>
                </div>
            </li>
            <li class="step {{ $step >= 9 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Success</div>
                    <div class="step-description text-sm text-gray-500">Done</div>
                </div>
            </li>

        </ul>
    </div>
    {{-- Stepper Content --}}
    @if ($step == 1)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    {{-- <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Terms and Conditions</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please read and accept the terms and conditions.</span>
                        </div>
                    </div> --}}

                    <div class="modal-body">
                        <div class="text-sm space-y-4 max-h-92 overflow-y-auto p-4">
                            <h4 class="font-bold text-xl mb-2">Terms and Conditions</h4>
                            <p>By using this system, you acknowledge that you have read, understood, and agreed to the
                                following Terms and Disclaimer. These terms govern your use of our online document
                                request and appointment scheduling system. Please read them carefully before proceeding.
                            </p>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">1. Scope of Services</h5>
                                <p>This system facilitates the <strong>online request and appointment
                                        scheduling</strong> for official documents. The actual processing, verification,
                                    and release of the requested documents are conducted <strong>solely by the
                                        designated Municipal or Local Civil Registry Office (LCRO)</strong>.</p>
                                <p class="mt-2">Our system <strong>does not provide delivery, mailing, or courier
                                        services</strong>. All requested documents must be <strong>personally claimed by
                                        the document owner or authorized representative</strong> at the designated
                                    office during the appointed schedule.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">2. User Responsibilities</h5>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>You affirm that all information provided in your request is <strong>true,
                                            complete, and accurate</strong>.</li>
                                    <li>You understand that any errors, omissions, or false declarations may result in
                                        processing delays or cancellation of your request.</li>
                                    <li>You are required to bring <strong>valid government-issued
                                            identification</strong> for verification upon claiming your document.</li>
                                    <li>If you are an authorized representative, you must present an
                                        <strong>authorization letter</strong> and valid IDs of both the requester and
                                        the owner of the document.
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">3. Payment and Proof of Transaction</h5>
                                <p>All payments must be made accurately to the <strong>official account details provided
                                        by our system</strong>. You are required to upload a <strong>valid and clear
                                        screenshot of your GCash payment receipt</strong> as proof of transaction.</p>
                                <p class="mt-2 text-justify">Please note that we <strong>do not process refunds</strong>
                                    for incorrect, incomplete, or misdirected payments. Any errors in the amount sent or
                                    transfers made to the wrong account are the sole responsibility of the sender.
                                    Payments will only be considered valid upon successful verification of the uploaded
                                    receipt.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">4. Document Processing and Release</h5>
                                <p>The processing of requested documents is performed by the authorized municipal
                                    offices. Processing time may vary depending on the completeness of your requirements
                                    and the verification procedures of the issuing office.</p>
                                <p class="mt-2">You will be notified through the system or official communication
                                    channels once your document is ready for pickup. Documents not claimed within the
                                    prescribed period may be subject to cancellation or revalidation, depending on
                                    office policy.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">5. Disclaimer of Liability</h5>
                                <p>While we exercise reasonable care in maintaining this system, we make no warranty
                                    that its operation will be uninterrupted, error-free, or secure at all times. We
                                    shall not be held liable for any losses, damages, or inconveniences caused by:</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>User’s failure to provide accurate or complete information;</li>
                                    <li>Improper or failed payment transactions;</li>
                                    <li>System downtime, network issues, or other unforeseen technical problems;</li>
                                    <li>Delays, errors, or decisions made by the municipal office during document
                                        processing.</li>
                                </ul>
                                <p class="mt-2">The system serves only as an <strong>online facilitation and
                                        appointment tool</strong>. The issuing government office retains full authority
                                    and responsibility over the processing and release of all official documents.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">6. Data Privacy and Confidentiality</h5>
                                <p>All personal information collected through this system is handled in accordance with
                                    the <strong>Data Privacy Act of 2012 (RA 10173)</strong> and our <a
                                        href="/privacy-policy" class="text-primary hover:underline">Data Privacy
                                        Notice</a>. We ensure that your personal data is used only for legitimate
                                    purposes and is protected against unauthorized access or disclosure.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">7. Policy Updates</h5>
                                <p>We reserve the right to update or modify these Terms and Disclaimer at any time
                                    without prior notice. Any changes will take effect immediately upon posting to this
                                    page. Continued use of the system constitutes acceptance of the revised terms.</p>
                            </div>
                            <br />
                            <br />
                            <br />
                            <br />
                            <h4 class="font-bold text-xl mb-2">Data Privacy Notice</h4>
                            <p>We respect your right to privacy and are committed to protecting the personal information
                                you provide through this system. This notice explains how we collect, use, store, and
                                secure your data in compliance with the <strong>Data Privacy Act of 2012 (Republic Act
                                    No. 10173)</strong> and its Implementing Rules and Regulations (IRR).</p>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">1. Purpose of Data Collection</h5>
                                <p>This system collects and processes personal information solely for legitimate
                                    purposes related to the <strong>online request, scheduling, and processing of
                                        official documents</strong>.</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>Verify your identity and eligibility to request official documents;</li>
                                    <li>Schedule appointments and manage request records;</li>
                                    <li>Process and confirm payments;</li>
                                    <li>Notify you of request or appointment updates; and</li>
                                    <li>Comply with legal, auditing, and administrative requirements.</li>
                                </ul>
                                <p class="mt-2">We <strong>do not engage in courier or delivery services</strong>. All
                                    requested documents must be <strong>personally claimed at the designated
                                        office</strong> upon presentation of a valid government-issued ID and proof of
                                    payment.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">2. Information We Collect</h5>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li><strong>Personal information:</strong> Full name, date of birth, contact number,
                                        email address, and other data required for document processing;</li>
                                    <li><strong>Supporting documents:</strong> Valid identification cards and payment
                                        receipts uploaded as proof of transaction;</li>
                                    <li><strong>Technical information:</strong> IP address, browser type, access time,
                                        and system logs for security and verification purposes.</li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">3. Data Usage and Processing</h5>
                                <p>Your personal information shall be used <strong>only for official and lawful
                                        purposes</strong>, including verification, appointment confirmation, and record
                                    management. We ensure that your data is processed <strong>fairly, accurately, and
                                        securely</strong>, and is retained <strong>only for as long as
                                        necessary</strong> to fulfill its intended purpose.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">4. Data Storage and Security</h5>
                                <p>All collected information is stored in <strong>secured databases</strong> accessible
                                    only to authorized personnel. We implement organizational, physical, and technical
                                    measures to protect your data against unauthorized access, alteration, disclosure,
                                    or destruction.</p>
                                <p class="mt-2">Uploaded files such as IDs and receipts are <strong>encrypted</strong>
                                    and automatically deleted after completion of the document request or after the
                                    legally required retention period.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">5. Data Sharing and Disclosure</h5>
                                <p>Your information will <strong>not be shared</strong> with any third party outside of
                                    our organization unless:</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>Required by law, court order, or government regulation;</li>
                                    <li>Necessary to comply with legal obligations; or</li>
                                    <li>Authorized by you in writing.</li>
                                </ul>
                                <p class="mt-2">We do <strong>not sell, rent, or trade</strong> personal information
                                    to any external entity.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">6. Your Rights Under the Data Privacy Act
                                </h5>
                                <p>As a data subject, you have the following rights under Republic Act No. 10173:</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li><strong>Right to be informed</strong> – to know how your personal data is
                                        collected, processed, and stored;</li>
                                    <li><strong>Right to access</strong> – to request a copy of your personal data in
                                        our records;</li>
                                    <li><strong>Right to rectification</strong> – to correct any inaccurate or outdated
                                        information;</li>
                                    <li><strong>Right to erasure or blocking</strong> – to request deletion or blocking
                                        of data that is no longer necessary;</li>
                                    <li><strong>Right to object</strong> – to refuse processing of your data under
                                        certain conditions; and</li>
                                    <li><strong>Right to file a complaint</strong> – to the <strong>National Privacy
                                            Commission (NPC)</strong> in case of any privacy violation.</li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">7. Data Retention Policy</h5>
                                <p>Personal information shall be retained only for as long as necessary to complete the
                                    service or as required by applicable laws and government regulations. After the
                                    retention period, all personal data shall be <strong>securely disposed of or
                                        anonymized</strong> to prevent unauthorized access.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">Contact Information</h5>
                                <p>For concerns or clarifications regarding these terms, you may contact:</p>
                                <div class="bg-base-200 p-3 rounded-lg mt-2 text-sm">
                                    <p><strong>Support Team</strong></p>
                                    <p>📧 <a href="mailto:santotomasdavnor@gov.ph"
                                            class="text-primary hover:underline">santotomasdavnor@gov.ph</a></p>
                                    <p>📞 +6392-232-2332</p>
                                    <p>🏢 santotomasdavnor@gov.ph</p>
                                </div>
                            </div>

                            <p class="italic text-xs text-gray-500 mt-4">Last Updated: October 2025</p>
                        </div>
                    </div>


                    <div class="flex justify-center mt-6">
                        <label for="termsAccepted"
                            class="flex items-center justify-center gap-2 cursor-pointer text-sm sm:text-base">
                            <input type="checkbox" wire:model.live="termsAccepted" id="termsAccepted"
                                class="checkbox checkbox-primary w-5 h-5 shrink-0" />
                            <span class="text-lg font-medium">
                                I accept the
                                <a href="/terms-and-disclaimer" class="text-blue-500 hover:underline ml-1">
                                    Terms and Conditions
                                </a>
                            </span>
                        </label>
                    </div>



                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-primary" type="button" wire:click="nextStep" @if (!$termsAccepted) disabled
                        @endif>Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 2)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Select a Certificate</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please select a certificate</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center ">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                        @if ($office->slug === 'business-permits-and-licensing-section')
                            <input type="radio" id="special-permit" name="service" value="special-permit"
                                wire:model.live="serviceSelected" hidden />
                            <label for="special-permit"
                                class="flux-input-primary flux-btn cursor-pointer {{ $service->slug === $serviceSelected ? 'flux-btn-active-primary' : '' }} p-2">Special
                                Permit
                            </label>
                        @else
                            @foreach ($services as $service)
                                <input type="radio" id="{{ $service->slug }}" name="service" value="{{ $service->slug }}"
                                    wire:model.live="serviceSelected" hidden />
                                <label for="{{ $service->slug }}"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $service->slug === $serviceSelected ? 'flux-btn-active-primary' : '' }} p-2">
                                    {{ $service->title }}
                                </label>
                            @endforeach
                        @endif
                    </div>



                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" type="button" wire:click="previousStep">Previous</button>
                        <button class="btn btn-primary" type="button" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 3)
        <div class="px-5 py-2 mt-5" wire:key="document-request-step-1">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">For Yourself or Someone Else?</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please select who this document request is for</span>
                        </div>
                    </div>


                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center ">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    @error('relationship')
                        <div class="alert alert-danger flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                        @if ($this->service->title === 'Death Certificate')
                                        <?php
                            $to_whom = 'someone_else';
                                                ?>
                                        <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden
                                            disabled />
                                        <label for="myself"
                                            class="flux-input-primary flux-btn cursor-pointer opacity-50 cursor-not-allowed p-2">Myself
                                            (Not available for Death Certificate)</label>
                                        <input type="radio" id="someone_else" name="to_whom" value="someone_else" wire:model.live="to_whom"
                                            hidden />
                                        <label for="someone_else"
                                            class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
                                            Else</label>
                        @else
                            <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden />
                            <label for="myself"
                                class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'myself' ? 'flux-btn-active-primary' : '' }} p-2">Myself</label>
                            <input type="radio" id="someone_else" name="to_whom" value="someone_else" wire:model.live="to_whom"
                                hidden />
                            <label for="someone_else"
                                class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
                                Else</label>
                        @endif
                    </div>

                    @if ($to_whom === 'someone_else')
                        <div class="mt-4" wire:loading.remove>
                            <div class="header mb-4">
                                <h3 class="text-lg font-semibold text-base-content">Select Relationship</h3>
                                <div class="flex items-center gap-2 text-sm text-base-content/70">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Please select your relationship to the person</span>
                                </div>
                            </div>

                            <?php        $relationship = $relationship ?? ''; ?>
                            <div class="flex flex-col gap-2 w-full">
                                <input type="radio" id="my_father" name="relationship" value="my_father"
                                    wire:model.live="relationship" hidden />
                                <label for="my_father"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_father' ? 'flux-btn-active-primary' : '' }} p-2">My
                                    Father</label>

                                <input type="radio" id="my_mother" name="relationship" value="my_mother"
                                    wire:model.live="relationship" hidden />
                                <label for="my_mother"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_mother' ? 'flux-btn-active-primary' : '' }} p-2">My
                                    Mother</label>

                                <input type="radio" id="my_son" name="relationship" value="my_son"
                                    wire:model.live="relationship" hidden />
                                <label for="my_son"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_son' ? 'flux-btn-active-primary' : '' }} p-2">My
                                    Son</label>

                                <input type="radio" id="my_daughter" name="relationship" value="my_daughter"
                                    wire:model.live="relationship" hidden />
                                <label for="my_daughter"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_daughter' ? 'flux-btn-active-primary' : '' }} p-2">My
                                    Daughter</label>

                                @if($this->service->title !== 'Death Certificate')
                                    <input type="radio" id="others_relationship" name="relationship" value="others"
                                        wire:model.live="relationship" hidden />
                                    <label for="others_relationship"
                                        class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'others' ? 'flux-btn-active-primary' : '' }} p-2">Others</label>
                                @else
                                    <input type="radio" id="my_wife" name="relationship" value="my_wife"
                                        wire:model.live="relationship" hidden />
                                    <label for="my_wife"
                                        class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_wife' ? 'flux-btn-active-primary' : '' }} p-2">My
                                        Wife</label>
                                @endif
                            </div>

                            @if ($relationship === 'others')
                                <div class="mt-4">
                                    <input type="text" placeholder="Please specify relationship" class="flux-form-control"
                                        wire:model="relationship_others" />
                                    @error('relationship_others')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" type="button" wire:click="previousStep">Previous</button>
                        <button class="btn btn-primary" type="button" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>

        <!-- @ include('livewire.documentrequest.components.document-request-steps.step1') -->
    @elseif($step == 4)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">State your purpose</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please select the purpose of your document request</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center ">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    @error('purpose')
                        <div class="alert alert-danger flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror


                    @if ($this->service->title !== 'Death Certificate')
                        <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                            <input type="radio" id="school_requirement" name="purpose" value="school_requirement"
                                wire:model.live="purpose" hidden />
                            <label for="school_requirement"
                                class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'school_requirement' ? 'flux-btn-active-primary' : '' }} p-2">School
                                requirement</label>

                            <input type="radio" id="employment" name="purpose" value="employment" wire:model.live="purpose"
                                hidden />
                            <label for="employment"
                                class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'employment' ? 'flux-btn-active-primary' : '' }} p-2">Employment</label>

                            <input type="radio" id="government_id" name="purpose" value="government_id"
                                wire:model.live="purpose" hidden />
                            <label for="government_id"
                                class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'government_id' ? 'flux-btn-active-primary' : '' }} p-2">Government
                                ID</label>

                            <input type="radio" id="hospital_use" name="purpose" value="hospital_use" wire:model.live="purpose"
                                hidden />
                            <label for="hospital_use"
                                class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'hospital_use' ? 'flux-btn-active-primary' : '' }} p-2">Hospital
                                use</label>

                            <input type="radio" id="others" name="purpose" value="others" wire:model.live="purpose" hidden />
                            <label for="others"
                                class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'others' ? 'flux-btn-active-primary' : '' }} p-2">Others</label>
                        </div>

                        @if ($purpose === 'others')
                            <div class="mt-4">
                                <input type="text" placeholder="Please specify purpose" class="flux-form-control"
                                    wire:model="purpose_others"
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                                @error('purpose_others')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    @else
                        <!-- Purpose for Requesting -->
                        <div class="mb-4" wire:loading.remove>
                            <h5 class="text-md font-semibold mb-3">Purpose for Requesting the Certificate</h5>
                            <div class="flex flex-col gap-2 w-full">
                                <input type="radio" id="legal_proceedings" name="purpose" value="Legal Proceedings"
                                    wire:model.live="purpose" hidden />
                                <label for="legal_proceedings"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Legal Proceedings' ? 'flux-btn-active-primary' : '' }} p-2">Legal
                                    Proceedings</label>

                                <input type="radio" id="insurance_claims" name="purpose" value="Insurance Claims"
                                    wire:model.live="purpose" hidden />
                                <label for="insurance_claims"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Insurance Claims' ? 'flux-btn-active-primary' : '' }} p-2">Insurance
                                    Claims</label>

                                <input type="radio" id="property_transfer" name="purpose" value="Property Transfer"
                                    wire:model.live="purpose" hidden />
                                <label for="property_transfer"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Property Transfer' ? 'flux-btn-active-primary' : '' }} p-2">Property
                                    Transfer</label>

                                <input type="radio" id="social_security" name="purpose" value="Social Security Benefits"
                                    wire:model.live="purpose" hidden />
                                <label for="social_security"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Social Security Benefits' ? 'flux-btn-active-primary' : '' }} p-2">Social
                                    Security Benefits</label>

                                <input type="radio" id="veterans_benefits" name="purpose" value="Veterans Benefits"
                                    wire:model.live="purpose" hidden />
                                <label for="veterans_benefits"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Veterans Benefits' ? 'flux-btn-active-primary' : '' }} p-2">Veterans
                                    Benefits</label>

                                <input type="radio" id="genealogy_research" name="purpose" value="Genealogy Research"
                                    wire:model.live="purpose" hidden />
                                <label for="genealogy_research"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Genealogy Research' ? 'flux-btn-active-primary' : '' }} p-2">Genealogy
                                    Research</label>

                                <input type="radio" id="personal_records" name="purpose" value="Personal Records"
                                    wire:model.live="purpose" hidden />
                                <label for="personal_records"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Personal Records' ? 'flux-btn-active-primary' : '' }} p-2">Personal
                                    Records</label>

                                <input type="radio" id="other_purpose" name="purpose" value="Other" wire:model.live="purpose"
                                    hidden />
                                <label for="other_purpose"
                                    class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Other' ? 'flux-btn-active-primary' : '' }} p-2">Other</label>
                            </div>

                            @if ($purpose === 'Other')
                                <div class="mt-4">
                                    <input type="text" placeholder="Please specify purpose" class="flux-form-control"
                                        wire:model="purpose_details"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                                    @error('purpose_details')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif


                        </div>
                    @endif

                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                        <button class="btn btn-primary" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>

        <!-- @ include('livewire.documentrequest.components.document-request-steps.step2') -->
    @elseif($step == 5)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">


                {{-- Personal Information Section --}}
                @if (
                        $this->service->slug !== 'marriage-certificate' &&
                        $this->service->slug !== 'death-certificate' &&
                        $this->service->slug !== 'special-permit'
                    )
                    <div class="PersonalInformation">
                        <div class="header mb-4 flex flex-row justify-between items-center">
                            <div>
                                <h3 class="text-xl font-semibold text-base-content">Personal Information</h3>
                                <div class="flex items-center gap-2 text-sm text-base-content/70">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Please provide complete personal information</span>
                                </div>

                            </div>
                            <div class="flex flex-row gap-2 mb-4">
                                @if ($editPersonDetails === false)
                                    <button type="button" class="flux-btn flux-btn-primary"
                                        wire:click="editPersonDetailsBtn">Edit</button>
                                @else
                                    <button type="button" class="flux-btn flux-btn-primary"
                                        wire:click="lockPersonDetailsBtn">Lock</button>
                                @endif
                            </div>

                        </div>

                        <!-- Info Redundancy Error -->
                        @error('info_redundancy')
                            <div class="alert alert-error mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <!-- Info: N/A Guidance -->
                        <div class="alert alert-info flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>If a field is not applicable, you may enter <span class="font-semibold">"N/A"</span>.</span>
                        </div>
                        @if ($to_whom === 'someone_else')
                            <div class="alert alert-info flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Please bring a valid ID and ensure the requester is present.</span>
                            </div>
                        @endif
                        <!-- Auto-fill Information Message -->
                        @if ($to_whom === 'myself')
                            <div class="alert alert-info flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    class="stroke-current shrink-0 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Your profile information has been automatically filled. You can edit any field if
                                    needed.</span>
                            </div>
                        @else
                            <div class="alert alert-warning flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <span>Please enter the complete information for the person you're requesting the document
                                    for.</span>
                            </div>
                        @endif
                        <!-- Loading State -->
                        <div wire:loading.delay class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                            <p class="text-gray-600">Loading...</p>
                        </div>



                        <div class="flex flex-col gap-6 w-full" wire:loading.remove>
                            <div class="flux-card p-6">
                                <h4 class="text-lg font-bold mb-4">Basic Information</h4>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Name', 'last_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="last_name" placeholder="Last Name" name="last_name"
                                            id="last_name" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Name', 'first_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="first_name" placeholder="First Name" name="first_name"
                                            id="first_name" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Name', 'middle_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="middle_name" placeholder="Middle Name" name="middle_name"
                                            id="middle_name" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-1/7">
                                        <label for="suffix" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Suffix', 'suffix') }}</label>
                                        <select
                                            class="flux-form-control md:col-span-1 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model="suffix" name="suffix" id="suffix" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                            <option value="">Suffix</option>
                                            <option value="N/A">N/A</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                            <option value="I">I</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                        </select>
                                        @error('suffix')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="email" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Email', 'email') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            name="email" type="email" wire:model="email" placeholder="Email" id="email" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('email')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Contact No', 'contact_no') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            name="phone" type="tel" inputmode="numeric" pattern="[0-9]*"
                                            minlength="11" maxlength="11"
                                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                            wire:model="phone" placeholder="Contact No" id="phone" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('phone')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div>
                                        <label for="sex_at_birth" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Sex at Birth', 'sex_at_birth') }}</label>
                                        <select
                                            class="flux-form-control w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model="sex_at_birth" name="sex_at_birth" id="sex_at_birth"
                                            aria-label="Sex at Birth" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif>
                                            <option value="">Select Sex at Birth</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        @error('sex_at_birth')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="date_of_birth" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Date of Birth', 'date_of_birth') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="date" wire:model="date_of_birth" placeholder="Date of Birth"
                                            name="date_of_birth" id="date_of_birth" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('date_of_birth')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div>
                                        <label for="place_of_birth" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Place of Birth', 'place_of_birth') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="place_of_birth" placeholder="Place of Birth"
                                            name="place_of_birth" id="place_of_birth" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('place_of_birth')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div>
                                        <label for="civil_status" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Civil Status', 'civil_status') }}</label>
                                        <select class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model="civil_status" name="civil_status" id="civil_status" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                            <option value="">Civil Status</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widowed">Widowed</option>
                                            <option value="Divorced">Divorced</option>
                                            <option value="Separated">Separated</option>
                                        </select>
                                        @error('civil_status')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="religion" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Religion', 'religion') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="religion" placeholder="Religion" name="religion"
                                            id="religion" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('religion')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div>
                                        <label for="nationality" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Nationality', 'nationality') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="nationality" placeholder="Nationality" name="nationality"
                                            id="nationality" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('nationality')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="government_id_type" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Government ID Type', 'government_id_type') }}</label>
                                        <select class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model="government_id_type" name="government_id_type" id="government_id_type"
                                            @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                            <option value="">Select Government ID Type *</option>
                                            <option value="SSS">SSS</option>
                                            <option value="GSIS">GSIS</option>
                                            <option value="TIN">TIN</option>
                                            <option value="PhilHealth">PhilHealth</option>
                                            <option value="Passport">Passport</option>
                                            <option value="Driver's License">Driver's License</option>
                                            <option value="UMID">UMID</option>
                                            <option value="Postal ID">Postal ID</option>
                                            <option value="Voter's ID">Voter's ID</option>
                                            <option value="Senior Citizen ID">Senior Citizen ID</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        @error('government_id_type')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div>
                                        <label for="government_id_image_file" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Government ID PDF', 'government_id_pdf') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="file" wire:model="government_id_image_file" name="government_id_image_file"
                                            id="government_id_image_file" accept=".pdf" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                        <span class="text-xs text-gray-500 mt-1">Upload a PDF copy of your government ID (Max
                                            5MB)</span>
                                        @error('government_id_image_file')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Address Information Section --}}
                    <div class="flux-card p-6">
                        @if ($to_whom === 'someone_else')
                            <div class="alert alert-info flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Please provide the complete address for the person you're requesting the document for.</span>
                            </div>
                        @endif
                        {{-- Permanent Address --}}
                        <div>
                            <h4 class="text-lg font-bold mb-4">{{ label_with_bisaya('Permanent Address', 'permanent_address') }}</h4>
                            <div>
                                <label for="address_type" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Address Type', 'address_type') }}</label>
                                <select class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                                    wire:model="address_type" name="address_type" id="address_type" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled @endif>
                                    <option value="">Address Type</option>
                                    <option value="Permanent">Permanent</option>
                                    <option value="Temporary">Temporary</option>
                                </select>
                                @error('address_type')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 mt-1">Required</span>
                            </div>
                            <div>
                                <label for="address_line_1" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Address Line 1', 'address_line_1') }}</label>
                                <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                                    type="text" wire:model="address_line_1" name="address_line_1" placeholder="Address Line 1"
                                    id="address_line_1" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                    @if ($same_as_personal_address) disabled @endif
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('address_line_1')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 mt-1">Required</span>
                            </div>
                            <div>
                                <label for="address_line_2" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Address Line 2', 'address_line_2') }}</label>
                                <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                                    type="text" wire:model="address_line_2" name="address_line_2" placeholder="Address Line 2"
                                    id="address_line_2" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                    @if ($same_as_personal_address) disabled @endif
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('address_line_2')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                            </div>
                            <div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex flex-col">
                                        <label for="region" class="text-xs font-medium mb-1">{{ label_with_bisaya('Region', 'region') }}</label>
                                        <select id="region"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="region" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select Region</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="province" class="text-xs font-medium mb-1">{{ label_with_bisaya('Province', 'province') }}</label>
                                        <select id="province"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="province" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select Province</option>
                                            @foreach ($provinces as $provinceKey => $provinceName)
                                                <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="city" class="text-xs font-medium mb-1">{{ label_with_bisaya('City', 'city') }}</label>
                                        <select id="city"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="city" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $cityKey => $cityName)
                                                <option value="{{ $cityKey }}">{{ $cityName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="barangay" class="text-xs font-medium mb-1">{{ label_with_bisaya('Barangay', 'barangay') }}</label>
                                        <select id="barangay"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="barangay" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select Barangay</option>
                                            @foreach ($barangays as $barangay)
                                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="street" class="text-xs font-medium mb-1">{{ label_with_bisaya('Street', 'street') }}</label>
                                        <input id="street"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="street" placeholder="Street" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="zip_code" class="text-xs font-medium mb-1">{{ label_with_bisaya('Zip Code', 'zip_code') }}</label>
                                        <input id="zip_code"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="tel" inputmode="numeric" pattern="[0-9]*"
                                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                            wire:model="zip_code" placeholder="Zip Code" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled
                                            @endif>
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Present Address --}}
                        <div>
                            <h4 class="text-lg font-bold mb-4">{{ label_with_bisaya('Present Address', 'present_address') }}</h4>
                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="same_as_personal_address" wire:model="same_as_personal_address"
                                    class="checkbox checkbox-primary mr-2" />
                                <label for="same_as_personal_address" class="text-sm">{{ label_with_bisaya('Same as personal address', 'same_as_personal_address') }}</label>
                            </div>
                            <div>
                                <label for="address_type" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Address Type', 'address_type') }}</label>
                                <select class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                                    wire:model="address_type" name="address_type" id="address_type" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled @endif>
                                    <option value="">Address Type</option>
                                    <option value="Permanent">Permanent</option>
                                    <option value="Temporary">Temporary</option>
                                </select>
                                @error('address_type')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 mt-1">Required</span>
                            </div>
                            <div>
                                <label for="address_line_1" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Address Line 1', 'address_line_1') }}</label>
                                <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                                    type="text" wire:model="address_line_1" name="address_line_1" placeholder="Address Line 1"
                                    id="address_line_1" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                    @if ($same_as_personal_address) disabled @endif
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('address_line_1')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 mt-1">Required</span>
                            </div>
                            <div>
                                <label for="address_line_2" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Address Line 2', 'address_line_2') }}</label>
                                <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                                    type="text" wire:model="address_line_2" name="address_line_2" placeholder="Address Line 2"
                                    id="address_line_2" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                    @if ($same_as_personal_address) disabled @endif
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('address_line_2')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                            </div>
                            <div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex flex-col">
                                        <label for="region" class="text-xs font-medium mb-1">{{ label_with_bisaya('Region', 'region') }}</label>
                                        <select id="region"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="region" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select Region</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="province" class="text-xs font-medium mb-1">{{ label_with_bisaya('Province', 'province') }}</label>
                                        <select id="province"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="province" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select Province</option>
                                            @foreach ($provinces as $provinceKey => $provinceName)
                                                <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="city" class="text-xs font-medium mb-1">{{ label_with_bisaya('City', 'city') }}</label>
                                        <select id="city"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="city" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $cityKey => $cityName)
                                                <option value="{{ $cityKey }}">{{ $cityName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="barangay" class="text-xs font-medium mb-1">{{ label_with_bisaya('Barangay', 'barangay') }}</label>
                                        <select id="barangay"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model.live="barangay" @if ($to_whom === 'myself' && $editPersonDetails === false)
                                            disabled @endif @if ($same_as_personal_address) disabled @endif>
                                            <option value="">Select Barangay</option>
                                            @foreach ($barangays as $barangay)
                                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="street" class="text-xs font-medium mb-1">{{ label_with_bisaya('Street', 'street') }}</label>
                                        <input id="street"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="street" placeholder="Street" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="zip_code" class="text-xs font-medium mb-1">{{ label_with_bisaya('Zip Code', 'zip_code') }}</label>
                                        <input id="zip_code"
                                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="tel" inputmode="numeric" pattern="[0-9]*"
                                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                            wire:model="zip_code" placeholder="Zip Code" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled
                                            @endif>
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Family Information Section (only if not Death Certificate) --}}
                    @if (($this->to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $this->service->slug !== 'death-certificate')
                        <div class="flux-card p-6" wire:loading.remove>
                            @if ($to_whom === 'someone_else')
                                <div class="alert alert-info flex items-center gap-2 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Please provide the complete information for the person you're requesting the document
                                        for.</span>
                                    <span>If a field is not applicable, you may enter <span class="font-semibold">"N/A"</span>.</span>
                                </div>
                            @endif
                            <h4 class="text-lg font-bold mb-4">{{ label_with_bisaya('Family Information', 'family_information') }}</h4>
                            {{-- Father --}}
                            <div>
                                <h5 class="text-md font-bold mb-4">{{ label_with_bisaya('Father', 'father') }}</h5>
                                <div class="form-control ">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">{{ label_with_bisaya('Father is Unknown', 'father_is_unknown') }}</span>
                                        <input type="checkbox" wire:model.live="father_is_unknown"
                                            class="checkbox @if ($editPersonDetails === false) bg-gray-100 @endif" @if ($editPersonDetails === false) disabled @endif />
                                    </label>
                                </div>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="father_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Name', 'father_last_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="father_last_name" placeholder="Last Name"
                                            name="father_last_name" id="father_last_name" required {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('father_last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Name', 'father_first_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="father_first_name" placeholder="First Name"
                                            name="father_first_name" id="father_first_name" required {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('father_first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Name', 'father_middle_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="father_middle_name" placeholder="Middle Name"
                                            name="father_middle_name" id="father_middle_name" required {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('father_middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-1/7">
                                        <label for="father_suffix" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Suffix', 'father_suffix') }}</label>
                                        <select
                                            class="flux-form-control md:col-span-1 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            wire:model="father_suffix" name="father_suffix" id="father_suffix" {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif>
                                            <option value="">Suffix</option>
                                            <option value="N/A">N/A</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                            <option value="I">I</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                        </select>
                                        @error('father_suffix')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                </div>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="father_birthdate" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Date of Birth', 'father_birthdate') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="date" wire:model="father_birthdate" name="father_birthdate" id="father_birthdate"
                                            {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('father_birthdate')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_nationality" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Nationality', 'father_nationality') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="father_nationality" placeholder="Nationality"
                                            name="father_nationality" id="father_nationality" required {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled
                                            @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('father_nationality')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_religion" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Religion', 'father_religion') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="father_religion" placeholder="Religion" name="father_religion"
                                            id="father_religion" required {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('father_religion')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_contact_no" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Contact No.', 'father_contact_no') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="tel" inputmode="numeric" pattern="[0-9]*"
                                            maxlength="11"
                                            x-on:input="$event.target.value = $event.target.value === 'N/A' ? 'N/A' : $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                            wire:model="father_contact_no" placeholder="Contact Number" name="father_contact_no"
                                            id="father_contact_no" required {{ $father_is_unknown ? 'disabled' : '' }} @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('father_contact_no')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Mother --}}
                            <div>
                                <h5 class="text-md font-bold mb-4">{{ label_with_bisaya('Mother', 'mother') }}</h5>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">{{ label_with_bisaya('Mother is Unknown', 'mother_is_unknown') }}</span>
                                        <input type="checkbox" wire:model.live="mother_is_unknown"
                                            class="checkbox @if ($editPersonDetails === false) bg-gray-100 @endif" @if ($editPersonDetails === false) disabled @endif />
                                    </label>
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Maiden Name', 'last_maiden_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="mother_last_name" placeholder="Last Name"
                                            name="mother_last_name" id="mother_last_name" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('mother_last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Required</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Maiden Name', 'first_maiden_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="mother_first_name" placeholder="First Name"
                                            name="mother_first_name" id="mother_first_name" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('mother_first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Maiden Name', 'middle_maiden_name') }}</label>
                                        <input
                                            class="flux-form-control md:col-span-3 w-full @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="mother_middle_name" placeholder="Middle Name"
                                            name="mother_middle_name" id="mother_middle_name" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('mother_middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                </div>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_birthdate" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Birthdate', 'mother_birthdate') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="date" wire:model="mother_birthdate" placeholder="Mother's Birthdate"
                                            name="mother_birthdate" id="mother_birthdate" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('mother_birthdate')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_nationality" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Nationality', 'mother_nationality') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="mother_nationality" placeholder="Mother's Nationality"
                                            name="mother_nationality" id="mother_nationality" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('mother_nationality')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_religion" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Religion', 'mother_religion') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="text" wire:model="mother_religion" placeholder="Mother's Religion"
                                            name="mother_religion" id="mother_religion" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('mother_religion')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_contact_no" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Contact No', 'mother_contact_no') }}</label>
                                        <input class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                                            type="tel" inputmode="numeric" pattern="[0-9]*"
                                            maxlength="11"
                                            x-on:input="$event.target.value = $event.target.value === 'N/A' ? 'N/A' : $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                            wire:model="mother_contact_no" placeholder="Mother's Contact No"
                                            name="mother_contact_no" id="mother_contact_no" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
                                        @error('mother_contact_no')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    @endif
                @endif
                {{-- Death Information Section (only if Death Certificate) --}}
                @if ($this->service->slug === 'death-certificate')
                    <div class="flux-card p-6">

                        @if ($to_whom === 'someone_else')
                            <div class="alert alert-info flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Please provide the complete information for the person you're requesting the document
                                    for.</span>
                            </div>
                        @endif
                        <h4 class="text-lg font-bold mb-4">Death Information</h4>
                        <!-- Full Name of the Deceased -->
                        <div class="mb-4">
                            <h5 class="text-md font-semibold mb-3">Full Name of the Deceased</h5>
                            <div class="flex flex-row md:flex-col gap-4 mb-4">
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Name', 'last_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_last_name"
                                        placeholder="Last Name" name="deceased_last_name" id="deceased_last_name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_last_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Required</span>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Name', 'first_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_first_name"
                                        placeholder="First Name" name="deceased_first_name" id="deceased_first_name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_first_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Required</span>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Name', 'middle_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_middle_name"
                                        placeholder="Middle Name" name="deceased_middle_name" id="deceased_middle_name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_middle_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                            </div>
                        </div>

                        <!-- Parental Information -->
                        <div class="mb-4">
                            <h5 class="text-md font-semibold mb-3">Father of the Deceased</h5>
                            <div class="flex flex-row md:flex-col gap-4 mb-4">
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_father_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Name', 'last_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_father_last_name"
                                        id="deceased_father_last_name" name="deceased_father_last_name" placeholder="Last Name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_father_last_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_father_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Name', 'first_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_father_first_name"
                                        id="deceased_father_first_name" name="deceased_father_first_name"
                                        placeholder="First Name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_father_first_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_father_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Name', 'middle_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_father_middle_name"
                                        id="deceased_father_middle_name" name="deceased_father_middle_name"
                                        placeholder="Middle Name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_father_middle_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                            </div>
                            <h5 class="text-md font-semibold mb-3">Mother of the Deceased</h5>
                            <div class="flex flex-row md:flex-col gap-4 mb-4">
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_mother_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Maiden Name', 'last_maiden_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_mother_last_name"
                                        id="deceased_mother_last_name" name="deceased_mother_last_name" placeholder="Last Name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_mother_last_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_mother_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Maiden Name', 'first_maiden_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_mother_first_name"
                                        id="deceased_mother_first_name" name="deceased_mother_first_name"
                                        placeholder="First Name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_mother_first_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <label for="deceased_mother_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Maiden Name', 'middle_maiden_name') }}</label>
                                    <input class="flux-form-control" type="text" wire:model="deceased_mother_middle_name"
                                        id="deceased_mother_middle_name" name="deceased_mother_middle_name"
                                        placeholder="Middle Name"
                                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                    @error('deceased_mother_middle_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                                </div>
                            </div>
                        </div>

                    </div>

                @endif

                {{-- Marriage Certificate Section (only if Marriage Certificate) --}}
                @if ($this->service->slug === 'marriage-certificate')
                    {{-- Marriage License (Read-Only) --}}
                    <div class="mirrage-license">
                        @if ($to_whom === 'someone_else')
                            <div class="alert alert-info flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Please provide the complete information for the person you're requesting the document
                                    for.</span>
                            </div>
                        @endif
                        <div class="header mb-4">
                            <h3 class="text-xl font-semibold text-base-content">Marriage License</h3>
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Marriage license information as submitted</span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4">
                            {{-- Groom Section (Read-Only) --}}
                            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form-groom')
                            {{-- Bride Section (Read-Only) --}}
                            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form-bride')
                        </div>
                        {{-- Requirements Checklist (Read-Only) --}}
                        {{--
                        @ include('livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.mirrage-form-requirements',
                        ['documentRequest' => $documentRequest]) --}}
                    </div>

                @endif

                {{-- Special Permit Section (only if Special Permit) --}}
                @if ($this->service->slug === 'special-permit')
                    {{-- Special Permit Information Section --}}
                    <div class="space-y-6">
                        <div class="header mb-4">
                            <h3 class="text-xl font-semibold text-base-content">Special Permit Information</h3>
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Please provide the establishment details for the special permit</span>
                            </div>
                        </div>

                        {{-- Establishment/Office/Person Name --}}

                        <label for="establishment_name" class="label mt-2">
                            <span class="label-text font-medium">{{ label_with_bisaya('Name of Establishment/Office/Person', 'establishment_name') }} *</span>
                        </label>
                        <input type="text" id="establishment_name" wire:model="establishment_name" class="flux-form-control"
                            placeholder="Enter the name of establishment, office, or person" required
                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                        @error('establishment_name')
                            <div class="text-error text-sm mt-1">{{ $message }}</div>
                        @enderror


                        {{-- Establishment Address --}}

                        <label for="establishment_address" class="label">
                            <span class="label-text font-medium">{{ label_with_bisaya('Address of the Establishment', 'establishment_address') }} *</span>
                        </label>
                        <textarea id="establishment_address" wire:model="establishment_address"
                            class="flux-form-control min-h-[100px]"
                            placeholder="Enter the complete address of the establishment" required
                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })"></textarea>
                        @error('establishment_address')
                            <div class="text-error text-sm mt-1">{{ $message }}</div>
                        @enderror


                        {{-- Purpose --}}

                        <label for="establishment_purpose" class="label">
                            <span class="label-text font-medium">{{ label_with_bisaya('Purpose', 'establishment_purpose') }} *</span>
                        </label>
                        <textarea id="establishment_purpose" wire:model="establishment_purpose"
                            class="flux-form-control min-h-[100px]" placeholder="Describe the purpose of the special permit"
                            required
                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })"></textarea>
                        @error('establishment_purpose')
                            <div class="text-error text-sm mt-1">{{ $message }}</div>
                        @enderror

                    </div>

                @endif


                <footer class="my-6 flex justify-end gap-2">
                    <button class="flux-btn btn-ghost" wire:click="previousStep">Previous</button>
                    <button class="flux-btn flux-btn-primary" x-data
                        x-on:click="$dispatch('open-modal-info-confirmation-modal')" title="Information Confirmation"
                        wire:loading.disabled>
                        Next
                    </button>
                </footer>
            </div>

            <x-modal id="info-confirmation-modal" title="Information Confirmation" size="max-w-2xl">
                <div class="modal-body">
                    <div class="text-sm space-y-4 p-2">
                        <h3>Have you double-checked all your information to ensure it's accurate?</h3>
                        <p>Please review all the details you have provided in the previous steps. If everything is correct,
                            click "Confirm" to proceed with your document request. If you need to make any changes, click
                            "Cancel" to return to the form.</p>
                        <p class="font-bold">Note: Once you confirm, you will not be able to edit your information.</p>
                    </div>
                </div>


                <x-slot name="footer">
                    <div class="flex gap-2">
                        <button type="button" class="flux-btn flux-btn-outline" x-data
                            x-on:click="$dispatch('close-modal-info-confirmation-modal')">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </button>
                        <button type="button" class="flux-btn flux-btn-success" wire:click="nextStep" x-data
                            x-on:click="$dispatch('close-modal-info-confirmation-modal')">
                            <span wire:loading.remove>
                                <i class="bi bi-check-circle me-1"></i>
                                Confirm
                            </span>
                            <span wire:loading>
                                <span class="loading loading-spinner me-1"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </x-slot>
            </x-modal>
        </div>

        <!-- @ include('livewire.documentrequest.components.document-request-steps.step3') -->
    @elseif($step == 6 && $this->service->slug !== 'special-permit')
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Requester's Details Information</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please confirm your contact information</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-full">
                        <p class="text-sm text-base-content/70 mb-2">Please review and confirm your contact details:
                        </p>

                        @if ($to_whom === 'someone_else')
                            <div class="alert alert-info flex items-center gap-2 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>This contact info is for reaching you about this request - it can be different from the
                                    document
                                    beneficiary's info.</span>
                            </div>
                        @endif

                        @if ($this->service->title !== 'Death Certificate')
                            <div class="bg-base-200 p-4 rounded-lg">
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="contact_last_name" class="block text-xs font-medium mb-1">Requester's Last
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="contact_last_name" placeholder="Requester's Last Name"
                                            name="contact_last_name" id="contact_last_name" required
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('contact_last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="contact_first_name" class="block text-xs font-medium mb-1">Requester's First
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="contact_first_name" placeholder="Requester's First Name"
                                            name="contact_first_name" id="contact_first_name" required
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('contact_first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="contact_middle_name" class="block text-xs font-medium mb-1">Requester's
                                            Middle
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="contact_middle_name" placeholder="Requester's Middle Name"
                                            name="contact_middle_name" id="contact_middle_name"
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('contact_middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class=" grid grid-cols-1 gap-2">
                                    <div>
                                        <label class="font-medium text-sm">{{ label_with_bisaya('Email Address', 'email_address') }}:</label>
                                        <input class="flux-form-control " wire:model="contact_email" placeholder="Email Address"
                                            name="contact_email" id="contact_email" required>
                                        @error('contact_email')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="font-medium text-sm">{{ label_with_bisaya('Phone Number', 'phone_number') }}:</label>
                                        <input class="flux-form-control" wire:model="contact_phone" type="tel"
                                            inputmode="numeric" pattern="[0-9]*"
                                            minlength="11" maxlength="11"
                                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                            placeholder="Phone Number" name="contact_phone" id="contact_phone" required>
                                        @error('contact_phone')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-base-200 p-4 rounded-lg">
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="contact_last_name" class="block text-xs font-medium mb-1">Requester's Last
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model.live="contact_last_name" placeholder="Requester's Last Name"
                                            name="contact_last_name" id="contact_last_name" required
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('contact_last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="contact_first_name" class="block text-xs font-medium mb-1">Requester's First
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model.live="contact_first_name" placeholder="Requester's First Name"
                                            name="contact_first_name" id="contact_first_name" required
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('contact_first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="contact_middle_name" class="block text-xs font-medium mb-1">Requester's
                                            Middle
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model.live="contact_middle_name" placeholder="Requester's Middle Name"
                                            name="contact_middle_name" id="contact_middle_name"
                                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                        @error('contact_middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class=" grid grid-cols-1 gap-2">
                                    <div>
                                        <label class="font-medium text-sm">{{ label_with_bisaya('Email Address', 'email_address') }}:</label>
                                        <input class="flux-form-control " wire:model.live="contact_email"
                                            placeholder="Email Address" name="contact_email" id="contact_email" required>
                                        @error('contact_email')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="font-medium text-sm">{{ label_with_bisaya('Phone Number', 'phone_number') }}:</label>
                                        <input class="flux-form-control" wire:model.live="contact_phone" type="tel"
                                            inputmode="numeric" pattern="[0-9]*"
                                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                            placeholder="Phone Number" name="contact_phone" id="contact_phone" required>
                                        @error('contact_phone')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                        <p class="text-sm text-base-content/70 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            We will use this information to contact you about your document request.
                        </p>
                    </div>

                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                        <button class="flux-btn btn-sm flux-btn-primary" type="button" wire:click="validateContactInfo"
                            x-on:click="$dispatch('open-modal-consent-confirmation')" title="Validate">
                            <i class="bi bi-check"></i>Next
                        </button>
                        {{-- <button class="btn btn-primary" wire:click="nextStep">Next</button> --}}
                    </footer>
                </div>
            </div>

            <x-modal id="consent-confirmation" title="Confirmation" size="max-w-2xl">
                <div class="modal-body">
                    <div class="text-sm space-y-4 max-h-[60vh] overflow-y-auto p-2">
                        <h3 class="font-bold text-lg mb-2">Consent and Affirmation</h3>

                        <div class="bg-base-200 p-3 rounded-lg">
                            <p>I affirm that I am the document owner or the duly authorized representative of the owner of
                                the
                                requested PSA certificate. I attest that all information provided above is true and accurate
                                to
                                the best of my knowledge.</p>
                            <p class="mt-2">I understand that as the authorized requester, I am required to personally
                                appear
                                at the designated office to claim the requested document. I will present valid
                                identification to
                                verify my identity during collection.</p>
                            <p class="mt-2">I acknowledge that my personal information will be processed in accordance with
                                the Data Privacy Act of 2012 (Republic Act No. 10173). I understand that the information
                                provided will be used solely for the purpose of processing my document request.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-base mt-4 mb-2">Disclaimer</h4>
                            <p>This online system serves only as a platform to facilitate document requests and appointment
                                scheduling. It does not provide courier, mailing, or delivery services of any kind.</p>
                            <p class="mt-2">All requested documents must be personally claimed by the requester or their
                                authorized representative at the designated Municipal Office. Failure to appear or present
                                valid
                                identification may result in delays or denial of release.</p>
                            <p class="mt-2">The processing, printing, and release of documents are conducted exclusively by
                                the authorized Municipal Offices. Our system has no involvement in or responsibility for the
                                physical handling, storage, or issuance of the requested documents.</p>
                            <p class="mt-2">For any concerns regarding the status or release of your document, please
                                contact
                                or visit your respective Municipal Office directly.</p>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-semibold text-base mb-2">Acceptance</h4>
                            <p>As a Requisition and Appointment System customer, I fully understand and accept the terms
                                stated
                                above, including all requirements, processes, and applicable laws governing this
                                application.
                            </p>
                        </div>

                    </div>
                </div>


                <x-slot name="footer">
                    <div class="gap-2">
                        <button type="button" class="flux-btn flux-btn-outline" x-data
                            x-on:click="$dispatch('close-modal-consent-confirmation')">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </button>
                        <button type="button" class="flux-btn btn-sm flux-btn-success" x-data="{}" x-on:click="
            $dispatch('close-modal-consent-confirmation');
            $wire.nextStep();
        ">
                            <span wire:loading.remove>
                                <i class="bi bi-check-circle me-1"></i>
                                Confirm
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Loading...
                            </span>
                        </button>
                    </div>
                </x-slot>
            </x-modal>
        </div>

    @elseif($step == 7)
        <div class="px-5 py-2 mt-5">
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
                <div class="alert alert-info flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please review your document request details before submitting.</span>
                </div>
            </div>

            <div class="alert alert-info flex items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Present your actual valid government-issued ID. PDF copies are accepted for online submission.</span>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
                class="hidden justify-center items-center w-full h-64">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
            </div>

            <div class="space-y-6" wire:loading.remove>
                <!-- Request Summary -->
                <div class="flux-card p-6">
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Request Summary</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Office</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $office->name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Service</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $service->title }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $purpose === 'others' ? $purpose_others : $purpose }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Price</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                ₱{{ number_format($service->price, 2) }}</p>
                        </div>
                    </div>
                </div>

                @if ($service->slug === 'marriage-certificate')
                    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form')
                @elseif ($service->slug === 'death-certificate')
                    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.death-information')
                @elseif ($service->slug === 'special-permit')
                    <div class="flux-card p-6">
                        <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Special Permit Details
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Establishment Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $establishment_name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Establishment Address</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $establishment_address }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $establishment_purpose }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.personal-information')
                    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.address-information')
                    @if (($to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $service->slug !== 'death-certificate')
                        @include('livewire.documentrequest.components.document-request-steps.document-request-forms.family-information')
                    @endif
                @endif

                @if($service->slug !== 'special-permit')
                    <!-- Contact Person Information -->
                    <div class="flux-card p-6">
                        <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Contact Person
                            Information
                        </h3>
                        <div class="space-y-4">
                            <div class="grid md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $contact_last_name }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $contact_first_name }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $contact_middle_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $contact_email }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $contact_phone ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <footer class="my-6 flex justify-end gap-2">
                <button class="flux-btn btn-ghost" wire:click="previousStep" wire:loading.attr="disabled">Previous</button>
                <button class="flux-btn flux-btn-primary" x-data x-on:click="$dispatch('open-modal-confirm-modal')"
                    wire:loading.attr="disabled" title="Confirm Submission">
                    <span wire:loading class="loading loading-spinner"></span>
                    Submit
                </button>
            </footer>

            <x-modal id="confirm-modal" title="Confirm Submission" size="max-w-2xl">
                <div class="modal-body">
                    <div class="text-sm space-y-4 p-2">

                        <div class="bg-base-200 p-3 rounded-lg">
                            <p>Are you sure you want to submit this document request?</p>
                            <p class="mt-2">Please double-check all your information to ensure it's accurate and complete.
                            </p>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>You must complete payment before visiting the office to collect your document with valid
                                ID.</span>
                        </div>

                    </div>
                </div>


                <x-slot name="footer">
                    <div class="flex gap-2">
                        <button type="button" class="flux-btn flux-btn-outline" x-data
                            x-on:click="$dispatch('close-modal-confirm-modal')">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </button>
                        <button type="button" class="flux-btn flux-btn-success" wire:click="submitDocumentRequest"
                            wire:loading.attr="disabled" x-data x-on:click="$dispatch('close-modal-confirm-modal')">
                            <span wire:loading.remove>
                                <i class="bi bi-check-circle me-1"></i>
                                Confirm
                            </span>
                            <span wire:loading>
                                <span class="loading loading-spinner me-1"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </x-slot>
            </x-modal>

        </div>
    @elseif($step == 8)
        <div>
            <div class="px-5 py-2 mt-5">
                <div class="header mb-4">
                    <h3 class="text-xl font-semibold text-base-content">Payment</h3>
                    <div class="alert alert-info flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Please complete your payment to finalize your document request.</span>
                    </div>

                    @if (session()->has('error'))
                        <div class="alert alert-danger flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                </div>

                <!-- Loading State -->
                <div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
                    class="hidden justify-center items-center w-full h-64">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
                </div>

                <div class="space-y-6" wire:loading.remove>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Payment Information -->
                        <div class="flux-card p-6 h-fit">
                            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Payment
                                Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Service</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $this->service->title }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Amount</label>
                                    <p
                                        class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline font-semibold">
                                        ₱{{ number_format($this->service->price, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Reference Number</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $reference_number }}</p>
                                    <input type="hidden" wire:model="reference_number" value="{{ $reference_number }}">
                                </div>
                            </div>
                        </div>
                        <!-- Payment Method and Upload -->
                        <div class="flux-card p-6">
                            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Select Payment
                                Method</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 cursor-pointer transition-all {{ $selectedPaymentMethod === 'gcash' ? 'border-blue-500 bg-blue-50' : '' }}"
                                    wire:click="$set('selectedPaymentMethod', 'gcash')">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-medium">GCash</div>
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-bold">G</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600">Pay using GCash</p>
                                </div>
                                <div class="border rounded-lg p-4 cursor-pointer transition-all {{ $selectedPaymentMethod === 'bdo' ? 'border-blue-500 bg-blue-50' : '' }}"
                                    wire:click="$set('selectedPaymentMethod', 'bdo')">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-medium">BDO</div>
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-bold">B</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600">Pay using BDO QR</p>
                                </div>
                                <div class="border rounded-lg p-4 cursor-pointer transition-all {{ $selectedPaymentMethod === 'maya' ? 'border-blue-500 bg-blue-50' : '' }}"
                                    wire:click="$set('selectedPaymentMethod', 'maya')">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-medium">Maya</div>
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-bold">M</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600">Pay using Maya</p>
                                </div>
                            </div>
                            @error('selectedPaymentMethod')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                            <div class="mt-6" wire:key="payment-method-{{ $selectedPaymentMethod }}">
                                @if($selectedPaymentMethod)
                                    <h4 class="text-md font-medium mb-4">Scan QR Code to Pay</h4>
                                    <div class="flex flex-col gap-6">
                                        <div class="flex justify-center">
                                            <div class="bg-white p-4 rounded-lg shadow-sm border">
                                                @if($selectedPaymentMethod === 'gcash')
                                                    <div class="text-center">
                                                        <div class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center">
                                                            <span class="text-gray-500">GCash QR Code</span>
                                                        </div>
                                                        <p class="mt-2 text-sm text-gray-600">GCash Account: 09123456789</p>
                                                    </div>
                                                @elseif($selectedPaymentMethod === 'bdo')
                                                    <div class="text-center">
                                                        <div class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center">
                                                            <span class="text-gray-500">BDO QR Code</span>
                                                        </div>
                                                        <p class="mt-2 text-sm text-gray-600">Account Name: Municipality Office</p>
                                                        <p class="text-sm text-gray-600">Account Number: 1234567890</p>
                                                    </div>
                                                @elseif($selectedPaymentMethod === 'maya')
                                                    <div class="text-center">
                                                        <div class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center">
                                                            <span class="text-gray-500">Maya QR Code</span>
                                                        </div>
                                                        <p class="mt-2 text-sm text-gray-600">Maya ID: @municipaloffice</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Payment Proof -->
                                        @if($selectedPaymentMethod)

                                            <div>
                                                <h4 class="text-sm font-medium mb-2">Upload Payment Proof</h4>
                                                <p class="text-sm text-gray-600 mb-4">Please upload a screenshot of your payment
                                                    confirmation</p>
                                                <div class="form-control w-full">
                                                    <label class="block text-xs font-medium mb-1">Payment Screenshot</label>
                                                    <input type="file" class="flux-form-control" wire:model="paymentProofImage"
                                                        accept="image/*">
                                                    @error('paymentProofImage')
                                                        <div class="text-red-500 text-sm mt-1">Please upload a payment proof image</div>
                                                    @enderror
                                                    @if($paymentProofImage)
                                                        <div class="mt-2">
                                                            <p class="text-xs text-gray-600 mb-1">Preview:</p>
                                                            <img src="{{ $paymentProofImage->temporaryUrl() }}" alt="Payment Proof"
                                                                class="w-full max-h-48 object-contain border rounded">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($selectedPaymentMethod)
                                        <div class="form-control w-full">
                                            <label class="block text-xs font-medium mb-1">Payment Screenshot</label>
                                            <input type="file" class="flux-form-control" wire:model="paymentProofImage"
                                                accept="image/*">
                                            @error('paymentProofImage')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                            @if($paymentProofImage)
                                                <div class="mt-2">
                                                    <p class="text-xs text-gray-600 mb-1">Preview:</p>
                                                    <img src="{{ $paymentProofImage->temporaryUrl() }}" alt="Payment Proof"
                                                        class="w-full max-h-48 object-contain border rounded">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Disclaimer -->
                <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                    <p class="text-sm text-gray-700">
                        <strong>Disclaimer:</strong> Please ensure that all payments are made accurately to the official
                        account details provided by our system. Clients must upload a clear and valid screenshot of their
                        GCash payment receipt as proof of transaction. Refunds will not be issued for payments that are
                        incorrect, incomplete, or sent to the wrong account. The client assumes full responsibility for any
                        errors in the payment amount or account details used during the transaction.
                    </p>
                </div>


                <footer class="my-6 flex justify-end gap-2">
                    <button class="btn btn-ghost" wire:click="previousStep" wire:loading.attr="disabled">Previous</button>
                    {{-- <button class="btn btn-primary" wire:click="completePayment" wire:loading.attr="disabled"
                        wire:loading.class="opacity-70">
                        <span wire:loading wire:target="completePayment"
                            class="loading loading-spinner loading-xs mr-2"></span>
                        Complete Payment
                    </button> --}}
                    <button class="flux-btn btn-sm flux-btn-primary" type="button" x-data="{}"
                        x-on:click="$dispatch('open-modal-complete-payment-confirmation')" title="Payment Confirmation">
                        <i class="bi bi-check"></i>Complete Payment
                    </button>
                </footer>
            </div>

            <x-modal id="complete-payment-confirmation" title="Payment Confirmation" size="max-w-2xl">
                <div class="modal-body">
                    <div class="text-sm space-y-4 max-h-[60vh] overflow-y-auto p-2">

                        <p class="text-lg font-semibold">Are you sure you want to complete this payment?</p>


                        <div class="flex flex-col items-center alert alert-warning mt-4">
                            <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span>Once confirmed, your payment information cannot be changed.</span>
                            </div>
                            <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                <p class="text-sm text-gray-700">
                                    <strong>Disclaimer:</strong> Please ensure that all payments are made accurately to the official
                                    account details provided by our system. Clients must upload a clear and valid screenshot of their
                                    GCash payment receipt as proof of transaction. Refunds will not be issued for payments that are
                                    incorrect, incomplete, or sent to the wrong account. The client assumes full responsibility for any
                                    errors in the payment amount or account details used during the transaction.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="gap-2">
                        <button type="button" class="flux-btn flux-btn-outline" x-data
                            x-on:click="$dispatch('close-modal-complete-payment-confirmation')">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </button>
                        <button type="button" class="flux-btn btn-sm flux-btn-success" x-data="{}" x-on:click="
            $dispatch('close-modal-complete-payment-confirmation');
            $wire.completePayment();
        ">
                            <span wire:loading.remove>
                                <i class="bi bi-check-circle me-1"></i>
                                I Understand
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Loading...
                            </span>
                        </button>
                    </div>
                </x-slot>
            </x-modal>

        </div>
    @elseif($step == 9)
    <div>
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col items-center justify-center py-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h2>
                <p class="text-gray-600 text-center max-w-md mb-6">
                    Your document request has been submitted and payment is being verified. The admin will process your
                    request
                    shortly.
                </p>

                <div class="alert alert-info flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Processing can take up to <span class="font-semibold">1-3 business days</span>.</span>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg w-full max-w-md mb-6">
                    <h3 class="font-medium text-gray-800 mb-2">Request Summary</h3>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="text-gray-600">Reference Number:</div>
                        <div class="font-medium">{{ $reference_number }}</div>
                        <div class="text-gray-600">Document Type:</div>
                        <div class="font-medium">{{ $this->service->title }}</div>
                        <div class="text-gray-600">Amount Paid:</div>
                        <div class="font-medium">₱{{ number_format($this->service->price, 2) }}</div>
                        <div class="text-gray-600">Payment Method:</div>
                        <div class="font-medium">{{ ucfirst($selectedPaymentMethod ?? 'Online') }}</div>
                        <div class="text-gray-600">Status:</div>
                        <div class="font-medium text-amber-600">Payment Verification in Progress</div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('client.documents') }}" class="flux-btn flux-btn-outline">
                        View My Requests
                    </a>
                    <a href="{{ route('client.dashboard') }}" class="flux-btn flux-btn-primary">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
