# AppoinSched - Online Appointment & Document Request System

AppoinSched is a robust Laravel-based appointment scheduling and document request management system designed for government offices and municipalities. The system streamlines the process of booking appointments and requesting official documents from various departments, enhancing service delivery efficiency.

## 🌟 Features

### For Citizens/Clients

- **User Authentication & Profile Management**
  - Secure user registration and login
  - Comprehensive profile management
  - Family member information management

- **Appointment Booking**
  - Schedule appointments with different government offices
  - Choose from available time slots
  - Receive email notifications and downloadable appointment slips
  - View and manage existing appointments

- **Document Requests**
  - Submit document requests to different departments
  - Upload required supporting documents
  - Track request status
  - Upload payment proof

### For Staff

- **Appointment Management**
  - View and manage incoming appointments
  - Approve, reschedule, or cancel appointments
  - Department-specific appointment views

- **Document Request Processing**
  - Review and process document requests
  - Verify uploaded documents
  - Update request status
  - Verify payment proof

### For Administrators

- **User Management**
  - Manage user accounts and roles
  - View user activity

- **Office & Service Management**
  - Add/edit government offices and departments
  - Configure services offered by each office
  - Set pricing and requirements for services

- **System Configuration**
  - Customize system settings
  - Manage roles and permissions

## 🚀 Technology Stack

- **Backend**: Laravel 12+
- **Frontend**:
  - Livewire Volt for reactive components
  - Alpine.js for client-side interactivity
  - TailwindCSS with daisyUI for styling
- **Database**: MySQL/SQLite
- **Authentication**: Laravel Breeze
- **Authorization**:  
- **PDF Generation**: Laravel DomPDF
- **Notification System**: Laravel Notifications

## 📋 Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL or SQLite

## ⚙️ Installation

### Clone the Repository

```bash
git clone https://github.com/Agustinelumandong/AppoinSched.git
cd AppoinSched
```

### Install Dependencies

```bash
composer install
npm install && npm run build
```

### Set Up Environment

On **Command Prompt** (cmd), use:

```cmd
copy .env.example .env
```

On **PowerShell**, use:

```powershell
Copy-Item .env.example .env
```

**Then:**

```bash

php artisan key:generate
```

Edit the `.env` file to configure your database and mail settings.

*Example set-up:*

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=AppointRequest
DB_USERNAME=root
DB_PASSWORD=

```

### Database Setup

```bash
php artisan migrate

php artisan db:seed
```

### Generate Assets

```bash
npm run dev
```

### Link Storage (for public file access)

```bash
php artisan storage:link
```

### Start the Server

```bash
composer run dev
```

This will start the server, queue listener, and Vite in concurrent mode.

## 👥 Role Configuration

The system includes the following roles:

- `client` - Regular users/citizens
- `MCR-staff` - Municipal Civil Registry staff
- `MTO-staff` - Municipal Treasurer's Office staff
- `BPLS-staff` - Business Permits & Licensing System staff
- `admin` - System administrators
- `super-admin` - Super administrators with full access

Default users are created through seeders with the following credentials:

- Admin: `admin@example.com` (password: password)
- Client: `user@example.com` (password: password)

## 🛠️ Development

### Running Tests

```bash
composer test
```

### Code Style

The project follows PSR-12 coding standards. You can format your code using Laravel Pint:

```bash
./vendor/bin/pint
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📧 Contact

For any inquiries or issues, please open an issue on the GitHub repository.

---

Built with ❤️ using Laravel and Livewire Volt.
