# PRD — BuildTrack India: Civil Works & Field Operations Management System (v3.0 Final)

**Document Version:** 3.0 (Deterministic CLI Agent Execution Ready)
**Application Language:** English (`en_IN`)
**Currency:** Indian Rupee (`INR`, symbol `Rs.` / `INR`, standardized to `DECIMAL(14,2)`)
**Target Stack:** Laravel 13, PHP 8.5, MySQL 8.0, Vite 8, Tailwind CSS 4, Blade, Alpine.js, Nginx
**Application Timezone:** `Asia/Kolkata` (IST)
**Application Mode:** Interactive Industry Demo (`DEMO_MODE=true` with Passwordless 1-Click Role Selection & Live Role Switcher)

---

## 0. Strict Directives for CLI AI Coding Agent (`agy cli`)

Read this entire specification before running commands or generating files. You must deliver a working, database-driven Laravel 13 web application where every button, filter, calculation, export, and role restriction works end-to-end.

### 0.1 Hard Technical & Visual Constraints
1. **ZERO EMOJIS ANYWHERE:** Do not place any emoji characters in PHP code, Blade templates, JavaScript, CSS, migrations, seeders, validation messages, comments, or `/docs`. Use only clean inline SVG icons (Lucide / Heroicons outline style).
2. **100% ENGLISH UI & SEED DATA:** Every heading, form label, button, status badge, flash message, seeded record, and documentation section must be written in professional English.
3. **STANDARDIZED CURRENCY (`INR` / `DECIMAL(14,2)`):** All monetary columns across all tables must use `DECIMAL(14,2)` in MySQL. Format currency values in the UI with `INR` or `Rs.` and 2 decimal places (e.g., `INR 45,000.00`).
4. **PUBLIC VS PROTECTED ROUTES:**
   - `GET /login` and `GET /docs` must be publicly accessible (both guests and authenticated users can view `/docs`).
   - All operational routes (`/dashboard`, `/projects`, `/daily-reports`, `/employees`, `/attendance`, `/payroll`, `/machineries`, `/compliance`, `/exports`, `/activity-logs`) require authentication and server-side role authorization.
5. **STACK & ASSET RULES:**
   - **Laravel 13 + PHP 8.5:** Use standard MVC with Form Requests, Eloquent Relationships, Policies/Middleware, and Service classes for calculations.
   - **Tailwind CSS 4 + Vite 8:** Use `@import "tailwindcss";` in `resources/css/app.css`. Ensure `npm run build` completes with zero errors.
   - **Alpine.js:** Include Alpine.js for modals, mobile navigation drawers, tabs, and bulk-attendance toggles.

### 0.2 Mandatory Business Rules & Safety Guardrails

#### Rule A — Demo Authentication Safety (`DEMO_MODE`)
- Add `DEMO_MODE=true` to `.env` and `.env.example`, exposed via `config('app.demo_mode', env('DEMO_MODE', false))`.
- Passwordless 1-click role login (`POST /demo/login`) and top-bar role switching (`POST /demo/switch-role`) are permitted **only** when `config('app.demo_mode') === true`. If `false`, those endpoints must abort with `403 Forbidden`.
- Always verify `user.is_active === true` before logging in or switching roles; reject inactive accounts with an error flash message.
- Always call `$request->session()->regenerate()` immediately after authenticating or switching roles.
- Never rely on hidden UI buttons for security; enforce all permissions on the backend via Middleware/Policies.
- Clearly state in `/login` and `/docs` that passwordless role switching is a synthetic evaluation feature for `DEMO_MODE=true` and must be disabled in live production deployments.

#### Rule B — Deterministic Project Progress Sync
- `daily_reports.reported_progress_percent` represents the **cumulative overall completion percentage** ($0$ to $100$) of the project as of `report_date`.
- Reports with status `submitted` or `flagged` **must not** modify `projects.overall_progress`.
- Only when a report transitions to `reviewed` (approved by `admin` or assigned `project_manager`) does the system evaluate `projects.overall_progress`:
  - Find the most recent `reviewed` report for that project ordered by `report_date DESC, id DESC`.
  - Official project progress cannot decrease automatically from older reports: update `projects.overall_progress` to $\max(\text{projects.overall\_progress}, \text{reported\_progress\_percent})$ only when the reviewed report's `report_date` is greater than or equal to the latest previously reviewed report date.
- An `admin` may manually adjust `projects.overall_progress` on the Project Edit form, which must write an audit entry in `activity_logs`.

