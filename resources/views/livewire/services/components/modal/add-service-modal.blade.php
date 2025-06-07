<x-modal id="add-service" title="Add New Service" size="max-w-2xl">
    <form wire:submit.prevent="saveService" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="mb-3">
                <label for="office_id" class="form-label fw-semibold">Office</label>
                <select class="form-select @error('office_id') is-invalid @enderror" id="office_id"
                    wire:model.defer="office_id">
                    <option value="">Select Office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
                @error('office_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                    wire:model.defer="title" placeholder="Enter title" required>
                @error('title')
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
                <label for="price" class="form-label fw-semibold">Price</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                    wire:model.defer="price" placeholder="Enter price">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="is_active" class="form-label fw-semibold">Status</label>
                <select class="form-select @error('is_active') is-invalid @enderror" id="is_active"
                    wire:model.defer="is_active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </form>

    <x-slot name="footer">
        <div class="gap-2 ">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$dispatch('close-modal-add-service')">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>

            <button type="submit" class="btn btn-primary ml-2" wire:click="saveService" wire:loading.attr="disabled">
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
