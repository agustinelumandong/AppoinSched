# AppoinSched Copilot Instructions

## Project Overview
AppoinSched is a Laravel-based appointment scheduling and document request management system built with Laravel 12+, Livewire Volt, Alpine.js, TailwindCSS, and daisyUI. The application serves various user roles: clients, staff members for different departments (MCR, MTO, BPLS), administrators, and super-administrators.

## Core Architecture

### Role-Based Access Control
- Uses Spatie's Permission package for role management
- Key roles: `client`, `MCR-staff`, `MTO-staff`, `BPLS-staff`, `admin`, `super-admin`
- Role-specific routes in `routes/web.php` control access to different sections

### Key Components
- **Offices**: Departments offering services (e.g., MCR, MTO, BPLS)
- **Services**: Services offered by each office
- **Appointments**: Scheduled meetings with staff members
- **Document Requests**: Requests for official documents from offices

### Livewire Volt Components
- Uses anonymous class syntax for components (see `/resources/views/livewire/**/*.blade.php`)
- Routes defined with `Volt::route()` syntax in `routes/web.php`
- Components render with Blade templates using Livewire's reactivity
- Each component file begins with PHP imports and uses strict typing declarations
- Components are registered in `VoltServiceProvider.php` using `Volt::mount()`
- Components leverage Livewire's attribute system like `#[On('eventName')]` for event handling
- Role-based access is often checked in component's `mount()` method

## Development Workflow

### Setup Commands
```bash
composer install
npm install
php artisan migrate --seed
npm run dev
```

### Testing
```bash
composer test
```

### Development Server
```bash
composer dev   # Runs server, queue listener, and Vite in concurrent mode
```

## Project Patterns

### Models and Relationships
- Models use Eloquent relationships and have descriptive scopes (e.g., `scopePending()`)
- Status constants are defined as class constants (e.g., `STATUS_PENDING`)
- Reference example: `Appointments.php` model

### Livewire Components
- Components are organized by user role (`/resources/views/livewire/admin`, `/livewire/staff`, etc.)
- Use lifecycle hooks and public properties for state management
- Form validation through Livewire's validation system
- Modal dialogs for CRUD operations
- Evezent handling with Livewire's emit/listen pattern and Volt's attribute syntax
- Component state is managed through public properties with PHP 8.2+ type declarations
- Component methods follow naming conventions like `save()`, `delete()`, `resetData()`

### UI/UX Conventions
- TailwindCSS for styling with daisyUI components
- Alpine.js for client-side interactivity
- Responsive design using Tailwind's responsive utilities

## Integration Features
- PayMongo integration for payment processing (see `implement.md`)
- Laravel Notifications for appointment confirmations and reminders

## Key Files to Reference
- `routes/web.php`: All application routes with role-based middleware
- `app/Models/*.php`: Data models and relationships
- `resources/views/livewire/**/*.blade.php`: UI components for different sections
- `database/migrations/*.php`: Database schema definition

## Development Standards
- Use PHP 8.2+ features including typed properties and strict typing
- Follow PSR-12 coding standards
- Maintain SOLID principles in component design
- Use Laravel's service container for dependency injection
