 <x-modal id="edit-permission" title="Edit Permission" size="max-w-2xl">
     <form wire:submit.prevent="saveEditPermission">
         <div class="modal-body">
             <div class="mb-3">
                 <label for="permissionName" class="form-label fw-semibold">Permission Name</label>
                 <input type="text" class="form-control @error('permissionName') is-invalid @enderror"
                     id="permissionName" wire:model.defer="permissionName"
                     placeholder="Enter role name (e.g., Admin, Manager, User)" required>
                 @error('permissionName')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>

         </div>
     </form>

     <x-slot name="footer">
         <div class="gap-2 ">
             <button type="button" class="btn btn-outline-secondary" x-data
                 x-on:click="$dispatch('close-modal-edit-permission')">
                 <i class="bi bi-x-lg me-1"></i>Cancel
             </button>

             <button type="submit" class="btn btn-primary ml-2" wire:click="saveEditPermission"
                 wire:loading.attr="disabled">
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
