<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Offices;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
new class extends Component {
    use WithPagination, WithFileUploads;

    #[Url(history: true)]
    public $page = 1;
    public string $search = '';
    public mixed $logo = null;
    public string $logoUrl = '';
    public int $officeId;
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public bool $confirmDeletion = false;
    public Offices $office;

    public function mount(Offices $office)
    {
        $this->office = $office;
        $this->name = $office->name ?? '';
        $this->slug = $office->slug ?? '';
        $this->description = $office->description ?? '';
        $this->search = $office->search ?? '';
        $this->logo = $office->logo ?? '';
        // $this->logoUrl = $office->logo ?? asset('storage/offices/default.png');
        $this->redirectTo = route('admin.offices');
        $this->resetPage();
    }

    public function with()
    {
        return [
            'offices' => Offices::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('slug', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate(5),
        ];
    }

    public function searchs()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        $this->slug = $this->generateUniqueSlug($value);
    }

    protected function generateUniqueSlug($value): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (
            Offices::where('slug', $slug)
                ->when(isset($this->office), function ($query) {
                    return $query->where('id', '!=', $this->office->id);
                })
                ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    public function saveOffice()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:offices,name',
            'slug' => 'string|max:255',
            'description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024',
        ]);

        // Save the file first
        $logoName = $this->logo->hashName;
        $this->logo->storeAs('offices', $logoName, 'public');

        // Create office record
        $office = Offices::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo' => $logoName,
        ]);

        if ($office) {
            $this->resetPage();
            $this->reset(['name', 'slug', 'description', 'logo']);
            $this->dispatch('close-modal-add-office');
            session()->flash('message', 'Office created successfully');
        } else {
            session()->flash('error', 'Failed to create office');
        }
    }

    public function openEditOfficeModal($id)
    {
        $office = Offices::find($id);
        if ($office) {
            $this->officeId = $office->id;
            $this->name = $office->name;
            $this->slug = $office->slug;
            $this->description = $office->description;
            $this->dispatch('open-modal-edit-office');
        } else {
            session()->flash('error', 'Office not found');
        }
    }

    public function updateOffice()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255|unique:offices,name,' . $this->officeId,
            'slug' => 'string|max:255',
            'description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024',
        ]);
        if ($this->logo) {
            // Save the file first
            $logoName = $this->logo->hashName();
            $this->logo->storeAs('offices', $logoName, 'public');
            $validated['logo'] = $logoName;
        }
        if ($validated) {
            $office = Offices::find($this->officeId);
            $office->update($validated);
            $this->dispatch('close-modal-edit-office');
            session()->flash('message', 'Office updated successfully');
            $this->resetPage();
            $this->reset(['name', 'slug', 'description', 'logo']);
        } else {
            session()->flash('error', 'Office update failed');
        }
    }

    public function resetForm()
    {
        $this->reset();
    }

    public function openDeleteOfficeModal($id)
    {
        $this->officeId = $id;
        $this->confirmDeletion = false;
        $this->dispatch('open-modal-delete-office');
    }

    public function deleteOffice()
    {
        $office = Offices::find($this->officeId);
        if ($office) {
            if ($this->confirmDeletion) {
                $office->delete();
                $this->dispatch('close-modal-delete-office');
                session()->flash('message', 'Office deleted successfully');
                $this->resetPage();
            } else {
                session()->flash('error', 'Please confirm the deletion');
            }
        } else {
            session()->flash('error', 'Office not found');
        }
    }
}; ?>

<div>

    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

    <!-- Flash Messages -->
    @include('components.alert')

    <!-- Header -->
    @include('livewire.offices.components.header')

    <!-- Table -->
    @include('livewire.offices.components.table')

    <!-- Modals -->
    @include('livewire.offices.components.modal.add-office-modal')
    @include('livewire.offices.components.modal.edit-office-modal')
    @include('livewire.offices.components.modal.delete-office-modal')
</div>
