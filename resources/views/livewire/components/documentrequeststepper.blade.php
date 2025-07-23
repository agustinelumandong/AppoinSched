<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestDetails;
use Carbon\Carbon;
use App\Models\Offices;
use App\Models\Services;
use App\Models\PersonalInformation;
use App\Models\UserFamily;
use App\Models\UserAddresses;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Notifications\RequestEventNotification;
use App\Enums\RequestNotificationEvent;
use App\Notifications\AdminEventNotification;
use App\Enums\AdminNotificationEvent;
use App\Models\User;
use App\Services\PhilippineLocationsService;

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

    // Death Certificate specific fields
    public string $deceased_last_name = '';
    public string $deceased_first_name = '';
    public string $deceased_middle_name = '';
    public string $death_date = '';
    public string $death_time = '';
    public string $death_place = '';
    public string $relationship_to_deceased = '';
    public mixed $deceased_sex = null;
    public string $deceased_religion = '';
    public int $deceased_age = 0;
    public string $deceased_place_of_birth = '';
    public string $deceased_date_of_birth = '';
    public string $deceased_civil_status = '';
    public string $deceased_residence = '';
    public string $deceased_occupation = '';
    public string $deceased_father_last_name = '';
    public string $deceased_father_first_name = '';
    public string $deceased_father_middle_name = '';
    public string $deceased_mother_last_name = '';
    public string $deceased_mother_first_name = '';
    public string $deceased_mother_middle_name = '';
    // Burial Details
    public string $burial_cemetery_name = '';
    public string $burial_cemetery_address = '';
    public string $informant_name = '';
    public string $informant_address = '';
    public string $informant_relationship = '';
    public string $informant_contact_no = '';


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
    public ?int $groom_age ;
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
    public ?int $bride_age ;
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
    // Consent Section
    public string $consent_person = '';
    public string $consent_relationship = '';
    public string $consent_citizenship = '';
    public string $consent_residence = '';

    public bool $editPersonDetails = false;

    // Address Information
    public string $address = '';
    public string $region = '';
    public string $province = '';
    public string $city = '';
    public string $barangay = '';
    public string $street = '';
    public string $zip_code = '';
    public string $country = '';

    public array $regions = [];
    public array $provinces = [];
    public array $cities = [];
    public array $barangays = [];

   

    public function mount(Offices $office, Services $service, PersonalInformation $personalInformation, UserFamily $userFamilies, UserAddresses $userAddresses, PhilippineLocationsService $locations, ?string $reference_number = null): void
    {
        $this->office = $office;
        $this->service = $service;
        $this->personalInformation = $personalInformation;
        $this->userFamilies = $userFamilies;
        $this->userAddresses = $userAddresses;
        $this->editPersonDetails = false;
// Pre-populate dependent dropdowns if values exist
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
                $this->reference_number = $existingRequest->reference_number;
                $this->to_whom = $existingRequest->to_whom;
                $this->purpose = $existingRequest->purpose;
                $this->payment_reference = $existingRequest->payment_reference ?? '';
                $this->status = $existingRequest->status ?? '';
                $this->payment_status = $existingRequest->payment_status ?? '';
                // Set step based on payment status
                if ($existingRequest->payment_status === 'unpaid') {
                    $this->step = 6;
                } elseif (in_array($existingRequest->payment_status, ['processing', 'paid', 'completed'])) {
                    $this->step = 7;
                } else {
                    $this->step = 1; // fallback
                }
                // Populate ALL fields from details if available
                if ($existingRequest->details) {
                    foreach ($existingRequest->details->toArray() as $field => $value) {
                        if (property_exists($this, $field)) {
                            $this->{$field} = $value;
                        }
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
        $this->death_date = '';
        $this->death_time = '';
        $this->death_place = '';
        $this->relationship_to_deceased = '';
        $this->deceased_sex = null;
        $this->deceased_religion = '';
        $this->deceased_age = 0;
        $this->deceased_place_of_birth = '';
        $this->deceased_date_of_birth = '';
        $this->deceased_civil_status = '';
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
            $this->contact_phone = $user->phone ?? '';
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
            $this->death_date = '0001-01-01';
            $this->death_time = '00:00';
            $this->death_place = 'N/A';
            $this->relationship_to_deceased = 'N/A';
        } else {
            $this->deceased_last_name = '';
            $this->deceased_first_name = '';
            $this->deceased_middle_name = '';
            $this->death_date = '';
            $this->death_time = '';
            $this->death_place = '';
            $this->relationship_to_deceased = '';
        }
    }

    public function nextStep()
    {
        switch ($this->step) {
            case 1:
                $this->isLoading = true;
                $this->validate([
                    'to_whom' => 'required',
                    'relationship' => $this->to_whom === 'someone_else'
                        ? 'required|in:my_father,my_mother,my_son,my_daughter,others'
                        : 'nullable|in:my_father,my_mother,my_son,my_daughter,others',
                ]);
                do {
                    $this->reference_number = 'DOC-' . strtoupper(Str::random(10));
                } while (DocumentRequest::where('reference_number', $this->reference_number)->exists());
                break;
            case 2:
                $this->isLoading = true;
                $this->validate([
                    'purpose' => 'required',
                ]);
                break;
            case 3:
                $this->isLoading = true;
                if ($this->service->slug === 'death-certificate') {
                    $rules = [
                        'deceased_last_name' => 'required|string|max:255',
                        'deceased_first_name' => 'required|string|max:255',
                        'deceased_middle_name' => 'nullable|string|max:255',
                        'deceased_sex' => 'required|in:Male,Female',
                        'deceased_religion' => 'required|string|max:100',
                        'deceased_age' => 'required|integer|min:0',
                        'deceased_place_of_birth' => 'required|string|max:150',
                        'deceased_date_of_birth' => 'required|date|before_or_equal:today',
                        'deceased_civil_status' => 'required|in:Single,Married,Widowed,Divorced,Separated',
                        'deceased_residence' => 'required|string|max:255',
                        'deceased_occupation' => 'required|string|max:100',
                        'death_date' => 'required|date|before_or_equal:today',
                        'death_time' => 'nullable|date_format:H:i',
                        'death_place' => 'required|string|max:150',
                        'relationship_to_deceased' => 'required|in:Spouse,Child,Parent,Sibling,Grandchild,Grandparent,Other Relative,Legal Representative,Other',
                        // Parental Information
                        'deceased_father_last_name' => 'required|string|max:100',
                        'deceased_father_first_name' => 'required|string|max:100',
                        'deceased_father_middle_name' => 'nullable|string|max:100',
                        'deceased_mother_last_name' => 'required|string|max:100',
                        'deceased_mother_first_name' => 'required|string|max:100',
                        'deceased_mother_middle_name' => 'nullable|string|max:100',
                        // Burial Details
                        'burial_cemetery_name' => 'required|string|max:150',
                        'burial_cemetery_address' => 'required|string|max:255',
                        // Informant's Declaration
                        'informant_name' => 'required|string|max:100',
                        'informant_address' => 'required|string|max:255',
                        'informant_relationship' => 'required|string|max:100',
                        'informant_contact_no' => 'required|string|max:50',
                    
                    ];
                } elseif ($this->service->slug === 'marriage-certificate') {
                    $rules = $this->marriageLicenseRules();
                } else {
                    $rules = [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email|max:255',
                        'sex_at_birth' => 'required|in:Male,Female',
                        'date_of_birth' => 'required|date|before:today',
                        'place_of_birth' => 'required|string|max:255',
                        'civil_status' => 'required|in:Single,Married,Widowed,Divorced,Separated',
                        'nationality' => 'required|string|max:255',
                        'government_id_type' => 'nullable|string|max:100',
                        'government_id_image_file' => 'nullable|image|max:2048', // 2MB max
                        'address_type' => 'required|in:Permanent,Temporary',
                        'address_line_1' => 'required|string|max:255',
                        'region' => 'required|string|max:255',
                        'province' => 'required|string|max:255',
                        'city' => 'required|string|max:255',
                        'barangay' => 'required|string|max:255',
                        'zip_code' => 'required|string|max:10',
                    ];

                    if (($this->to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $this->service->slug !== 'death-certificate') {
                        if (!$this->father_is_unknown && !$this->mother_is_unknown) {
                            $rules = array_merge($rules, [
                                'father_last_name' => 'required|string|max:255',
                                'father_first_name' => 'required|string|max:255',
                                'father_middle_name' => 'required|string|max:255',
                                'father_suffix' => 'nullable|string|max:10',
                                'father_birthdate' => 'nullable|date',
                                'father_nationality' => 'required|string|max:255',
                                'father_religion' => 'required|string|max:255',
                                'father_contact_no' => 'required|string|max:20',
                            ]);
                        

                            $rules = array_merge($rules, [
                                'mother_last_name' => 'required|string|max:255',
                                'mother_first_name' => 'required|string|max:255',
                                'mother_middle_name' => 'required|string|max:255',
                                'mother_suffix' => 'nullable|string|max:10',
                                'mother_birthdate' => 'nullable|date',
                                'mother_nationality' => 'required|string|max:255',
                                'mother_religion' => 'required|string|max:255',
                                'mother_contact_no' => 'required|string|max:20',
                            ]);
                        }
                    }
                }

                $this->validate($rules);
                $this->fillMarriageCertificateData();
                $this->fillFamilyDefaults();
                $this->initializeContactInfo();
                break;
            case 4:
                $this->isLoading = true;
                $this->validate([
                    'contact_first_name' => 'required|string|max:255',
                    'contact_last_name' => 'required|string|max:255',
                    'contact_email' => 'required|email|max:255',
                    'contact_phone' => 'required|string|max:20',
                ]);
                break;
            case 5:
                $this->isLoading = true;
                if ($this->service->slug === 'death-certificate') {
                    $this->validate([
                        'last_name' => 'required|string|max:255',
                        'first_name' => 'required|string|max:255',
                        'middle_name' => 'nullable|string|max:255',
                        'suffix' => 'nullable|string|max:10',
                        'email' => 'required|email|max:255',
                        'phone' => 'required|string|max:20',
                    ]);
                } else {
                    // For other document types, validate contact information
                    $this->validate([
                        'contact_first_name' => 'required|string|max:255',
                        'contact_last_name' => 'required|string|max:255',
                        'contact_email' => 'required|email|max:255',
                        'contact_phone' => 'required|string|max:20',
                    ]);
                }
                break;
        }
        $this->step++;
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
        if (empty($this->death_date)) {
            $this->death_date = '0001-01-01';
        }
        if (empty($this->death_time)) {
            $this->death_time = '00:00';
        }
        if (empty($this->death_place)) {
            $this->death_place = 'N/A';
        }
        if (empty($this->relationship_to_deceased)) {
            $this->relationship_to_deceased = 'N/A';
        }
        if (empty($this->deceased_sex)) {
            $this->deceased_sex = 'Male';
        }
        if (empty($this->deceased_religion)) {
            $this->deceased_religion = 'N/A';
        }
        if (empty($this->deceased_age)) {
            $this->deceased_age = 0;
        }
        if (empty($this->deceased_place_of_birth)) {
            $this->deceased_place_of_birth = 'N/A';
        }
        if (empty($this->deceased_date_of_birth)) {
            $this->deceased_date_of_birth = '0001-01-01';
        }
        if (empty($this->deceased_civil_status)) {
            $this->deceased_civil_status = 'Single';
        }
        if (empty($this->deceased_residence)) {
            $this->deceased_residence = 'N/A';
        }
        if (empty($this->deceased_occupation)) {
            $this->deceased_occupation = 'N/A';
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
        if (empty($this->burial_cemetery_name)) {
            $this->burial_cemetery_name = 'N/A';
        }
        if (empty($this->burial_cemetery_address)) {
            $this->burial_cemetery_address = 'N/A';
        }
        if (empty($this->informant_name)) {
            $this->informant_name = 'N/A';
        }
        if (empty($this->informant_address)) {
            $this->informant_address = 'N/A';
        }
        if (empty($this->informant_relationship)) {
            $this->informant_relationship = 'N/A';
        }
        if (empty($this->informant_contact_no)) {
            $this->informant_contact_no = 'N/A';
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

        // Consent Section (if applicable)
        $this->consent_person = $this->consent_person ?: 'N/A';
        $this->consent_relationship = $this->consent_relationship ?: 'N/A';
        $this->consent_citizenship = $this->consent_citizenship ?: 'N/A';
        $this->consent_residence = $this->consent_residence ?: 'N/A';
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
            $this->step--;
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

            // Handle file upload for government ID image
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
            
                $detailsData = array_merge($detailsData, [
                    // Groom
                    'groom_first_name' => $this->groom_first_name,
                    'groom_middle_name' => $this->groom_middle_name,
                    'groom_last_name' => $this->groom_last_name,
                    'groom_suffix' => $this->groom_suffix,
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
                    'groom_father_middle_name' => $this->groom_father_middle_name,
                    'groom_father_last_name' => $this->groom_father_last_name,
                    'groom_father_suffix' => $this->groom_father_suffix,
                    'groom_father_citizenship' => $this->groom_father_citizenship,
                    'groom_father_residence' => $this->groom_father_residence,
                    // Groom Mother
                    'groom_mother_first_name' => $this->groom_mother_first_name,
                    'groom_mother_middle_name' => $this->groom_mother_middle_name,
                    'groom_mother_last_name' => $this->groom_mother_last_name,
                    'groom_mother_suffix' => $this->groom_mother_suffix,
                    'groom_mother_citizenship' => $this->groom_mother_citizenship,
                    'groom_mother_residence' => $this->groom_mother_residence,
                    // Bride
                    'bride_first_name' => $this->bride_first_name,
                    'bride_middle_name' => $this->bride_middle_name,
                    'bride_last_name' => $this->bride_last_name,
                    'bride_suffix' => $this->bride_suffix,
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
                    'bride_father_middle_name' => $this->bride_father_middle_name,
                    'bride_father_last_name' => $this->bride_father_last_name,
                    'bride_father_suffix' => $this->bride_father_suffix,
                    'bride_father_citizenship' => $this->bride_father_citizenship,
                    'bride_father_residence' => $this->bride_father_residence,
                    // Bride Mother
                    'bride_mother_first_name' => $this->bride_mother_first_name,
                    'bride_mother_middle_name' => $this->bride_mother_middle_name,
                    'bride_mother_last_name' => $this->bride_mother_last_name,
                    'bride_mother_suffix' => $this->bride_mother_suffix,
                    'bride_mother_citizenship' => $this->bride_mother_citizenship,
                    'bride_mother_residence' => $this->bride_mother_residence,
                    // Consent Section
                    'consent_person' => $this->consent_person,
                    'consent_relationship' => $this->consent_relationship,
                    'consent_citizenship' => $this->consent_citizenship,
                    'consent_residence' => $this->consent_residence,
                ]);
           
                // Existing logic for other document types
                $detailsData = array_merge($detailsData, [
                    // Personal Information
                    'last_name' => $this->last_name,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'suffix' => $this->suffix,
                    'email' => $this->email,
                    'contact_no' => $this->phone,
                    'sex_at_birth' => $this->sex_at_birth,
                    'date_of_birth' => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
                    'place_of_birth' => $this->place_of_birth,
                    'civil_status' => $this->civil_status,
                    'religion' => $this->religion,
                    'nationality' => $this->nationality,
                    'government_id_type' => $this->government_id_type,
                    'government_id_image_path' => $governmentIdImagePath,
                    'address_type' => $this->address_type,
                    'address_line_1' => $this->address_line_1,
                    'address_line_2' => $this->address_line_2,
                    'region' => $this->region,
                    'province' => $this->province,
                    'city' => $this->city,
                    'barangay' => $this->barangay,
                    'street' => $this->street,
                    'zip_code' => $this->zip_code,

                    'father_last_name' => $this->father_last_name,
                    'father_first_name' => $this->father_first_name,
                    'father_middle_name' => $this->father_middle_name,
                    'father_suffix' => $this->father_suffix,
                    'father_birthdate' => $this->father_birthdate ? Carbon::parse($this->father_birthdate) : null,
                    'father_nationality' => $this->father_nationality,
                    'father_religion' => $this->father_religion,
                    'father_contact_no' => $this->father_contact_no,
                    'mother_last_name' => $this->mother_last_name,
                    'mother_first_name' => $this->mother_first_name,
                    'mother_middle_name' => $this->mother_middle_name,
                    'mother_suffix' => $this->mother_suffix,
                    'mother_birthdate' => $this->mother_birthdate ? Carbon::parse($this->mother_birthdate) : null,
                    'mother_nationality' => $this->mother_nationality,
                    'mother_religion' => $this->mother_religion,
                    'mother_contact_no' => $this->mother_contact_no,

                    // Death Certificate specific fields

                    'deceased_last_name' => $this->deceased_last_name,
                    'deceased_first_name' => $this->deceased_first_name,
                    'deceased_middle_name' => $this->deceased_middle_name,
                    'death_date' => $this->death_date ? Carbon::parse($this->death_date) : null,
                    'death_time' => $this->death_time,
                    'death_place' => $this->death_place,
                    'relationship_to_deceased' => $this->relationship_to_deceased,
                    'deceased_sex' => $this->deceased_sex,
                    'deceased_religion' => $this->deceased_religion,
                    'deceased_age' => $this->deceased_age,
                    'deceased_place_of_birth' => $this->deceased_place_of_birth,
                    'deceased_date_of_birth' => $this->deceased_date_of_birth,
                    'deceased_civil_status' => $this->deceased_civil_status,
                    'deceased_residence' => $this->deceased_residence,
                    'deceased_occupation' => $this->deceased_occupation,
                    'deceased_father_last_name' => $this->deceased_father_last_name,
                    'deceased_father_first_name' => $this->deceased_father_first_name,
                    'deceased_father_middle_name' => $this->deceased_father_middle_name,
                    'deceased_mother_last_name' => $this->deceased_mother_last_name,
                    'deceased_mother_first_name' => $this->deceased_mother_first_name,
                    'deceased_mother_middle_name' => $this->deceased_mother_middle_name,
                    'burial_cemetery_name' => $this->burial_cemetery_name,
                    'burial_cemetery_address' => $this->burial_cemetery_address,
                    'informant_name' => $this->informant_name,
                    'informant_address' => $this->informant_address,
                    'informant_relationship' => $this->informant_relationship,
                    'informant_contact_no' => $this->informant_contact_no,
                ]);
            

            // Contact Information (for third-party requests)

            $detailsData = array_merge($detailsData, [
                'contact_first_name' => $this->contact_first_name,
                'contact_last_name' => $this->contact_last_name,
                'contact_middle_name' => $this->contact_middle_name,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
            ]);
            // Create the document request details
            DocumentRequestDetails::create($detailsData);

            // Store the document ID for the payment step
            $this->id = $documentRequest->id;

            DB::commit();

            // Send notification to user
            auth()->user()->notify(new RequestEventNotification(
                RequestNotificationEvent::Submitted,
                [
                    'reference_no' => $this->reference_number,
                    'summary' => $this->service->title . ' for ' . $this->first_name . ' ' . $this->last_name
                ]
            ));

            // Send notification to staff
            $staffs = User::getStaffsByOfficeId($this->office->id);
            if ($staffs->count() > 0) {
                foreach ($staffs as $staff) {
                    $staff->notify(new AdminEventNotification(
                        AdminNotificationEvent::UserSubmittedDocumentRequest,
                        [
                            'reference_no' => $this->reference_number,
                            'summary' => $this->service->title . ' for ' . $this->first_name . ' ' . $this->last_name
                        ]
                    ));
                }
            }

            session()->flash('success', 'Document request submitted successfully!');

            // Move to payment step instead of redirecting
            $this->step = 6;
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
                    throw new \Exception("Document request not found. Please check your request and try again.");
                }

                // Update the ID for future use
                $this->id = $documentRequest->id;
            } else {
                throw new \Exception("Document request information is missing. Please try again.");
            }

            // Save payment proof
            $paymentProofPath = null;
            try {
                $paymentProofPath = $this->paymentProofImage->storeAs(
                    'payment-proofs',
                    'payment_' . $this->reference_number . '.' . $this->paymentProofImage->getClientOriginalExtension(),
                    'public'
                );
            } catch (\Exception $fileException) {
                Log::error('File upload error: ' . $fileException->getMessage());
                // Continue without file path if there's an error
                $paymentProofPath = 'payment-pending';
            }

            // Update payment status using the model
            $documentRequest->payment_status = 'processing';

            // Only set these fields if they exist in the database
            $documentRequest->payment_method = $this->selectedPaymentMethod;

            if ($paymentProofPath) {
                $documentRequest->payment_proof_path = $paymentProofPath;
            }

            $documentRequest->payment_date = now();
            $documentRequest->save();

            // Send notification to staff
            $staffs = User::getStaffsByOfficeId($this->office->id);
            if ($staffs->count() > 0) {
                foreach ($staffs as $staff) {
                    $staff->notify(new AdminEventNotification(
                        AdminNotificationEvent::UserPayedDocumentRequest,
                        [
                            'reference_no' => $this->reference_number,
                            'summary' => $this->service->title . ' for ' . $this->first_name . ' ' . $this->last_name,
                            'payment_method' => $this->selectedPaymentMethod
                        ]
                    ));
                }
            }

            // Move to success page
            $this->step = 7;
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
            // Consent Section (optional)
            'consent_person' => 'nullable|string|max:255',
            'consent_relationship' => 'nullable|string|max:255',
            'consent_citizenship' => 'nullable|string|max:255',
            'consent_residence' => 'nullable|string|max:255',
        ];
    }
}; ?>


