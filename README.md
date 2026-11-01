# BuildTrack - Civil Works & Field Operations Management System

BuildTrack is a web-based field operations and site logistics management system designed for civil engineering firms, general contractors, and construction managers. The platform consolidates site activities, workforce rosters, machinery deployment, and financial disbursements into a unified workspace.

---

## Key Modules & Capabilities

### 1. Project Portfolio & Site Supervision
* Manage multiple civil construction projects across active, on-hold, and completed states.
* Track project parameters including client identity, site locations, total contract values, budget allocations, and scheduled completion dates.
* Centralized timeline monitoring and status reporting.

### 2. Workforce Rostering & Field Attendance
* Maintain comprehensive worker profiles categorized by trade, role, and department (masons, carpenters, machine operators, supervisors).
* Daily field attendance recording with shifts, check-in timestamps, and absence categorization.
* Real-time headcount tallies for active job sites.

### 3. Daily Site Progress Reports
* Structured daily logging mechanism for field supervisors.
* Track meteorological conditions, temperature, and weather impacts on physical progress.
* Record delays, equipment downtime, raw material shipments, and milestone completions.
* Site safety remarks and supervisory observations.

### 4. Heavy Machinery Fleet & Compliance
* Inventory registry of earthmoving equipment, dump trucks, excavators, cranes, and concrete mixers.
* Real-time operational statuses (active, in-maintenance, idle, decommissioned).
* Machinery compliance documentation management: insurance policies, fitness certificates, and road permits with expiration alerts.

### 5. Field Payroll & Overtime Disbursements
* Calculation engine linking daily attendance logs with wage scales.
* Overtime wage calculation based on verified site attendance records.
* Historical disbursement records for labor cost auditing and contractor settlements.

### 6. Activity & Audit Trail
* Automated recording of critical operational actions: project creation, wage approvals, and machinery reassignment.
* Detailed timestamped audit trails ensuring accountability across site management teams.

### 7. Dual Theme Interface
* High-contrast dark and light display modes engineered for outdoor visibility on mobile field tablets as well as indoor workstations.

---

## Technical Stack

* Framework: Laravel 13
* Runtime: PHP 8.5
* Database: MySQL 8.0
* Frontend: Tailwind CSS, Blade Templating Engine
* Containerization: Docker, Nginx

---

## Project Structure

```
app/
├── Http/Controllers/     Application request handlers and controllers
├── Models/               Eloquent models (Project, Employee, DailyReport, Machinery, PayrollRecord)
resources/
├── views/                Blade templates (Dashboard, Projects, Attendance, Machinery, Payroll)
routes/
├── web.php               Application route definitions
database/
├── migrations/           Relational database schema definitions
├── seeders/              Default demo data and initial lookup records
```

---

## Installation & Deployment

### Prerequisites
* Docker and Docker Compose
* Git

### Setup Instructions
1. Clone the repository into your local development environment:
   ```bash
   git clone <repository_url>
   cd <repository_folder>
   ```

2. Copy the sample environment configuration:
   ```bash
   cp .env.example .env
   ```

3. Update the database and environment variables in `.env`:
   ```env
   APP_NAME=BuildTrack
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel13_civil
   DB_USERNAME=usr_civil
   DB_PASSWORD=your_secure_password
   ```

4. Launch the application container:
   ```bash
   docker compose up -d
   ```

5. Install application dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

6. Generate the application encryption key:
   ```bash
   php artisan key:generate
   ```

7. Run database migrations and load sample fixtures:
   ```bash
   php artisan migrate --seed
   ```

8. Clear and cache configuration:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## License

This software is distributed under the proprietary license of the author. All rights reserved.
