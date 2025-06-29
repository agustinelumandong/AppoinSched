<div class="px-5 py-2 mt-5">
  <div class="flex flex-col gap-4">
    <div>
      <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">For Yourself or Someone Else?</h3>
        <div class="flex items-center gap-2 text-sm text-base-content/70">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Please select who this document request is for</span>
        </div>
      </div>

      <!-- Loading State -->
      <div wire:loading.delay class="text-center ">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
        <p class="text-gray-600">Loading...</p>
      </div>

      <div class="flex flex-col gap-2 w-full" wire:loading.remove>
        @if ($this->service->title === 'Death Certificate')
          <?php
        $to_whom = 'someone_else';
             ?>
          <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden disabled />
          <label for="myself" class="flux-input-primary flux-btn cursor-pointer opacity-50 cursor-not-allowed p-2">Myself
            (Not available for Death Certificate)</label>
          <input type="radio" id="someone_else" name="to_whom" value="someone_else" wire:model.live="to_whom" hidden />
          <label for="someone_else"
            class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
            Else</label>
    @else
      <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden />
      <label for="myself"
        class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'myself' ? 'flux-btn-active-primary' : '' }} p-2">Myself</label>
      <input type="radio" id="someone_else" name="to_whom" value="someone_else" wire:model.live="to_whom" hidden />
      <label for="someone_else"
        class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
        Else</label>
    @endif
      </div>


      <footer class="my-6 flex justify-end gap-2">
        {{-- <button class="btn btn-ghost" wire:click="previousStep">Previous</button> --}}
        <button class="btn btn-primary" wire:click="nextStep">Next</button>
      </footer>
    </div>
  </div>
</div>