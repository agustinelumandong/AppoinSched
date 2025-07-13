<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
  #[On('markAsRead')]
  public function markAsRead($data): void
  {
    $user = Auth::user();
    $notificationId = $data['id'] ?? null;
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

  public function render()
  {
    return view('components.notification-bell');
  }
}