#### Rule C — Attendance Defaults, Overtime Approval & Payroll Locking
- **Attendance Status Defaults:**
  | Attendance Status | Paid Day Weight | Default `regular_hours` | Allowed `overtime_hours` |
  | :--- | :---: | :---: | :---: |
  | `present` | $1.0$ | $8.00$ | $0.00$ to $12.00$ |
  | `half_day` | $0.5$ | $4.00$ | $0.00$ to $8.00$ |
  | `paid_leave` | $1.0$ | $0.00$ | Must be $0.00$ |
  | `absent` | $0.0$ | $0.00$ | Must be $0.00$ |
- **Overtime Approval Workflow:**
  - `attendance_records.overtime_approved` defaults to `false`.
  - When a `foreman` records overtime (`overtime_hours > 0`), `overtime_approved` is saved as `false` (Pending Approval).
  - Only `admin`, `project_manager` (for assigned projects), or `hr_payroll` can approve overtime, which sets `overtime_approved = true`, `overtime_approved_by = auth()->id()`, and `overtime_approved_at = now()`.
  - Only attendance rows where `overtime_approved === true` are included in payroll overtime pay calculations.
- **Payroll State Machine & Locking:**
  - Valid transitions: `draft` $\rightarrow$ `approved` $\rightarrow$ `paid`.
  - `draft` records can be recalculated at any time from eligible attendance records, and `allowances_inr` / `deductions_inr` can be edited.
  - `approved` records are **locked financial snapshots**. They cannot be recalculated, overwritten, or deleted.
  - `paid` records can only be transitioned from `approved` (setting `paid_at = now()`) and are strictly immutable.
  - When the Payroll Generator runs for `period_month` (`YYYY-MM`), it **must skip** any employee who already has an `approved` or `paid` record for that month and display a summary message (e.g., *"Recalculated 6 draft records; skipped 4 locked (approved/paid) records"*).
- **Operational Scope Disclaimer:** Document clearly in the UI and `/docs` that the payroll module is an *Operational Wage & Overtime Calculator (INR)* for civil works and not a statutory Indian tax/EPF/ESIC filing engine.

#### Rule D — In-App Document Expiry Reminders (Idempotent)
- MVP expiry reminders are **in-app compliance alerts** stored in `activity_logs` and displayed in the Compliance & Expiry Center and Top Notification Drawer. Do not claim external emails or SMS messages were transmitted.
- Running `php artisan machinery:check-expiries` or clicking **"Run Expiry Check Now"** scans all documents where `expiry_date <= today + 30 days`.
- **Idempotency Rule:** Re-running the check on the same calendar day (`Asia/Kolkata`) must not create duplicate `activity_logs` entries for the same document. Update `last_reminder_logged_at` on `machinery_documents` and only insert a new `activity_logs` alert if `last_reminder_logged_at` is null or earlier than `Carbon::today('Asia/Kolkata')`.

#### Rule E — Data Integrity, Deletion Protection & Export Safety
- **No Destructive Cascades on Historical Records:** Never use `ON DELETE CASCADE` on `projects` or `employees` for tables that hold operational history (`daily_reports`, `attendance_records`, `payroll_records`, `machineries`). Use `restrictOnDelete()` on foreign keys and block deletion in controllers with a friendly error message if related records exist (encourage changing status to `completed` or `inactive` instead).
- **CSV Formula Injection Protection:** Every CSV export cell whose string value begins with `=`, `+`, `-`, or `@` must be prefixed with a single quote (`'`) before streaming so spreadsheet software does not execute formulas. Include UTF-8 BOM (`\xEF\xBB\xBF`) at the start of CSV files for Microsoft Excel compatibility.
- **Browser Print / Save as PDF:** Print endpoints (`/exports/*/print` and `/payroll/{payroll}/payslip`) must render clean, print-styled HTML pages (`@media print`) that trigger `window.print()` so users can print or select **"Save as PDF"** natively in their browser.

---

## 1. Navigation Menu & Feature Architecture (Practical & Non-Overkill)

All features below are powered strictly by the **9 core database tables**—giving the client a rich, complete civil-works system without schema bloat:

1. **Executive Dashboard (`/dashboard`)**
   - Unified Global Slice Filter Bar (`Project`, `Location`, `Date / Month`).
   - Role-adaptive KPI cards, Project Progress vs. Target timeline bars, Urgent Machinery Compliance table, Recent Site Reports, and Mobile Field Quick-Action buttons.
