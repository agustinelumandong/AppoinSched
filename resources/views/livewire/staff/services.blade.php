<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Services;
use App\Models\Offices;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new class extends Component {
  use WithPagination;

  public string $search = '';
  public int $serviceId = 0;
  public int $office_id = 0;
  public string $title = '';
  public string $slug = '';
  public string $description = '';
  public float $price = 0.0;
  public int $is_active = 1;
  public bool $confirmDeletion = false;

  public function mount(): void
  {
    if (!auth()->user()->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'MCR-admin', 'MTO-admin', 'BPLS-admin', 'admin', 'super-admin'])) {
      abort(403, 'Unauthorized access');
    }
    $this->office_id = $this->getOfficeIdForStaff();
  }

  public function getOfficeIdForStaff(): ?int
  {
    return auth()->user()->getOfficeIdForStaff();
  }

  public function with(): array
  {
    $officeId = $this->getOfficeIdForStaff();
    $user = auth()->user();

    // Super-admin sees all offices, others see only their assigned office
    $query = Services::query();

    if (!$user->hasRole('super-admin') && $officeId) {
      $query->where('office_id', $officeId);
    }

    $services = $query->when($this->search, function ($q) {
        $q->where('title', 'like', '%' . $this->search . '%')
          ->orWhere('description', 'like', '%' . $this->search . '%');
      })
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    return [
      'services' => $services,
      'office' => $officeId ? Offices::find($officeId) : null,
    ];
  }

  public function searchs(): void
  {
    $this->resetPage();
  }

  public function updatedTitle($value): void
  {
    $this->slug = $this->generateUniqueSlug($value);
  }

  protected function generateUniqueSlug($value): string
  {
    $slug = Str::slug($value);
    $original = $slug;
    $count = 1;
    while (
      Services::where('slug', $slug)
        ->when($this->serviceId, fn($q) => $q->where('id', '!=', $this->serviceId))
        ->exists()
    ) {
      $slug = $original . '-' . $count++;
    }
    return $slug;
  }

  public function openAddServiceModal(): void
  {
    $this->resetExcept(['search']);
    $this->office_id = $this->getOfficeIdForStaff();
    $this->is_active = 1;
    $this->dispatch('open-modal-add-service');
  }

  public function saveService(): void
  {
    $validated = $this->validate([
      'office_id' => 'required|exists:offices,id',
      'title' => 'required|string|max:255',
      'slug' => 'required|string|max:255',
      'description' => 'required|string|max:255',
      'price' => 'required|numeric|min:0',
      'is_active' => 'required',
    ]);
    $service = Services::create($validated);
    if ($service) {
      $this->dispatch('close-modal-add-service');
      session()->flash('success', 'Service created successfully');
    } else {
      session()->flash('error', 'Failed to create service');
    }
    $this->resetExcept(['search']);
  }

  public function openEditServiceModal($id): void
  {
    $service = Services::findOrFail($id);
    $this->serviceId = $service->id;
    $this->office_id = $service->office_id;
    $this->title = $service->title;
    $this->slug = $service->slug;
    $this->description = $service->description;
    $this->price = (float) $service->price;
    $this->is_active = $service->is_active;
    $this->dispatch('open-modal-edit-service');
  }

  public function updateService(): void
  {
    $validated = $this->validate([
      'office_id' => 'required|exists:offices,id',
      'title' => 'required|string|max:255',
      'slug' => 'required|string|max:255',
      'description' => 'required|string|max:255',
      'price' => 'required|numeric|min:0',
      'is_active' => 'required',
    ]);
    $service = Services::findOrFail($this->serviceId);
    $service->update($validated);
    if ($service) {
      $this->dispatch('close-modal-edit-service');
      session()->flash('success', 'Service updated successfully');
    } else {
      session()->flash('error', 'Failed to update service');
    }
    $this->resetExcept(['search']);
  }

  public function openDeleteServiceModal($id): void
  {
    $service = Services::findOrFail($id);
    $this->serviceId = $service->id;
    $this->title = $service->title;
    $this->confirmDeletion = false;
    $this->dispatch('open-modal-delete-service');
  }

  public function deleteService(): void
  {
    $service = Services::findOrFail($this->serviceId);
    if ($this->confirmDeletion) {
      $service->delete();
      $this->dispatch('close-modal-delete-service');
      session()->flash('success', 'Service deleted successfully');
    } else {
      session()->flash('error', 'Please confirm the deletion');
    }
    $this->resetExcept(['search']);
  }
}; ?>

<div>
  @include('components.alert')

  <div class="flux-card p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage Services</h1>
        <p class="text-gray-600 mt-1">View and manage services for your assigned office</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500">Assigned Office:</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {{ $office->name ?? 'No assigned office' }}
        </span>
      </div>
    </div>
  </div>

  <div class="flux-card p-6 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
        <input type="text" id="search" wire:model.live="search" class="form-control"
          placeholder="Search by service title or description...">
      </div>
      <div class="flex gap-2">
        <button wire:click="searchs" class="btn btn-primary">
          <i class="bi bi-search me-2"></i>Search
        </button>
        <button wire:click="openAddServiceModal" class="btn btn-success">
          <i class="bi bi-plus-circle me-2"></i>Add Service
        </button>
      </div>
    </div>
  </div>

  <div class="flux-card" style="padding: 12px;">
    <div class="overflow-x-auto">
      <table class="table flux-table w-full">
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($services as $service)
        <tr>
        <td>{{ $service->title }}</td>
        <td>{{ $service->description }}</td>
        <td>₱{{ number_format($service->price, 2) }}</td>
        <td>
          <span class="flux-badge flux-badge-{{ $service->is_active ? 'success' : 'danger' }}">
          {{ $service->is_active ? 'Active' : 'Inactive' }}
          </span>
        </td>
        <td>
          <button wire:click="openEditServiceModal({{ $service->id }})" class="flux-btn btn-sm flux-btn-primary">
          <i class="bi bi-pencil"></i>
          </button>
          <button wire:click="openDeleteServiceModal({{ $service->id }})" class="flux-btn btn-sm flux-btn-danger">
          <i class="bi bi-trash"></i>
          </button>
        </td>
        </tr>
      @empty
        <tr>
        <td colspan="5" class="text-center py-8">
          <div class="text-gray-500">
          <i class="bi bi-file-earmark-text text-4xl mb-2"></i>
          <p>No services found for your assigned office.</p>
          </div>
        </td>
        </tr>
      @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-6">
      {{ $services->links() }}
    </div>
  </div>

  @include('livewire.services.components.modal.add-service-modal', ['offices' => collect([$office])])
  @include('livewire.services.components.modal.edit-service-modal', ['offices' => collect([$office])])
  @include('livewire.services.components.modal.delete-service-modal')
</div>