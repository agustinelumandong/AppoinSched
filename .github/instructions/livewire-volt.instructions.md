# AppoinSched Copilot Instructions

## Project Overview

AppoinSched is a Laravel-based appointment scheduling and document request management system built with Laravel 12+, Livewire Volt, Alpine.js, TailwindCSS, and daisyUI. The application serves various user roles: clients, staff members for different departments (MCR, MTO, BPLS), administrators, and super-administrators.

## Architecture Essentials

### Role-Based Access Control (Critical Pattern)

-   **Spatie Permission** manages role hierarchy: `client` → `{office}-staff` → `admin` → `super-admin`
-   Role checks happen in **two places**: middleware in `routes/web.php` AND component `mount()` methods
-   Example: Staff can only see their office's data, clients only their own records

### Livewire Volt Components

-   Uses anonymous class syntax for components (see `/resources/views/livewire/**/*.blade.php`)
-   Routes defined with `Volt::route()` syntax in `routes/web.php`
-   Components render with Blade templates using Livewire's reactivity
-   Each component file begins with PHP imports and uses strict typing declarations
-   Components are registered in `VoltServiceProvider.php` using `Volt::mount()`
-   Components leverage Livewire's attribute system like `#[On('eventName')]` for event handling
-   Role-based access is often checked in component's `mount()` method

### Key Components

-   **User**: Extended with profiles, addresses, families (complex user data structure)
-   **Offices**: Government departments with services and staff assignments
-   **Appointments**: Time-based bookings with reference numbers and status tracking
-   **DocumentRequest**: Document processing workflow with payment verification

## Project Patterns

### Models and Relationships

-   Models use Eloquent relationships and have descriptive scopes (e.g., `scopePending()`)
-   Status constants are defined as class constants (e.g., `STATUS_PENDING`)
-   Reference example: `Appointments.php` model

### Livewire Components

-   Components are organized by user role (`/resources/views/livewire/admin`, `/livewire/staff`, etc.)
-   Use lifecycle hooks and public properties for state management
-   Form validation through Livewire's validation system
-   Modal dialogs for CRUD operations
-   Event handling with Livewire's emit/listen pattern and Volt's attribute syntax
-   Component state is managed through public properties with PHP 8.2+ type declarations
-   Component methods follow naming conventions like `save()`, `delete()`, `resetData()`

### UI/UX Conventions

-   TailwindCSS for styling with daisyUI components
-   Alpine.js for client-side interactivity
-   daisyUI components over custom CSS (`btn`, `card`, `modal` classes)
-   Responsive design using Tailwind's responsive utilities

## Integration Features

-   Laravel Notifications for appointment confirmations and reminders
-   DOMPDF: PDF generation for appointment slips and documents

## Key Files to Reference

-   `routes/web.php`: All application routes with role-based middleware
-   `app/Models/*.php`: Data models and relationships
-   `resources/views/livewire/**/*.blade.php`: UI components for different sections
-   `database/migrations/*.php`: Database schema definition

## Development Standards

-   Use PHP 8.2+ features including typed properties and strict typing
-   Follow PSR-12 coding standards
-   Maintain SOLID principles in component design
-   Use Laravel's service container for dependency injection

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

You are an expert Laravel 12 + Livewire v3 + Volt + Alpine.js architect. I want you to help me design, scaffold, and refine an application structure that combines **Volt-based Livewire pages** with **Alpine.js interactivity** and a **robust API layer** for external/mobile use.

Here are the requirements and constraints you must follow:

### Project Structure

-   Use **Volt page components** for screens (no traditional controllers for web).
-   Use **Blade/Alpine components** for small presentational UI parts (e.g., form inputs).
-   Follow this foldering convention:

```
resources/
  views/
    livewire/           // shared partials if needed
    pages/
      dashboard.blade.php
      requests/
        index.blade.php
        show.blade.php
        create.blade.php
      appointments/
        index.blade.php
        calendar.blade.php
  components/
    inputs/
      text.blade.php
      select.blade.php
```