2. **Projects & Sites (`/projects`, `/projects/{project}`)**
   - Project portfolio list with status, location, budget (`INR`), and completion percentage bar.
   - **Project Command View (`/projects/{project}`):** Single-page project hub with 4 tabs: *Overview & Progress*, *Daily Site Reports*, *Assigned Workforce*, and *Deployed Machinery*.
3. **Daily Site Reports (`/daily-reports`)**
   - Filterable log of concrete pours, earthwork, reinforcement, and road base work.
   - **1-Screen Mobile Quick Logger (`/daily-reports/create`):** Pre-selects today's date, active project, and default weather so a foreman can log work in under 15 seconds.
   - **Supervisor Review Queue:** 1-click filter for `submitted` / `flagged` reports awaiting PM/Admin approval.
4. **Workforce & Attendance (`/employees`, `/attendance`, `/attendance/bulk`)**
   - Employee directory (`monthly` staff vs. `daily` muster-roll workers).
   - **Bulk Daily Crew Attendance Sheet (`/attendance/bulk`):** Select a Project and Date to mark an entire site crew's status (`present`, `half_day`, `absent`, `paid_leave`) and overtime hours on a single screen.
   - **Overtime Approval Queue:** Dedicated filter tab allowing PM/HR/Admin to approve pending overtime in 1 click.
5. **Payroll & Payslips (`/payroll`, `/payroll/{payroll}/payslip`)**
   - Monthly INR Payroll Engine (`YYYY-MM` selector), showing Days Present, Approved OT Hours, Base Earned, OT Pay, Allowances, Deductions, Net Salary (`INR`), and Lock Status (`draft`, `approved`, `paid`).
   - Inline editor for allowances/deductions on `draft` rows, plus 1-click **Approve** and **Mark Paid** actions.
   - **Printable Payslip View:** Formatted employee payslip with company header, breakdown in `INR`, and browser print/PDF trigger.
6. **Machinery & Fleet Register (`/machineries`)**
   - Heavy equipment directory showing asset code, registration plate, category, operational status (`active_on_site`, `available_yard`, `under_maintenance`), assigned project, and assigned driver.
7. **Compliance & Expiry Center (`/compliance`)**
   - Dedicated view over `machinery_documents` segmented by status tabs: `All`, `Expired`, `Critical (<= 7 Days)`, `Expiring Soon (8-30 Days)`, `Flagged by Driver`, and `Valid`.
   - Includes **1-Click Driver Renewal Flagging** modal and the **"Run Expiry Check Now"** button.
8. **Reports & Export Hub (`/exports`)**
   - Central reporting page where authorized roles slice data by Project, Location, and Date/Month, and trigger **Excel-Safe CSV Downloads** or **Browser Print / Save as PDF Summaries**.
9. **Activity & Audit Log (`/activity-logs`)**
   - Searchable and module-filterable audit trail showing every important action, progress override, payroll approval, driver renewal flag, and automated expiry reminder check.
10. **System Documentation (`/docs`)**
    - Public & in-app single-page interactive documentation explaining every module, formula, role permission matrix (with 1-click role switch buttons), and deployment guide.

---

## 2. Role-Based Access Control (RBAC) & Scoping Matrix

Seed 6 demo accounts (one per role). Enforce both **Action Authorization** and **Project/Asset Scoping** on the backend:

### 2.1 Demo Accounts & Scoping Rules
| Role Slug | Demo User Name | Scoping Rule |
| :--- | :--- | :--- |
| `admin` | Rajesh Sharma (Owner / Director) | Unscoped access to all projects, employees, payrolls, machinery, exports, and audit logs. |
| `project_manager` | Vikram Patel (Project Manager) | Scoped to assigned projects (`projects.manager_id = user.id`) for editing/reviewing reports and approving overtime; read-only visibility on overall portfolio. Cannot view or manage Payroll. |
| `foreman` | Suresh Nair (Site Foreman) | Mobile-first field view. Can create/view daily reports and record attendance for active projects; can flag machinery document issues. Cannot approve reports/OT or view Payroll. |
| `hr_payroll` | Priya Menon (HR & Payroll Officer) | Full access to Employees, Attendance, Overtime Approval, Payroll Generation/Approval/Payment (`INR`), and HR/Payroll Exports. Read-only on Projects. |
| `asset_manager` | Arun Deshmukh (Fleet & Asset Manager) | Full access to Machinery Register, Compliance Documents, Expiry Check Runner, and Fleet Exports. Cannot access Payroll. |
| `driver` | Manoj Yadav (Operator / Driver) | Mobile-first view scoped to assigned machinery (`machineries.assigned_driver_id = user.id`). Can view machine details, check document validity, and flag documents for renewal. |

