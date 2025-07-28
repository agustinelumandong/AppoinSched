@php
    $certificateTypes = [
        'birth_certificate' => 'Birth Certificate',
        'death_certificate' => 'Death Certificate',
        'marriage_certificate' => 'Marriage Certificate',
        'cenomar' => 'CENOMAR (Certificate of No Marriage)',
    ];

    $relationshipTypes = [
        'myself' => 'Myself',
        'spouse' => 'Spouse',
        'child' => 'Child',
        'parent' => 'Parent',
        'sibling' => 'Sibling',
        'other' => 'Other',
    ];
@endphp

<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">Certificate Requests</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Select the certificates you need for this appointment</span>
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading...</p>
            </div>

            <div wire:loading.remove>
                <div class="space-y-4">
                    @foreach ($certificates as $index => $certificate)
                        <div class="card bg-base-100 border border-gray-200">
                            <div class="card-body p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-semibold">Certificate #{{ $index + 1 }}</h3>
                                    @if (count($certificates) > 1)
                                        <button type="button" wire:click="removeCertificate({{ $index }})"
                                            class="btn btn-error btn-sm btn-outline">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Certificate Type <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <select wire:model="certificates.{{ $index }}.certificate_type"
                                            class="select select-bordered" required>
                                            <option value="">-- Select Certificate Type --</option>
                                            @foreach ($certificateTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error("certificates.{$index}.certificate_type")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Relationship <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <select wire:model="certificates.{{ $index }}.relationship"
                                            class="select select-bordered" required>
                                            <option value="">-- Select Relationship --</option>
                                            @foreach ($relationshipTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error("certificates.{$index}.relationship")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">First Name <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text" wire:model="certificates.{{ $index }}.first_name"
                                            class="input input-bordered" required>
                                        @error("certificates.{$index}.first_name")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Last Name <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text" wire:model="certificates.{{ $index }}.last_name"
                                            class="input input-bordered" required>
                                        @error("certificates.{$index}.last_name")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text">Middle Name</span>
                                        </label>
                                        <input type="text" wire:model="certificates.{{ $index }}.middle_name"
                                            class="input input-bordered">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <button type="button" wire:click="addCertificate" class="btn btn-outline btn-primary">
                        Add Another Certificate
                    </button>
                </div>

                <footer class="my-6 flex justify-between">
                    <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                    <button class="btn btn-primary" wire:click="nextStep">Next</button>
                </footer>
            </div>
        </div>
    </div>
</div>
