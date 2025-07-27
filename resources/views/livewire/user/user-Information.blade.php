<!-- User Info -->
<div class="flux-card p-6">
    <h2 class="text-xl font-bold mb-4">Personal Information</h2>

    <div class="grid grid-cols-1 md:grid-cols-10 gap-4 mb-4">
        <div class="flex flex-col md:col-span-3">
            <label for="last_name" class="text-xs font-medium mb-1">Last Name</label>
            <input id="last_name" class="flux-form-control w-full" type="text" wire:model="last_name"
                placeholder="Last Name">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col md:col-span-3">
            <label for="first_name" class="text-xs font-medium mb-1">First Name</label>
            <input id="first_name" class="flux-form-control w-full" type="text" wire:model="first_name"
                placeholder="First Name">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col md:col-span-3">
            <label for="middle_name" class="text-xs font-medium mb-1">Middle Name</label>
            <input id="middle_name" class="flux-form-control w-full" type="text" wire:model="middle_name"
                placeholder="Middle Name">
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div class="flex flex-col md:col-span-1">
            <label for="suffix" class="text-xs font-medium mb-1">Suffix</label>
            <select id="suffix" class="flux-form-control w-full" wire:model="suffix">
                <option value="">Suffix</option>
                <option value="N/A">N/A</option>
                <option value="Jr.">Jr.</option>
                <option value="Sr.">Sr.</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
            </select>
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="flex flex-col">
            <label for="email" class="text-xs font-medium mb-1">Email</label>
            <input id="email" class="flux-form-control" type="email" wire:model="email" placeholder="Email">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col">
            <label for="contact_no" class="text-xs font-medium mb-1">Contact No</label>
            <input id="contact_no" class="flux-form-control" type="text" wire:model="contact_no"
                placeholder="Contact No">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col">
            <label for="sex_at_birth" class="text-xs font-medium mb-1">Sex at Birth</label>
            <select id="sex_at_birth" class="flux-form-control" wire:model="sex_at_birth">
                <option value="">Select Sex at Birth</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col">
            <label for="date_of_birth" class="text-xs font-medium mb-1">Date of Birth</label>
            <input id="date_of_birth" class="flux-form-control" type="date" wire:model="date_of_birth"
                placeholder="Date of Birth">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col">
            <label for="place_of_birth" class="text-xs font-medium mb-1">Place of Birth</label>
            <input id="place_of_birth" class="flux-form-control" type="text" wire:model="place_of_birth"
                placeholder="Place of Birth">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col">
            <label for="civil_status" class="text-xs font-medium mb-1">Civil Status</label>
            <select id="civil_status" class="flux-form-control" wire:model="civil_status">
                <option value="">Civil Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Divorced">Divorced</option>
                <option value="Separated">Separated</option>
            </select>
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex flex-col">
            <label for="religion" class="text-xs font-medium mb-1">Religion</label>
            <input id="religion" class="flux-form-control" type="text" wire:model="religion" placeholder="Religion">
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col">
            <label for="nationality" class="text-xs font-medium mb-1">Nationality</label>
            <input id="nationality" class="flux-form-control" type="text" wire:model="nationality"
                placeholder="Nationality">
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="flex flex-col">
            <label for="government_id_type" class="text-xs font-medium mb-1">Government ID Type</label>
            <select id="government_id_type" class="flux-form-control" wire:model="government_id_type">
                <option value="">Select Government ID Type</option>
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
            <span class="text-xs text-gray-500 mt-1">Select your primary government ID</span>
            @error('government_id_type')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>

        <div class="flex flex-col">
            <label for="government_id_image_path" class="text-xs font-medium mb-1">Government ID PDF</label>
            <input id="government_id_image_path" class="flux-form-control" type="file"
                wire:model="government_id_image_path" accept=".pdf" required>
            <span class="text-xs text-gray-500 mt-1">Please upload a PDF file showing both the front and back of your government-issued ID (maximum size: 5MB).</span>
            @error('government_id_image_path')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
            @if ($this->getGovernmentIdImageUrl())
                <div class="mt-2">
                    <p class="text-xs text-gray-600 mb-1">Current ID PDF:</p>
                    <a href="{{ $this->getGovernmentIdImageUrl() }}" target="_blank"
                        class="text-blue-600 hover:text-blue-800 text-xs">
                        View PDF Document
                    </a>
                </div>
            @endif
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
    </div>
</div>