### 2.2 Action Permission Matrix
| Feature / Action | `admin` | `project_manager` | `foreman` | `hr_payroll` | `asset_manager` | `driver` |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| View Executive Dashboard | Full | Project-focused | Field Quick View | HR/Payroll View | Fleet View | Driver View |
| Create / Edit Projects | Yes | Assigned only | No | No | No | No |
| Submit Daily Site Report | Yes | Yes (Assigned) | Yes | No | No | No |
| Review / Approve Daily Report | Yes | Yes (Assigned) | No | No | No | No |
| Manage Employee Master | Yes | View only | View crew only | Yes | No | No |
| Record Daily / Bulk Attendance | Yes | Yes (Assigned) | Yes | Yes | No | No |
| Approve Overtime Hours | Yes | Yes (Assigned) | No | Yes | No | No |
| Access & Run Payroll (`INR`) | Yes | No | No | Yes | No | No |
| Manage Machinery & Documents | Yes | View only | View only | No | Yes | View assigned |
| Flag Document for Renewal | Yes | Yes | Yes | No | Yes | Yes (Assigned) |
| Run Expiry Reminder Check | Yes | No | No | No | Yes | No |
| Export Reports (CSV / Print) | All | Project & Att. | No | Att. & Payroll | Fleet & Expiry | No |
| View Activity & Audit Logs | Yes | Yes | No | Yes | Yes | No |

---

## 3. Consolidated Database Schema (9 Core Tables)

All monetary columns use `DECIMAL(14,2)`. Foreign keys protecting historical records must use `restrictOnDelete()` or `nullOnDelete()`.

### 3.1 `users`
- `id` (PK)
- `name` (string)
- `email` (string, unique)
- `role` (string, indexed: `admin`, `project_manager`, `foreman`, `hr_payroll`, `asset_manager`, `driver`)
- `phone` (string, nullable)
- `is_active` (boolean, default `true`)
- `timestamps`

### 3.2 `projects`
- `id` (PK)
- `code` (string, unique, e.g., `CW-PUN-01`)
- `name` (string)
- `client_name` (string)
- `location` (string, indexed)
- `manager_id` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `budget_inr` (decimal 14,2, default `0.00`)
- `start_date` (date)
- `target_end_date` (date)
- `overall_progress` (unsigned tinyInteger, `0`–`100`, default `0`)
- `status` (string, indexed: `planned`, `active`, `on_hold`, `completed`)
- `description` (text, nullable)
- `timestamps`

### 3.3 `daily_reports`
- `id` (PK)
- `project_id` (FK -> `projects.id`, `restrictOnDelete()`)
- `reported_by` (FK -> `users.id`, `restrictOnDelete()`)
- `report_date` (date, indexed)
- `work_category` (string: `Concrete Pouring (RCC/PCC)`, `Earthwork & Excavation`, `Reinforcement & Shuttering`, `Road Base & Bitumen`, `Masonry & Finishing`, `Site Clearance`)
- `activity_summary` (text)
- `quantity_completed` (decimal 10,2, nullable)
- `quantity_unit` (string, nullable: `m3`, `sq.m`, `MT`, `RMT`, `Nos`)
- `reported_progress_percent` (unsigned tinyInteger, `0`–`100`)
- `labor_count` (unsigned integer, default `0`)
- `weather` (string: `Clear / Sunny`, `Cloudy`, `Light Rain`, `Heavy Monsoon`)
- `issues_or_delays` (text, nullable)
- `status` (string, indexed: `submitted`, `reviewed`, `flagged`)
- `reviewed_by` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `reviewed_at` (timestamp, nullable)
- `review_notes` (text, nullable)
- `timestamps`

### 3.4 `employees`
- `id` (PK)
- `employee_code` (string, unique, e.g., `EMP-101`)
- `user_id` (FK -> `users.id`, nullable, unique, `nullOnDelete()`)
- `full_name` (string)
- `designation` (string)
- `phone` (string)
- `primary_project_id` (FK -> `projects.id`, nullable, `nullOnDelete()`)
- `wage_type` (string: `monthly`, `daily`)
- `base_salary_inr` (decimal 14,2)
- `overtime_rate_per_hour_inr` (decimal 14,2)
- `joining_date` (date)
- `status` (string, indexed: `active`, `inactive`)
- `timestamps`

