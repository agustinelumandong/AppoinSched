<x-modal id="add-office" title="Add New Office" size="max-w-2xl">
    <form wire:submit.prevent="saveOffice" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    wire:model.defer="name" placeholder="Enter name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                    wire:model.defer="slug"  readonly disabled hidden>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                    wire:model.defer="description" placeholder="Enter description">
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label fw-semibold">Logo</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo"
                    wire:model="logo" accept="image/*">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Logo Preview</label>
                <div class="mt-2">
                    <div wire:loading wire:target="logo" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <img src="{{ $logo ? $logo->temporaryUrl() : '' }}" alt="Logo Preview" class="img-thumbnail" style="max-width: 100px; max-height: 100px; display: {{ $logo ? 'block' : 'none' }};">
                </div>
            </div>
        </div>
    </form>

    <x-slot name="footer">
        <div class="gap-2 ">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$dispatch('close-modal-add-office')">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>

            <button type="submit" class="btn btn-primary ml-2" wire:click="saveOffice" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="bi bi-save me-1"></i>
                    Save
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Loading...
                </span>
            </button>
        </div>
    </x-slot>
</x-modal>
