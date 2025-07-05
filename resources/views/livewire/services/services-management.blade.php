<?php

declare(strict_types=1);

use App\Models\Services;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Illuminate\Support\Str;
use App\Models\Offices;

new class extends Component {

    use WithPagination;

    public string $search = '';
    public int $serviceId;
    public int $office_id;
    public string $title = '';
    public string $slug = '';
    public string $description = '';
    public float $price = 0.00;
    public int $is_active = 1;
    public string $redirectTo = '';
    public bool $confirmDeletion = false;
    public Services $service;
    public Offices $office;

    public function mount(Services $service)
    {
        $this->service = $service;
        $this->title = $service->title ?? '';
        $this->slug = $service->slug ?? '';
        $this->description = $service->description ?? '';
        $this->price = $service->price ?? 0.00;
        $this->is_active = $service->is_active ?? 1;
        $this->redirectTo = route('admin.services');
        $this->resetPage();
    }  

    public function with()
    {
        return [
            'services' => Services::with('office')->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('slug', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate(5),
            'offices' => Offices::all(),
        ];
    }

    public function searchs()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
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
                  ->when(isset($this->service), function ($query) {
                      return $query->where('id', '!=', $this->service->id);
                  })
                  ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    public function saveService()
    {
        $validated = $this->validate([
            'office_id' => 'required|exists:offices,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required',
        ]);
        
        if($validated){
            $services = Services::create(
                [
                    'office_id' => $validated['office_id'],
                    'title' => $validated['title'],
                    'slug' => $validated['slug'],
                    'description' => $validated['description'],
                    'price' => $validated['price'],
                    'is_active' => $validated['is_active'],
                ]
            );
            if($services){
                $this->dispatch('close-modal-add-service'); 
                session()->flash('success', 'Service created successfully');
                $this->redirectTo = route('admin.services');
            }else{
                session()->flash('error', 'Failed to create service');
            }
        }else{
            session()->flash('error', 'Failed to save service');
        }
        $this->reset(); 
    }

    public function openEditServiceModal($id){
        $service = Services::findOrFail($id);
        $this->office_id = $service->office_id;
        $this->serviceId = $service->id;
        $this->title = $service->title;
        $this->slug = $service->slug;
        $this->description = $service->description;
        $this->price = (float) $service->price;
        $this->is_active = $service->is_active;
        $this->dispatch('open-modal-edit-service');
    }

    public function updateService(){
        $validated = $this->validate([
            'office_id' => 'required|exists:offices,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required',
        ]);
        if($validated){
            $service = Services::findOrFail($this->serviceId);
            $service->update(
                [
                    'office_id' => $validated['office_id'],
                    'title' => $validated['title'],
                    'slug' => $validated['slug'],
                    'description' => $validated['description'],
                    'price' => $validated['price'],
                    'is_active' => $validated['is_active'],
                ]
            );
            if($service){
                $this->dispatch('close-modal-edit-service');
                session()->flash('success', 'Service updated successfully');
                $this->redirectTo = route('admin.services');
            }else{
                session()->flash('error', 'Failed to update service');
            }
        }else{
            session()->flash('error', 'Failed to update service');
        }
        $this->reset();
    }

    public function openDeleteServiceModal($id){
        $service = Services::findOrFail($id);
        $this->serviceId = $service->id;
        $this->title = $service->title; 
        $this->confirmDeletion = false;
        $this->dispatch('open-modal-delete-service');
    }

    public function deleteService(){
        $service = Services::findOrFail($this->serviceId);
        if($service){
            if($this->confirmDeletion){
                $service->delete();
                session()->flash('success', 'Service deleted successfully');
                $this->redirectTo = route('admin.services');
            }else{
                session()->flash('error', 'Please confirm the deletion');
            }
        }else{
            session()->flash('error', 'Failed to delete service');
        }
        $this->resetPage();
    }
    
}; ?>

<div>
    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">
    <!-- Flash Messages -->
    @include('components.alert')
    <!-- Header -->
    @include('livewire.services.components.header')
    <!-- Table -->
    @include('livewire.services.components.table')
    <!-- Modals -->
    @include('livewire.services.components.modal.add-service-modal')
    @include('livewire.services.components.modal.edit-service-modal')
    @include('livewire.services.components.modal.delete-service-modal')
</div>