### 3.5 `attendance_records`
- `id` (PK)
- `employee_id` (FK -> `employees.id`, `restrictOnDelete()`)
- `project_id` (FK -> `projects.id`, `restrictOnDelete()`)
- `attendance_date` (date, indexed)
- `status` (string, indexed: `present`, `half_day`, `absent`, `paid_leave`)
- `check_in` (time, nullable)
- `check_out` (time, nullable)
- `regular_hours` (decimal 5,2, default `8.00`)
- `overtime_hours` (decimal 5,2, default `0.00`)
- `overtime_approved` (boolean, default `false`)
- `overtime_approved_by` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `overtime_approved_at` (timestamp, nullable)
- `remarks` (string, nullable)
- `recorded_by` (FK -> `users.id`, `restrictOnDelete()`)
- `timestamps`
- **Unique Constraint:** `['employee_id', 'attendance_date']` (one attendance record per employee per calendar day).

### 3.6 `payroll_records`
- `id` (PK)
- `employee_id` (FK -> `employees.id`, `restrictOnDelete()`)
- `period_month` (string, indexed, validated regex `/^\d{4}-(0[1-9]|1[0-2])$/`)
- `days_present` (decimal 4,1, default `0.0`)
- `total_overtime_hours` (decimal 6,2, default `0.00`)
- `base_earned_inr` (decimal 14,2)
- `overtime_pay_inr` (decimal 14,2)
- `allowances_inr` (decimal 14,2, default `0.00`)
- `deductions_inr` (decimal 14,2, default `0.00`)
- `net_salary_inr` (decimal 14,2)
- `allowance_notes` (string, nullable)
- `deduction_notes` (string, nullable)
- `status` (string, indexed: `draft`, `approved`, `paid`)
- `approved_by` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `approved_at` (timestamp, nullable)
- `paid_at` (timestamp, nullable)
- `timestamps`
- **Unique Constraint:** `['employee_id', 'period_month']`

### 3.7 `machineries`
- `id` (PK)
- `asset_code` (string, unique, e.g., `MCH-EX-01`)
- `name` (string)
- `category` (string: `Excavator / Backhoe`, `Transit Mixer`, `Dump Truck / Tipper`, `Mobile Crane`, `Road Roller / Compactor`, `Concrete Pump`)
- `registration_number` (string, unique)
- `make_model` (string)
- `current_project_id` (FK -> `projects.id`, nullable, `nullOnDelete()`)
- `assigned_driver_id` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `operational_status` (string, indexed: `active_on_site`, `available_yard`, `under_maintenance`)
- `notes` (text, nullable)
- `timestamps`

### 3.8 `machinery_documents`
- `id` (PK)
- `machinery_id` (FK -> `machineries.id`, `restrictOnDelete()`)
- `document_type` (string: `Pollution Under Control (PUC)`, `Comprehensive Motor Insurance`, `Fitness Certificate (FC)`, `Road Tax / National Permit`)
- `document_number` (string)
- `issue_date` (date)
- `expiry_date` (date, indexed)
- `renewal_cost_estimate_inr` (decimal 14,2, nullable)
- `flagged_for_renewal` (boolean, default `false`)
- `flagged_by` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `flagged_at` (timestamp, nullable)
- `flag_notes` (string, nullable)
- `last_reminder_logged_at` (timestamp, nullable)
- `timestamps`

### 3.9 `activity_logs`
- `id` (PK)
- `user_id` (FK -> `users.id`, nullable, `nullOnDelete()`)
- `module` (string, indexed: `Projects`, `Daily Reports`, `Attendance`, `Payroll`, `Machinery`, `Compliance`, `Auth`)
- `action` (string)
- `description` (text)
- `created_at` (timestamp, indexed)

---

## 4. Exact Calculation Formulas & Global Slicing Rules

### 4.1 How Global Slice Filters Map to Each Module
When a user filters by `project_id`, `location`, or `date_from` / `date_to` / `period_month`:
- **Daily Reports:** Filters `daily_reports.project_id`, `projects.location` (via relation), and `daily_reports.report_date` between `date_from` and `date_to`.
- **Attendance:** Filters `attendance_records.project_id`, `projects.location` (via relation), and `attendance_records.attendance_date` between `date_from` and `date_to`.
- **Payroll:** Filters via `employees.primary_project_id` (and its `projects.location`), and matches `payroll_records.period_month` (format `YYYY-MM`, derived from `period_month` input or `date_from` month).
- **Machinery & Compliance:** Filters `machineries.current_project_id`, `projects.location` (via relation), and `machinery_documents.expiry_date` when date range filters are applied on the Compliance page.