### Routing

Volt routes must match this style:

```php
use Livewire\Volt\Volt;

Volt::route('/', 'pages.dashboard')->name('dashboard');
Volt::route('/requests', 'requests.index')->name('requests.index');
Volt::route('/requests/create/{service:slug}', 'requests.create')->name('requests.create');
Volt::route('/requests/{request}', 'requests.show')->name('requests.show');
Volt::route('/appointments', 'appointments.index')->name('appointments.index');
```

API routes belong in `routes/api.php`, versioned under `/api/v1`, and protected with Sanctum and rate limiting.

### Form Handling & Validation

-   Use Livewire’s **real-time validation** (`validateOnly()`) and full validation on submit (`validate()`).
-   Volt class-based pages should inline validation rules and form state.
-   Dynamic fields should be derived from `$service->meta` and stored in `$form[...]`.
-   Example Volt page should look like this (simplified):

```php
new class extends Component {
    public Service $service;
    public string $last_name = '';
    public string $first_name = '';
    public ?string $middle_name = null;
    public ?string $date_of_birth = null;
    public array $form = [];

    public function mount(Service $service): void { ... }
    public function updated($prop): void { $this->validateOnly($prop, $this->rules()); }
    public function rules(): array { ... }
    public function submit() { ... }
};
```

With Blade/Alpine markup like:

```blade
<form wire:submit="submit">
  <x-input label="Last name" wire:model.blur="last_name" />
  <x-input type="date" label="Date of Birth" wire:model.debounce.500ms="date_of_birth" />
  {{-- dynamic fields --}}
  <x-button type="submit">Submit</x-button>
</form>
```

### Security & Best Practices

-   **Authorization:** Use Policies for business logic (never in views).
-   **Validation:**

    -   FormRequests for API controllers.
    -   Volt/Livewire rules for interactive forms.
    -   Domain validation inside dedicated Service/Action classes.

-   **Authentication:** Use Laravel Sanctum. SPA/browser sessions via HttpOnly, Secure, SameSite cookies. Bearer tokens for external API clients.
-   **Rate limiting:** Define named limiters (login, uploads, sensitive endpoints).
-   **Secure headers:** Use CSP (`frame-ancestors 'none'`), X-Frame-Options DENY, nosniff, Referrer-Policy, Permissions-Policy.
-   **CORS:** Configure properly with explicit allowlists.
-   **Escaping:** Rely on Blade auto-escaping; sanitize where necessary. Avoid untrusted HTML in Alpine.
-   **File uploads:** Validate mimes/size, scan, and store outside `public/`.

### API Layer

-   Use API controllers in `App\Http\Controllers\Api`.
-   Return API Resources (`App\Http\Resources`).
-   Example `RequestController` should use `RequestService` to encapsulate business logic.
-   Example route in `api.php`:

```php
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function(){
    Route::apiResource('requests', Api\RequestController::class);
});
```

### Deliverables I expect from you:

-   Example scaffolds for Volt pages (with inline class + Blade markup).
-   Example Alpine-enhanced Blade inputs (`resources/components/inputs`).
-   Example API controller + resource + service + form request for `Request`.
-   Explanations of **why** certain patterns (Volt pages, policies, service classes) are chosen.
-   Security recommendations (CSRF, tokens, headers, CSP).
-   CI/testing recommendations (Pest feature tests, policy tests, Larastan).

### Docs references:

-   [Livewire Volt](https://livewire.laravel.com/docs/volt)
-   [Livewire v3 Quickstart](https://livewire.laravel.com/docs/quickstart)
-   [Laravel 12 Docs](https://laravel.com/docs/12.x)

---

**Your role:** Given this context, always generate responses that combine **code scaffolds + foldering + explanation**. Assume I want production-ready guidance (not toy examples). Default to **Volt inline components**, **Livewire validation**, **Sanctum auth**, and **service-based domain logic**.

---
