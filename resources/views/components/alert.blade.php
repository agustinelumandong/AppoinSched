@if (session()->has('success'))
    <div id="success-notification" 
         class="fixed top-4 right-4 z-50 max-w-md opacity-100 transition-all duration-300"
         x-init="setTimeout(() => closeNotification('success-notification'), 5000)"
         role="alert" 
         aria-live="polite">
        <div class="flux-card p-4 bg-green-50 border-green-200 shadow-lg rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="closeNotification('success-notification')"
                    class="text-green-600 hover:text-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded"
                    aria-label="Close success notification">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@elseif (session()->has('error'))
    <div id="error-notification" class="fixed top-4 right-4 z-50 max-w-md opacity-100 transition-all duration-300"
        x-init="setTimeout(() => closeNotification('error-notification'), 5000)">
        <div class="flux-card p-4 bg-red-50 border-red-200 shadow-lg rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="closeNotification('error-notification')"
                    class="text-red-600 hover:text-red-800 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif

<script>
    function closeNotification(id) {
        try {
            const notification = document.getElementById(id);
            if (!notification) {
                console.warn(`Notification with id "${id}" not found`);
                return;
            }
            
            // Prevent multiple rapid clicks
            if (notification.style.opacity === '0') {
                return;
            }
            
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                try {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                } catch (removeError) {
                    console.error('Error removing notification element:', removeError);
                }
            }, 300);
        } catch (error) {
            console.error('Error in closeNotification function:', error);
        }
    }
</script>

<style>
    #success-notification,
    #error-notification {
        transition: all 0.3s ease;
        transform: translateX(0);
    }
</style>

{{-- @foreach (['success' => 'green', 'error' => 'red'] as $type => $color)
        @if (session()->has($type))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="fixed top-4 right-4 z-50 max-w-md">
                <div class="flux-card p-4 bg-{{ $color }}-50 border-{{ $color }}-200 shadow-lg rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-{{ $color }}-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if ($type === 'success')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                @endif
                            </svg>
                            <span class="text-{{ $color }}-800 font-medium">{{ session($type) }}</span>
                        </div>
                        <button @click="show = false"
                            class="text-{{ $color }}-600 hover:text-{{ $color }}-800 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                         </svg>
                        </button>
                    </div>
              </div>
            </div>
       @endif
    @endforeach --}}