### 4.2 Payroll Calculation Math (`INR`)
For a given employee and calendar month (`YYYY-MM`):
1. **Paid Days (`days_present`):**
   $$\text{days\_present} = \sum (\text{present} \times 1.0) + \sum (\text{paid\_leave} \times 1.0) + \sum (\text{half\_day} \times 0.5)$$
2. **Base Earned (`base_earned_inr`):**
   - If `wage_type === 'daily'`:
     $$\text{base\_earned\_inr} = \text{round}(\text{days\_present} \times \text{base\_salary\_inr}, 2)$$
   - If `wage_type === 'monthly'` (26-working-day civil construction norm, capped at monthly salary):
     $$\text{effective\_days} = \min(\text{days\_present}, 26)$$
     $$\text{base\_earned\_inr} = \text{round}\left(\frac{\text{base\_salary\_inr}}{26} \times \text{effective\_days}, 2\right)$$
3. **Approved Overtime Pay (`overtime_pay_inr`):**
   $$\text{total\_overtime\_hours} = \sum_{\text{overtime\_approved = true}} \text{overtime\_hours}$$
   $$\text{overtime\_pay\_inr} = \text{round}(\text{total\_overtime\_hours} \times \text{overtime\_rate\_per\_hour\_inr}, 2)$$
4. **Net Salary (`net_salary_inr`):**
   $$\text{net\_salary\_inr} = \text{base\_earned\_inr} + \text{overtime\_pay\_inr} + \text{allowances\_inr} - \text{deductions\_inr}$$
   *(Validation: `net_salary_inr` cannot be negative; validate that `deductions_inr` does not exceed gross earnings).*

### 4.3 Document Expiry Bucket Rules
Calculated dynamically against `$today = Carbon::today('Asia/Kolkata')`:
- $\text{days\_remaining} = \text{today}\rightarrow\text{diffInDays}(\text{expiry\_date}, \text{false})$
- **Expired:** $\text{days\_remaining} < 0$ (Rose / Red badge)
- **Critical (Due $\le 7$ Days):** $0 \le \text{days\_remaining} \le 7$ (Orange-Red badge)
- **Expiring Soon (Due $8\text{--}30$ Days):** $8 \le \text{days\_remaining} \le 30$ (Amber badge)
- **Valid:** $\text{days\_remaining} > 30$ (Emerald / Green badge)

---

## 5. Relative & Evergreen Seed Data Rules (`DatabaseSeeder.php`)

**CRITICAL:** Never hardcode static year/month strings like `2026-10` in seeders. All seeded dates must be computed dynamically relative to `$now = Carbon::now('Asia/Kolkata')` and `$today = Carbon::today('Asia/Kolkata')` so the demo remains evergreen in any month or year. Seeders must use `updateOrCreate` or clean truncates so running `php artisan db:seed` repeatedly does not produce duplicate key errors.

1. **6 Demo Users:** Seed the 6 exact role accounts from Section 2.1 (`is_active = true`).
2. **4 Realistic Indian Civil-Works Projects:**
   - `CW-PUN-01`: *NH-48 Six-Lane Flyover & Approach Concrete Works* (`Pune, Maharashtra`, `active`, Progress: `68%`, Budget: `45000000.00`, Manager: Vikram Patel)
   - `CW-MUM-02`: *Navi Mumbai Storm Water Drain & Box Culvert Package-3* (`Navi Mumbai, Maharashtra`, `active`, Progress: `42%`, Budget: `28500000.00`, Manager: Vikram Patel)
   - `CW-NGP-03`: *MIHAN Industrial Park Internal Cement Concrete Roads* (`Nagpur, Maharashtra`, `active`, Progress: `85%`, Budget: `19000000.00`, Manager: Rajesh Sharma)
   - `CW-NSK-04`: *Godavari Canal Retaining Wall & Earthwork Phase-2* (`Nashik, Maharashtra`, `planned`, Progress: `10%`, Budget: `12000000.00`, Manager: Vikram Patel)
