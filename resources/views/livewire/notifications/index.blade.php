<?php
declare(strict_types=1);

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

new class extends Component {

    #[On('markAsRead')]
    public function markAsRead($data): void
    {
        $user = Auth::user();
        dd($data);
        $notificationId = $data['id'];
        if ($notificationId) {
            $notification = $user->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
            }
        }
    }

    #[On('markAllAsRead')]
    public function markAllAsRead(): void
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
    }

    public function with()
    {
        return [
            'notifications' => Auth::user()->notifications()->paginate(10),
        ];
    }
}; ?>

<div>

    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('All Notifications') }}
    </h2>

    <div class="py-12">

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center justify-between mb-6">
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <button wire:click="markAllAsRead"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Mark all as read
                        </button>
                    @endif
                </div>

                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50 dark:bg-gray-700' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @if(!$notification->read_at)
                                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $notification->data['message'] ?? 'Notification' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                            @if(isset($notification->data['event']))
                                                <span
                                                    class="inline-block mt-2 px-2 py-1 text-xs bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded">
                                                    {{ ucfirst(str_replace('_', ' ', $notification->data['event'])) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!$notification->read_at)
                                        <button wire:click="markAsRead({{ $notification->id }})"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            Mark as read
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 dark:text-gray-500 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 6 6v3.75l2.25 2.25a.75.75 0 0 1-.75 1.25H3a.75.75 0 0 1-.75-.75V9.75a6 6 0 0 1 6-6Z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications yet</h3>
                        <p class="text-gray-500 dark:text-gray-400">You'll see notifications here when they arrive.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>