<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    #[On('markAsRead')]
    public function markAsRead(?array $data = null): void
    {
        $user = Auth::user();
        $notificationId = $data['id'] ?? null;
        if ($notificationId) {
            $notification = $user->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->dispatch('notificationRead');
            }
        }
    }

    #[On('markAllAsRead')]
    public function markAllAsRead(): void
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('components.notification-bell', [
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'recentNotifications' => Auth::user()->notifications()->latest()->take(5)->get()
        ]);
    }
}