3. **14 Daily Site Reports:** Distributed across `$today->copy()->subDays(9)` through `$today`, with realistic quantities (`m3` concrete pour, `MT` steel reinforcement, `sq.m` shuttering) and statuses (`reviewed`, `submitted`, `flagged`). Ensure the highest `reviewed` report percentage on each project matches its seeded `overall_progress`.
4. **10 Employees & Current-Month Attendance:**
   - 4 `monthly` employees (Site Engineers, Supervisors, Storekeeper: `INR 28,000` to `INR 48,000`) and 6 `daily` wage workers (Senior Mason, Bar Bender, Excavator Operator, Transit Mixer Driver, Helpers: `INR 700` to `INR 1,150/day`). Link `Manoj Yadav` (`driver` user) to his Employee record.
   - Seed attendance records inside the **current calendar month** (`$now->format('Y-m')`) up to `$today` (or at least 10 working days within the current month) with a mix of `overtime_approved = true` and `overtime_approved = false` rows so the Overtime Approval Queue has live items to test.
5. **10 Payroll Records for Current Month (`$now->format('Y-m')`):**
   - Calculated using the exact formula in Section 4.2: 5 in `draft`, 3 in `approved`, and 2 in `paid` status.
6. **6 Heavy Machinery Units & 18 Compliance Documents:**
   - Units: *JCB 3DX Backhoe Loader (`MH-12-EQ-4421`)*, *Ajax Fiori Argo 4000 Self-Loading Mixer (`MH-14-CM-8810`)*, *Tata Prima 2830.TK Tipper (`MH-43-AT-1920`)*, *L&T Komatsu PC210 Excavator (`MH-31-EX-5502`)*, *Escorts TRX 15T Mobile Crane (`MH-12-CR-7731`)*, *Hamm HD 99 Tandem Vibratory Roller (`MH-15-RR-3309`)*. Assign at least 2 machines to `Manoj Yadav` (`driver`).
   - Seed 3 documents per machine (`Pollution Under Control (PUC)`, `Comprehensive Motor Insurance`, `Fitness Certificate (FC)`) with `expiry_date` offsets relative to `$today`:
     - **2 Expired:** `$today->copy()->subDays(12)`, `$today->copy()->subDays(4)`
     - **3 Critical ($\le 7$ Days):** `$today->copy()->addDays(2)`, `$today->copy()->addDays(5)`, `$today->copy()->addDays(7)`
     - **4 Expiring Soon ($8\text{--}30$ Days):** `$today->copy()->addDays(11)`, `$today->copy()->addDays(16)`, `$today->copy()->addDays(22)`, `$today->copy()->addDays(28)`
     - **9 Valid ($> 30$ Days):** `$today->copy()->addDays(60)` to `$today->copy()->addDays(320)`
     - Pre-flag 2 upcoming/expired documents (`flagged_for_renewal = true`) with realistic driver notes.

---

## 6. Testable UI/UX & Mobile-First Acceptance Criteria

- **Viewport Support:** Must render cleanly without unintended horizontal page overflow from `320px` (small field phone) up to `1920px` (widescreen monitor). Tables on mobile must either wrap cleanly into stacked cards or sit inside an isolated `overflow-x-auto` container.
- **Touch Target Sizing:** All primary mobile buttons, role-switcher triggers, and quick-action links must have a minimum height of `48px` (`min-h-12`).
- **1-Screen Mobile Quick Report Logger:** When creating a Daily Report on mobile, the default Project, Today's Date (`Asia/Kolkata`), and Weather (`Clear / Sunny`) must be pre-selected so the user only needs to pick Work Category, enter Quantity & Progress %, write a 1-line summary, and tap **Submit Report**.
- **Persistent Demo Role Banner:** Display a high-contrast top banner showing:
  - Current Demo User Name & Role Badge
  - Instant 1-Click Role Switcher dropdown (`Owner/Admin`, `Project Manager`, `Site Foreman`, `HR & Payroll`, `Asset Manager`, `Driver/Operator`)
  - Direct link to `/docs` ("System Documentation & Feature Guide")

---

## 7. Single-Page In-App Documentation Specification (`GET /docs`)

