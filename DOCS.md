# Internal Academy 🎓

A web application to manage company internal workshops and registrations, built as a technical challenge for Corsi.it.

## Tech Stack

- **Backend:** Laravel 13
- **Frontend:** Vue.js 3 + Inertia.js
- **Database:** MySQL
- **Testing:** PHPUnit 12

## Requirements

- PHP 8.3+
- Composer 2+
- Node.js 22+
- MySQL

## Installation

### 1. Clone the repository
git clone https://github.com/anxietyPotato/CORSI
cd CORSI

### 2. Install dependencies
composer install
npm install

### 3. Environment setup
copy .env.example .env
php artisan key:generate

### 4. Configure database
Update .env with your MySQL credentials:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=internal_academy
DB_USERNAME=root
DB_PASSWORD=

### 5. Run migrations
php artisan migrate

### 6. Seed test data
php artisan db:seed

This creates:
- Admin: admin@academy.com / password
- Employee: employee@academy.com / password
- 4 sample workshops

### 7. Start the application

Terminal 1:
php artisan serve

Terminal 2:
npm run dev

Visit: http://localhost:8000

## Running Tests

Create a test database called internal_academy_test in MySQL, then:
php artisan test

## Artisan Commands

Send reminder emails to tomorrow's workshop participants:
php artisan academy:remind

## Features

### Must Have
- Two roles: Admin and Employee with different interfaces
- Admin can create, edit and delete workshops
- Each workshop has title, description, date/time and capacity
- Employees can view future workshops and register with one click
- Employees can cancel registration, immediately freeing the seat

### Show Off Skills
- Waiting List: When workshop is full, employees join waiting list. When a confirmed participant cancels, first person on waiting list is automatically promoted (FIFO)
- No Ubiquity: Employees cannot register for two overlapping workshops
- Reminder Command: php artisan academy:remind sends reminder emails to all participants of tomorrows workshops

### Top Player Zone
- Statistics Dashboard: Shows most popular workshop, total registrations per workshop with fill rate bars
- Real-time Updates: Registration counter updates every 5 seconds via polling without page refresh
- Tests: 36 passing feature and unit tests with PHPUnit

## Architectural Decisions

### PHP Enums over DB enums
Used UserRole and RegistrationStatus PHP enums instead of database-level enums for better type safety, maintainability and IDE support.

### MySQL InnoDB Engine
Manually changed MySQL engine from MyISAM to InnoDB because MyISAM does not support foreign keys, which are essential for maintaining referential integrity between workshops and registrations.

### Form Requests for validation
Validation logic is separated from controllers using StoreWorkshopRequest and UpdateWorkshopRequest to keep controllers clean and focused on a single responsibility.

### Workshop ownership
Only the admin who created a workshop can edit or delete it. Other admins can view but not modify workshops they did not create. This decision was made to respect content ownership.

### FIFO Waiting List
When a confirmed participant cancels, the first person on the waiting list ordered by registered_at timestamp is automatically promoted to confirmed status, ensuring fair queue management.

### Overlap Prevention
Users cannot register for two workshops that overlap in time. This is enforced at the backend level in RegistrationController regardless of frontend state.

### Polling over WebSockets
Used simple polling every 5 seconds instead of WebSockets or Laravel Reverb for the real-time counter. This avoids unnecessary dependencies and infrastructure complexity while still delivering the real-time feel the requirement asked for.
