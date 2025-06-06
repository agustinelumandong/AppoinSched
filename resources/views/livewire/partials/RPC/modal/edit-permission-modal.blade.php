 <x-modal id="edit-role" title="Edit Role" size="max-w-2xl">
     <form wire:submit.prevent="saveEditRole">
         <div class="modal-body">
             <div class="mb-3">
                 <label for="roleName" class="form-label fw-semibold">Role Name</label>
                 <input type="text" class="form-control @error('roleName') is-invalid @enderror" id="roleName"
                     wire:model.defer="roleName" placeholder="Enter role name (e.g., Admin, Manager, User)" required>
                 @error('roleName')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>

             @if ($allpermissions->count() > 0)
                 <div class="mb-3">
                     <label class="form-label fw-semibold">Assign Permissions</label>
                     <div class="border rounded p-3 bg-light">
                         <div class="row">
                             @foreach ($allpermissions as $permission)
                                 <div class="col-md-6 mb-2">
                                     <div class="form-check">
                                         <input class="form-check-input" style="cursor:pointer;" type="checkbox"
                                             id="perm_{{ $permission->id }}" wire:model.defer="selectedPermissions"
                                             value="{{ $permission->id }}">
                                         <label class="form-check-label" style="cursor:pointer;"
                                             for="perm_{{ $permission->id }}">
                                             <span class="fw-medium">{{ $permission->name }}</span>
                                         </label>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                     </div>
                 </div>
             @endif
         </div>
     </form>

     <x-slot name="footer">
         <div class="gap-2 ">
             <button type="button" class="btn btn-outline-secondary" x-data
                 x-on:click="$dispatch('close-modal-edit-role')">
                 <i class="bi bi-x-lg me-1"></i>Cancel
             </button>

             <button type="submit" class="btn btn-primary ml-2" wire:click="saveEditRole" wire:loading.attr="disabled">
                 <span wire:loading.remove>
                     <i class="bi bi-trash me-1"></i>
                     Submit
                 </span>
                 <span wire:loading>
                     <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                     Loading...
                 </span>
             </button>
         </div>
     </x-slot>
 </x-modal>
