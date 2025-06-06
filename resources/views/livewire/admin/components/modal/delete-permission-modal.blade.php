 <x-modal id="delete-permission" title="Delete Permission" size="max-w-2xl">
     <form wire:submit.prevent="confirmDeletePermission">
         <div class="modal-body">
             <div class="alert alert-warning">
                 <i class="bi bi-exclamation-triangle me-2"></i>
                 <strong>Warning:</strong> This action cannot be undone.
             </div>

             <p class="mt-3">
                 Are you sure you want to delete the permission <strong>"{{ $permissionName }}"</strong>?
             </p>
             <div class="form-check mt-4">
                 <input class="form-check-input" type="checkbox" id="confirmDeletion" wire:model.live="confirmDeletion">
                 <label class="form-check-label" for="confirmDeletion">
                     I understand this action cannot be undone
                 </label>
             </div>

         </div>
     </form>

     <x-slot name="footer">
         <div class="gap-2 ">
             <button type="button" class="btn btn-outline-secondary" x-data
                 x-on:click="$dispatch('close-modal-delete-permission')">
                 <i class="bi bi-x-lg me-1"></i>Cancel
             </button>

             <button type="button" class="btn btn-danger" wire:click="confirmDeletePermission"
                 wire:loading.attr="disabled" @if (!$confirmDeletion) disabled @endif>
                 <span wire:loading.remove>
                     <i class="bi bi-trash me-1"></i>
                     Yes, Delete
                 </span>
                 <span wire:loading>
                     <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                     Deleting...
                 </span>
             </button>
         </div>
     </x-slot>
 </x-modal>
