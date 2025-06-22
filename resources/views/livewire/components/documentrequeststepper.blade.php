<?php

declare(strict_types=1);

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Models\Offices;
use App\Models\Services;
use App\Models\Staff;
use Livewire\Attributes\Title;

new #[Title('Document Request')]
    class extends Component {
    public int $step = 1;
    public string $to_whom = 'myself';
    public string $purpose = 'document request';
    public string $first_name = '';
    public string $last_name = '';
    public string $middle_name = '';
    public string $email = '';
    public Carbon $requested_date;
    public string $purpose_others = '';


    public ?int $id = null;
    public bool $isLoading = false;

    public Offices $office;
    public Services $service;

    public function mount(Offices $office, Services $service): void
    {
        $this->office = $office;
        $this->service = $service;
    }

    public function nextStep()
    {
        switch ($this->step) {
            // To Whom
            case 1:
                $this->isLoading = true;
                $this->validate([
                    'to_whom' => 'required',
                ]);
                break;
            // Purpose
            case 2:
                $this->isLoading = true;
                $this->validate([
                    'purpose' => 'required',
                ]);
                break;
            // Recipient /Personal Information
            case 3:
                $this->isLoading = true;
                $this->validate([
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'middle_name' => 'nullable',
                    'email' => 'required|email',
                ]);
                break;
            // Contact Info
            case 4:
                $this->isLoading = true;
                break;
            case 5:
                $this->isLoading = true;
                //  just confirmation step
                break;
        }
        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        } else {
            $this->step = 1;
            // session()->flash('error', 'Cannot go back further');
        }
        $this->isLoading = true;
    }

    public function submitDocumentRequest()
    {
        try {
            $user_id = auth()->user()->id;
            // Validate the data
            $validated = $this->validate([
                'to_whom' => 'required|string',
                'purpose' => 'required|string',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
            ]);
            $request_date = Carbon::now()->format('Y-m-d');
            Log::info('Request date: ' . $request_date);

            Log::info('Validation passed, attempting to create document request', $validated);

            // Create the document request
            $documentRequest = DocumentRequest::create([
                'user_id' => $user_id,
                'office_id' => $this->office->id,
                'service_id' => $this->service->id,
                'staff_id' => $this->id,
                'requested_date' => $request_date,
                'status' => 'pending',
                'to_whom' => $this->to_whom,
                'purpose' => $this->purpose,
            ]);


            if ($documentRequest) {
                Log::info('Document request created successfully', ['document_request_id' => $documentRequest->id]);
                session()->flash('success', 'Document request created successfully! We will contact you soon to confirm.');

                // Reset the form
                $this->reset(['step', 'to_whom', 'purpose', 'first_name', 'last_name', 'middle_name', 'email', 'requested_date']);
                $this->step = 1;

                $this->isLoading = true;
            } else {

                Log::error('Document request creation failed - no document request object returned');
                session()->flash('error', 'Failed to create document request. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Exception during document request creation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'user_id' => $user_id,
                    'office_id' => $this->office->id,
                    'service_id' => $this->service->id,
                    'staff_id' => $this->id,
                ]
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }




}; ?>

<div class="card shadow-xl border-none border-gray-200" style="border-radius: 1rem;">

    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success mx-5 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mx-5 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <h1 class="text-2xl font-semibold text-base-content mt-3 py-2 text-center">Request a Document</h1>

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
        <div class="px-5 py-2 mt-5">
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

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                        <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden />
                        <label for="myself"
                            class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'myself' ? 'flux-btn-active-primary' : '' }} p-2">Myself</label>
                        <input type="radio" id="someone_else" name="to_whom" value="someone_else" wire:model.live="to_whom"
                            hidden />
                        <label for="someone_else"
                            class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
                            Else</label>
                    </div>


                    <footer class="my-6 flex justify-end gap-2">
                        {{-- <button class="btn btn-ghost" wire:click="previousStep">Previous</button> --}}
                        <button class="btn btn-primary" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 2)
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

                    @if($purpose === 'others')
                        <div class="mt-4">
                            <input type="text" placeholder="Please specify purpose" class="flux-form-control"
                                wire:model="purpose_others" />
                            @error('purpose_others') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    @endif


                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                        <button class="btn btn-primary" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 3)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Your/Someone details</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please provide your/someone details</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2">
                        </div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>

                        <div class="flex flex-row gap-2 w-full">
                            <div class="w-full">
                                <input type="text" placeholder="Last Name" class="flux-form-control"
                                    wire:model="last_name" />
                                @error('last_name') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>


                            <div class="w-full">
                                <input type="text" placeholder="First Name" class="flux-form-control"
                                    wire:model="first_name" />
                                @error('first_name') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="w-full">
                                <input type="text" placeholder="Middle Name" class="flux-form-control"
                                    wire:model="middle_name" />
                                @error('middle_name') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex flex-row gap-2 w-full">
                            <div class=" w-full">
                                <input type="email" placeholder="Email" class="flux-form-control" wire:model="email" />
                                @error('email')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full">
                                <input type="tel" placeholder="Phone" class="flux-form-control" wire:model="phone" />
                                @error('phone')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <footer class="my-6 flex justify-end gap-2">
                            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                            <button class="btn btn-primary" wire:click="nextStep" wire:loading.disable>Next</button>

                        </footer>
                    </div>
                </div>
            </div>
        </div>

    @elseif($step == 4)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Contact Information</h3>
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

                    <div class="flex flex-col gap-2 w-1/2" wire:loading.remove>
                        <p class="text-sm text-base-content/70 mb-2">Please review and confirm your contact details:
                        </p>



                        <div class="bg-base-200 p-4 rounded-lg">
                            <div class=" grid grid-cols-1 gap-2">
                                <div>
                                    <label class="font-medium text-sm">Email:</label>
                                    <p class="text-base-content">{{ $email }}</p>
                                </div>
                            </div>
                        </div>

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
                        <button class="btn btn-primary" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 5)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please review your document request details</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-1/2 text-sm text-base-content/70" wire:loading.remove>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <p class="font-medium">Document Request Details</p>
                                <p>Office Id: {{ $office->id }}</p>
                                <p>Service Id: {{ $service->id }}</p>

                                <p>To Whom: <span class="text-base-content">{{ $to_whom }}</span></p>
                                <p>Purpose: <span class="text-base-content">{{ $purpose }}</span></p>
                                <p>Date: <span class=" text-base-content">{{ now()->format('Y-m-d') }}</span></p>
                                {{-- <p>Time: <span class="text-base-content">{{ $completed_date }}</span></p> --}}
                            </div>
                            <div class=" flex flex-col gap-2">
                                <p class="font-medium">Personal Information</p>
                                <p>Name: <span class="text-base-content">{{ $first_name }}
                                        {{ $last_name }}</span></p>
                                <p>Email: <span class=" text-base-content">{{ $email }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                        <button class="btn btn-primary" wire:click="submitDocumentRequest">Submit</button>

                    </footer>
                </div>
            </div>
        </div>
    @endif
</div>