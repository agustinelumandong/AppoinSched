# AppoinSched Copilot Instructions

## Project Overview

AppoinSched is a Laravel 12+ appointment scheduling and document request management system for government offices. Built with **Livewire Volt**, **Alpine.js**, **TailwindCSS**, and **daisyUI**. Serves multiple user roles with distinct permission levels across government departments (MCR, MTO, BPLS).

## Architecture Essentials

### Role-Based Access Control (Critical Pattern)

-   **Spatie Permission** manages role hierarchy: `client` → `{office}-staff` → `admin` → `super-admin`
-   Role checks happen in **two places**: middleware in `routes/web.php` AND component `mount()` methods
-   Example: Staff can only see their office's data, clients only their own records

### Core Domain Models

-   **User**: Extended with profiles, addresses, families (complex user data structure)
-   **Offices**: Government departments with services and staff assignments
-   **Appointments**: Time-based bookings with reference numbers and status tracking
-   **DocumentRequest**: Document processing workflow with payment verification

### Livewire Volt Components Pattern

All components use **anonymous class syntax** with strict typing:

```php
<?php
declare(strict_types=1);
use Livewire\Volt\Component;

new class extends Component {
    public function mount() {
        // Role validation ALWAYS here
        if (!auth()->user()->hasRole('expected-role')) {
            abort(403, 'Unauthorized');
        }
    }

    public function with(): array {
        // Data fetching pattern
        return ['key' => Model::with(['relations'])->get()];
    }
}; ?>
```

## Development Workflow

### Essential Commands

```bash
# Full development stack (server + queue + vite)
composer dev

# Database reset with seeding
php artisan migrate:fresh --seed

# Run tests
composer test
```

### Component Development Pattern

1. **File Location**: `/resources/views/livewire/{role}/{feature}.blade.php`
2. **Route Definition**: `Volt::route('path', 'folder.component')->name('route.name')`
3. **Role Middleware**: Apply in `routes/web.php` AND verify in component `mount()`

### Notification System Architecture

-   **Enums**: `AdminNotificationEvent` and `RequestNotificationEvent` define event types
-   **Real-time**: `NotificationBell` component with `#[On('markAsRead')]` pattern
-   **Database**: Laravel notifications table stores structured notification data

## Project-Specific Patterns

### Status Management

Models use string status fields with enum-like constants:

```php
// In model classes
public const STATUS_PENDING = 'pending';
public const STATUS_APPROVED = 'approved';
```

### Data Relationships (Complex)

-   Users have multiple address types and family member records
-   Appointments link User → Office → Service with separate details table
-   Document requests follow similar pattern with payment tracking

### Frontend Conventions

-   **daisyUI** components over custom CSS (`btn`, `card`, `modal` classes)
-   **Alpine.js** for simple interactions, Livewire for data operations
-   **TailwindCSS** responsive-first approach

## Integration Points

### External Services

-   **PayMongo**: Payment processing (implementation details in `implement.md`)
-   **DOMPDF**: PDF generation for appointment slips and documents
-   **Laravel Notifications**: Email system for appointment confirmations

### Key Dependencies

-   `livewire/volt`: Anonymous component system
-   `spatie/laravel-permission`: Role/permission management
-   `daisyui`: UI component library
-   `spatie/laravel-livewire-wizard`: Multi-step forms

## Critical Files for Context

-   `routes/web.php`: Complete role-based routing structure
-   `app/Models/User.php`: Extended user model with profile methods
-   `app/Enums/*NotificationEvent.php`: System event definitions
-   `resources/views/livewire/*/dashboard.blade.php`: Role-specific dashboard patterns

## Common Gotchas

-   Always check both route middleware AND component `mount()` for authorization
-   Use `with()` method for data fetching in Volt components, not `render()`
-   Status fields are strings, not enums - define constants in models
-   Component file paths must match Volt route definitions exactly
