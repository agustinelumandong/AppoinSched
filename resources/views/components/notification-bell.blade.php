<!-- Remove the PHP variables as they're now passed from the component -->
<div class="relative" x-data="{ open: false }" @notification-read.window="open = false" wire:poll.10s>
    <!-- Notification Bell -->
    <button @click="open = !open"
        class="relative p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 6 6v3.75l2.25 2.25a.75.75 0 0 1-.75 1.25H3a.75.75 0 0 1-.75-.75V9.75a6 6 0 0 1 6-6Z">
            </path>
        </svg>

        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">

        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white">Notifications</h4>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        style="font-size: 12px;">
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @if ($recentNotifications->count() > 0)
                @foreach ($recentNotifications as $notification)
                    @if (!$notification->read_at)
                        <div
                            class="p-2 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $notification->read_at ? '' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @if (!$notification->read_at)
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $notification->data['message'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <button wire:click="markAsRead({id: '{{ $notification->id }}'})"
                                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    style="font-size: 12px;">
                                    Mark as read
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
            @if ($recentNotifications->count() == 0 || $unreadCount == 0)
                <div class="p-2 text-center text-gray-500 dark:text-gray-400 mt-2">
                    <p>No unread notifications</p>
                </div>
            @endif
        </div>

        @if ($recentNotifications->count() > 0)
            <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                <a href="{{ route('notifications.index') }}"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
