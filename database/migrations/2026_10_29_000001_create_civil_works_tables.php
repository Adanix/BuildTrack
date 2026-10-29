<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('client_name');
            $table->string('location')->index();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('budget_inr', 14, 2)->default(0.00);
            $table->date('start_date');
            $table->date('target_end_date');
            $table->unsignedTinyInteger('overall_progress')->default(0);
            $table->string('status')->default('planned')->index(); // planned, active, on_hold, completed
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Daily Reports
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('users')->restrictOnDelete();
            $table->date('report_date')->index();
            $table->string('work_category'); // Concrete Pouring (RCC/PCC), Earthwork & Excavation, etc.
            $table->text('activity_summary');
            $table->decimal('quantity_completed', 10, 2)->nullable();
            $table->string('quantity_unit')->nullable(); // m3, sq.m, MT, RMT, Nos
            $table->unsignedTinyInteger('reported_progress_percent')->default(0);
            $table->unsignedInteger('labor_count')->default(0);
            $table->string('weather')->default('Clear / Sunny'); // Clear / Sunny, Cloudy, Light Rain, Heavy Monsoon
            $table->text('issues_or_delays')->nullable();
            $table->string('status')->default('submitted')->index(); // submitted, reviewed, flagged
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });

        // 3. Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('designation');
            $table->string('phone');
            $table->foreignId('primary_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('wage_type'); // monthly, daily
            $table->decimal('base_salary_inr', 14, 2);
            $table->decimal('overtime_rate_per_hour_inr', 14, 2);
            $table->date('joining_date');
            $table->string('status')->default('active')->index(); // active, inactive
            $table->timestamps();
        });

        // 4. Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->date('attendance_date')->index();
            $table->string('status')->default('present')->index(); // present, half_day, absent, paid_leave
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('regular_hours', 5, 2)->default(8.00);
            $table->decimal('overtime_hours', 5, 2)->default(0.00);
            $table->boolean('overtime_approved')->default(false);
            $table->foreignId('overtime_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('overtime_approved_at')->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
        });

        // 5. Payroll Records
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('period_month', 7)->index(); // YYYY-MM
            $table->decimal('days_present', 4, 1)->default(0.0);
            $table->decimal('total_overtime_hours', 6, 2)->default(0.00);
            $table->decimal('base_earned_inr', 14, 2)->default(0.00);
            $table->decimal('overtime_pay_inr', 14, 2)->default(0.00);
            $table->decimal('allowances_inr', 14, 2)->default(0.00);
            $table->decimal('deductions_inr', 14, 2)->default(0.00);
            $table->decimal('net_salary_inr', 14, 2)->default(0.00);
            $table->string('allowance_notes')->nullable();
            $table->string('deduction_notes')->nullable();
            $table->string('status')->default('draft')->index(); // draft, approved, paid
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period_month']);
        });

        // 6. Machineries
        Schema::create('machineries', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category'); // Excavator / Backhoe, Transit Mixer, etc.
            $table->string('registration_number')->unique();
            $table->string('make_model');
            $table->foreignId('current_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('operational_status')->default('available_yard')->index(); // active_on_site, available_yard, under_maintenance
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Machinery Documents
        Schema::create('machinery_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machinery_id')->constrained('machineries')->restrictOnDelete();
            $table->string('document_type'); // Pollution Under Control (PUC), Comprehensive Motor Insurance, etc.
            $table->string('document_number');
            $table->date('issue_date');
            $table->date('expiry_date')->index();
            $table->decimal('renewal_cost_estimate_inr', 14, 2)->nullable();
            $table->boolean('flagged_for_renewal')->default(false);
            $table->foreignId('flagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('flagged_at')->nullable();
            $table->string('flag_notes')->nullable();
            $table->timestamp('last_reminder_logged_at')->nullable();
            $table->timestamps();
        });

        // 8. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module')->index(); // Projects, Daily Reports, Attendance, Payroll, Machinery, Compliance, Auth
            $table->string('action');
            $table->text('description');
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('machinery_documents');
        Schema::dropIfExists('machineries');
        Schema::dropIfExists('payroll_records');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('projects');
    }
};