<div class="card shadow-xl border-none border-gray-200" style="border-radius: 1rem;">



    <h1 class="text-2xl font-semibold text-base-content mt-3 py-2 text-center">Request a {{ $this->service->title }}
    </h1>

    {{-- Stepper Header --}}
    <div class="px-5 py-2 mt-5">
        <ul class="steps steps-horizontal w-full">
            <li class="step {{ $step >= 1 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">To Whom?</div>
                    <div class="step-description text-sm text-gray-500">For Yourself or Someone Else?</div>
                </div>
            </li>
            <li class="step {{ $step >= 2 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Purpose Request</div>
                    <div class="step-description text-sm text-gray-500">State your purpose</div>
                </div>
            </li>
            <li class="step {{ $step >= 3 ? 'step-info' : '' }}">
                <div class="step-content">
                    @php 
                                                                    if ($this->service->slug === 'marriage-certificate') {
                            $stepTitle = 'Marriage License';
                            $stepDescription = 'Marriage License Information';
                        } elseif ($this->service->slug === 'death-certificate') {
                            $stepTitle = 'Death Certificate';
                            $stepDescription = 'Death Certificate Information';
                        } else {
                            $stepTitle = 'Personal Information';
                            $stepDescription = 'Your/Someone details';
                        }
                    @endphp
                    <div class="step-title">{{ $stepTitle }}</div>
                    <div class="step-description text-sm text-gray-500">{{ $stepDescription }}</div>
                </div>
            </li>
            <li class="step {{ $step >= 4 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Contact Information</div>
                    <div class="step-description text-sm text-gray-500">How to reach you</div>
                </div>
            </li>
            <li class="step {{ $step >= 5 ? 'step-info' : '' }}">
                    <div class="step-content">
                    <div class="step-title">Confirmation</div>
                        <div class="step-description text-sm text-gray-500">Review & Submit</div>
                </div>
                </li>
            <li class="step {{ $step >= 6 ? 'step-info' : '' }}">
                    <div class="step-content">
                    <div class="step-title">Payment</div>
                    <div class="step-description text-sm text-gray-500">Complete your transaction</div>
                 </div>
            </li>
            <li class="step {{ $step >= 7 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Success</div>
                <div class="step-description text-sm text-gray-500">Done</div>
            </div>
        </li>
    </ul>
</div>
    {{-- Stepper Content --}}
   @if ($step == 1)
    @include('livewire.documentrequest.components.document-request-steps.step1')
@elseif($step == 2)
    @include('livewire.documentrequest.components.document-request-steps.step2')
@elseif($step == 3)
    @include('livewire.documentrequest.components.document-request-steps.step3')
@elseif($step == 4)
        @include('livewire.documentrequest.components.document-request-steps.step4')
    @elseif($step == 5)
        @include('livewire.documentrequest.components.document-request-steps.step5')
    @elseif($step == 6)
        @include('livewire.documentrequest.components.document-request-steps.step6')
    @elseif($step == 7)
        @include('livewire.documentrequest.components.document-request-steps.step7')
    @endif
</div>