Build a comprehensive, well-organized documentation page at `GET /docs` (accessible without login from `/login` and with the full sidebar when logged in). It must include:
1. **Executive Overview & Architecture:** How BuildTrack India unifies Projects, Workforce Payroll (`INR`), and Machinery Compliance in a single MySQL 8 database on Laravel 13, PHP 8.5, and Tailwind CSS 4.
2. **Interactive Role Access Matrix & Live Switcher:** Full table of all 6 roles, what each role can access, project/asset scoping rules, and embedded **"Test as this Role"** buttons (when `DEMO_MODE=true`).
3. **Module 1 Deep-Dive (Projects & Daily Site Reports):** How cumulative progress sync works (`reviewed` reports only, non-decreasing safeguard), mobile quick-logging for concrete pours, and supervisor review workflows.
4. **Module 2 Deep-Dive (Workforce, Bulk Attendance, Overtime & INR Payroll):** Exact attendance day weights, overtime approval queue rules, the 26-day monthly wage divisor formula vs. daily wage formula, draft-to-paid locking rules, and operational calculator scope note.
5. **Module 3 Deep-Dive (Machinery Register & Compliance Expiry Engine):** PUC, Insurance, and Fitness Certificate tracking, dynamic expiry buckets (`Expired`, `<= 7 Days`, `8-30 Days`, `Valid`), 1-click driver renewal flagging, and idempotent in-app reminder logging via `php artisan machinery:check-expiries`.
6. **Global Slicing & Export Guide:** How Project, Location, and Date filters map across modules, CSV formula-injection protection, and browser Print / Save-as-PDF workflows.
7. **Deployment, Scheduler & Handover Runbook:** Exact server setup commands, `.env` flags (`DEMO_MODE=false` for real operational deployment), cron configuration (`* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1`), and backup procedures.

---

## 8. Step-by-Step Execution Plan & Mandatory Feature Tests for `agy cli`

Execute these phases sequentially. Do not skip automated testing.

1. **Phase 1 — Environment & Configuration:**
   - Configure `Asia/Kolkata` timezone in `config/app.php` and add `'demo_mode' => (bool) env('DEMO_MODE', true)`.
   - Verify database connection and Tailwind CSS 4 + Vite 8 setup.
2. **Phase 2 — Migrations, Models & Services:**
   - Create migrations for all 9 tables with `DECIMAL(14,2)` and non-destructive foreign keys (`restrictOnDelete` / `nullOnDelete`).
   - Build Eloquent models with relationships, scopes, INR formatting helpers, and expiry bucket accessors.
   - Build `PayrollCalculationService`, `ProjectProgressSyncService`, and `MachineryExpiryService` (plus `machinery:check-expiries` Artisan command scheduled daily in `routes/console.php`).
3. **Phase 3 — Evergreen Seeders:**
   - Implement `DatabaseSeeder` using relative `Carbon::now('Asia/Kolkata')` dates.
   - Run `php artisan migrate:fresh --seed` and verify counts and expiry bucket distributions.
4. **Phase 4 — Auth, RBAC Middleware & Controllers:**
   - Implement `DemoAuthController` (guarded by `config('app.demo_mode')` + session regeneration + `is_active` check).
   - Implement role and project/asset scoping checks across all controllers.
5. **Phase 5 — Blade Views (10 Navigation Menus):**
   - Build the responsive layout shell, Top Demo Role Switcher, Dashboard, Projects (with tabbed detail view), Daily Reports (with mobile quick logger & review queue), Employees, Attendance (with Bulk Crew Sheet & OT Approval Queue), Payroll & Printable Payslip, Machinery Register, Compliance & Expiry Center, Exports Hub (CSV + Print views), Activity Logs, and `/docs`.
6. **Phase 6 — Build Assets & Run Mandatory Feature Tests:**
   - Run `npm run build` and verify zero build errors.
   - Create and run comprehensive Feature Tests (`php artisan test`) that explicitly verify:
     1. **Demo Auth Safety:** Role login and switching work when `DEMO_MODE=true`, regenerate session ID, reject inactive users, and return `403` when `DEMO_MODE=false`.
     2. **RBAC & Scoping:** A `foreman` or `driver` cannot access `/payroll` (`403`); a `project_manager` cannot review reports for another manager's project (`403`).
     3. **Project Progress Sync:** Submitting a `submitted` or `flagged` report leaves `projects.overall_progress` unchanged; approving (`reviewed`) updates progress deterministically and never reduces official progress from an older report.
     4. **Overtime & Payroll Locking:** Unapproved overtime (`overtime_approved = false`) is excluded from `overtime_pay_inr`; approving overtime includes it in `draft` recalculation; `approved` and `paid` payroll records are skipped and untouched when recalculating a period.
     5. **Compliance Expiry & Idempotency:** Document buckets (`expired`, `critical`, `expiring_soon`, `valid`) compute accurately; running `machinery:check-expiries` twice on the same day logs only one reminder per document for that day; driver renewal flagging works.
     6. **Exports & `/docs`:** Public `/docs` renders `200 OK`; CSV exports include UTF-8 BOM and sanitize leading `=`, `+`, `-`, `@` characters; print views render `200 OK`.
   - Perform a final regex scan across `app/`, `resources/`, `database/`, and `routes/` to guarantee **zero emojis** exist anywhere in the project.