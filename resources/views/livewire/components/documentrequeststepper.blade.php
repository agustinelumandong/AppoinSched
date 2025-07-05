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

new #[Title('Document Request')] class extends Component {
    public int $step = 1;
    public string $to_whom = 'myself';
    public string $purpose = '';

    // User Information
    public string $first_name = '';
    public string $last_name = '';
    public string $middle_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $zip = '';
    public string $country = '';
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
    public string $spouse_first_name = '';
    public string $spouse_middle_name = '';
    public string $spouse_last_name = '';
    public string $spouse_suffix = '';
    public string $spouse_birthdate = '';
    public string $spouse_nationality = '';
    public string $spouse_religion = '';
    public string $spouse_contact_no = '';
    public string $sex_at_birth = '';
    public string $date_of_birth = '';
    public string $place_of_birth = '';
    public string $civil_status = 'Single';
    public string $religion = '';
    public string $nationality = 'Filipino';
    public string $address_type = 'Permanent';
    public string $address_line_1 = '';
    public string $address_line_2 = '';
    public string $region = '';
    public string $province = '';
    public string $barangay = '';
    public string $street = '';
    public string $zip_code = '';

    // Death Certificate specific fields
    public string $deceased_last_name = '';
    public string $deceased_first_name = '';
    public string $deceased_middle_name = '';
    public string $death_date = '';
    public string $death_time = '';
    public string $death_place = '';
    public string $relationship_to_deceased = '';

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

    public function mount(Offices $office, Services $service, PersonalInformation $personalInformation, UserFamily $userFamilies, UserAddresses $userAddresses): void
    {
        $this->office = $office;
        $this->service = $service;
        $this->personalInformation = $personalInformation;
        $this->userFamilies = $userFamilies;
        $this->userAddresses = $userAddresses;

        // Check if user has complete profile
        if (!auth()->user()->hasCompleteProfile()) {
            session()->flash('warning', 'Please complete your profile information before making a document request.');
            $this->redirect(route('userinfo'));
        }

        // Only populate user data if "myself" is selected
        if ($this->to_whom === 'myself') {
            $this->populateUserData();
        }
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
        $this->spouse_last_name = '';
        $this->spouse_first_name = '';
        $this->spouse_middle_name = '';
        $this->spouse_suffix = '';
        $this->spouse_birthdate = '';
        $this->spouse_nationality = '';
        $this->spouse_religion = '';
        $this->spouse_contact_no = '';
        // Clear death certificate fields
        $this->deceased_last_name = '';
        $this->deceased_first_name = '';
        $this->deceased_middle_name = '';
        $this->death_date = '';
        $this->death_time = '';
        $this->death_place = '';
        $this->relationship_to_deceased = '';
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

    public function nextStep()
    {
        switch ($this->step) {
            case 1:
                $this->isLoading = true;
                $this->validate([
                    'to_whom' => 'required',
                ]);
                break;
            case 2:
                $this->isLoading = true;
                $this->validate([
                    'purpose' => 'required',
                ]);
                break;
            case 3:
                $this->isLoading = true;
                if ($this->service->title === 'Death Certificate') {
                    $rules = [
                        'deceased_last_name' => 'required|string|max:255',
                        'deceased_first_name' => 'required|string|max:255',
                        'deceased_middle_name' => 'nullable|string|max:255',
                        'death_date' => 'required|date|before_or_equal:today',
                        'death_time' => 'nullable|date_format:H:i',
                        'death_place' => 'required|string|max:255',
                        'relationship_to_deceased' => 'required|in:Spouse,Child,Parent,Sibling,Grandchild,Grandparent,Other Relative,Legal Representative,Other',
                    ];
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
                        'address_type' => 'required|in:Permanent,Temporary',
                        'address_line_1' => 'required|string|max:255',
                        'region' => 'required|string|max:255',
                        'province' => 'required|string|max:255',
                        'city' => 'required|string|max:255',
                        'barangay' => 'required|string|max:255',
                        'zip_code' => 'required|string|max:10',
                    ];

                    if (($this->to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $this->service->title !== 'Death Certificate') {
                        $rules = array_merge($rules, [
                            'father_last_name' => 'required|string|max:255',
                            'father_first_name' => 'required|string|max:255',
                            'father_middle_name' => 'required|string|max:255',
                            'father_suffix' => 'nullable|string|max:10',
                            'father_birthdate' => 'nullable|date',
                            'father_nationality' => 'required|string|max:255',
                            'father_religion' => 'required|string|max:255',
                            'father_contact_no' => 'required|string|max:20',

                            'mother_last_name' => 'required|string|max:255',
                            'mother_first_name' => 'required|string|max:255',
                            'mother_middle_name' => 'required|string|max:255',
                            'mother_suffix' => 'nullable|string|max:10',
                            'mother_birthdate' => 'nullable|date',
                            'mother_nationality' => 'required|string|max:255',
                            'mother_religion' => 'required|string|max:255',
                            'mother_contact_no' => 'required|string|max:20',
                        ]);

                        if ($this->service->title !== 'Certificate of No Marriage (CENOMAR)') {
                            $rules = array_merge($rules, [
                                'spouse_last_name' => 'required|string|max:255',
                                'spouse_first_name' => 'required|string|max:255',
                                'spouse_middle_name' => 'required|string|max:255',
                                'spouse_suffix' => 'nullable|string|max:10',
                                'spouse_birthdate' => 'nullable|date',
                                'spouse_nationality' => 'required|string|max:255',
                                'spouse_religion' => 'required|string|max:255',
                                'spouse_contact_no' => 'required|string|max:20',
                            ]);
                        }
                    }
                }
                $this->validate($rules);
                $this->initializeContactInfo();
                break;
            case 4:
                $this->isLoading = true;
                $this->validate([
                    'contact_first_name' => 'required|string|max:255',
                    'contact_last_name' => 'required|string|max:255',
                    'contact_email' => 'required|email|max:255',
                    'contact_phone' => 'nullable|string|max:20',
                ]);
                break;
            case 5:
                $this->isLoading = true;
                if ($this->service->title === 'Death Certificate') {
                    $this->validate([
                        'last_name' => 'required|string|max:255',
                        'first_name' => 'required|string|max:255',
                        'middle_name' => 'nullable|string|max:255',
                        'suffix' => 'nullable|string|max:10',
                        'email' => 'required|email|max:255',
                        'phone' => 'required|string|max:20',
                    ]);
                }
                break;
        }
        $this->step++;
    }

    // Check if the service requires family information
    private function requiresFamilyInfo(): bool
    {
        $familyRequiredServices = ['Birth Certificate', 'Marriage Certificate', 'Certificate of No Marriage (CENOMAR)'];
        return in_array($this->service->title, $familyRequiredServices);
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
            ]);

            // Prepare data for DocumentRequestDetails
            $detailsData = [
                'document_request_id' => $documentRequest->id,
                'request_for' => $this->to_whom === 'myself' ? 'myself' : 'someone_else',
            ];

            // Personal Information
            if ($this->service->title !== 'Death Certificate') {
                $detailsData = array_merge($detailsData, [
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
                ]);

                // Address Information
                $detailsData = array_merge($detailsData, [
                    'address_type' => $this->address_type,
                    'address_line_1' => $this->address_line_1,
                    'address_line_2' => $this->address_line_2,
                    'region' => $this->region,
                    'province' => $this->province,
                    'city' => $this->city,
                    'barangay' => $this->barangay,
                    'street' => $this->street,
                    'zip_code' => $this->zip_code,
                ]);

                // Family Information (if required)
                if ($this->requiresFamilyInfo()) {
                    // Father's Information
                    $detailsData = array_merge($detailsData, [
                        'father_last_name' => $this->father_last_name,
                        'father_first_name' => $this->father_first_name,
                        'father_middle_name' => $this->father_middle_name,
                        'father_suffix' => $this->father_suffix,
                        'father_birthdate' => $this->father_birthdate ? Carbon::parse($this->father_birthdate) : null,
                        'father_nationality' => $this->father_nationality,
                        'father_religion' => $this->father_religion,
                        'father_contact_no' => $this->father_contact_no,
                    ]);

                    // Mother's Information
                    $detailsData = array_merge($detailsData, [
                        'mother_last_name' => $this->mother_last_name,
                        'mother_first_name' => $this->mother_first_name,
                        'mother_middle_name' => $this->mother_middle_name,
                        'mother_suffix' => $this->mother_suffix,
                        'mother_birthdate' => $this->mother_birthdate ? Carbon::parse($this->mother_birthdate) : null,
                        'mother_nationality' => $this->mother_nationality,
                        'mother_religion' => $this->mother_religion,
                        'mother_contact_no' => $this->mother_contact_no,
                    ]);

                    // Spouse Information 
                    if ($this->service->title !== 'Certificate of No Marriage (CENOMAR)') {
                        $detailsData = array_merge($detailsData, [
                            'spouse_last_name' => $this->spouse_last_name,
                            'spouse_first_name' => $this->spouse_first_name,
                            'spouse_middle_name' => $this->spouse_middle_name,
                            'spouse_suffix' => $this->spouse_suffix,
                            'spouse_birthdate' => $this->spouse_birthdate ? Carbon::parse($this->spouse_birthdate) : null,
                            'spouse_nationality' => $this->spouse_nationality,
                            'spouse_religion' => $this->spouse_religion,
                            'spouse_contact_no' => $this->spouse_contact_no,
                        ]);
                    }
                }
            } else {
                // Death Certificate specific fields
                $detailsData = array_merge($detailsData, [
                    'deceased_last_name' => $this->deceased_last_name,
                    'deceased_first_name' => $this->deceased_first_name,
                    'deceased_middle_name' => $this->deceased_middle_name,
                    'death_date' => $this->death_date ? Carbon::parse($this->death_date) : null,
                    'death_time' => $this->death_time,
                    'death_place' => $this->death_place,
                    'relationship_to_deceased' => $this->relationship_to_deceased,
                ]);
            }

            // Contact Information (for third-party requests)
            if ($this->to_whom !== 'myself') {
                $detailsData = array_merge($detailsData, [
                    'contact_first_name' => $this->contact_first_name,
                    'contact_last_name' => $this->contact_last_name,
                    'contact_middle_name' => $this->contact_middle_name,
                    'contact_email' => $this->contact_email,
                    'contact_phone' => $this->contact_phone,
                ]);
            }

            // Create the document request details
            DocumentRequestDetails::create($detailsData);

            DB::commit();

            session()->flash('success', 'Document request submitted successfully!');

            // Redirect to success page or document requests list
            return redirect(request()->header('Referer') ?? url()->current());

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Document Request Submission Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting your request. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }
}; ?>


<div class="card shadow-xl border-none border-gray-200" style="border-radius: 1rem;">

    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

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
                    <div class="step-title">Personal Information</div>
                    <div class="step-description text-sm text-gray-500">Your/Someone details</div>
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
    @endif
</div>