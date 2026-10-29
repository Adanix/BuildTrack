<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\Machinery;
use App\Models\MachineryDocument;
use App\Models\PayrollRecord;
use App\Models\Project;
use App\Models\User;
use App\Services\PayrollCalculationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Kolkata');
        $today = Carbon::today('Asia/Kolkata');
        $currentMonth = $now->format('Y-m');

        // Disable foreign keys for clean repeatable seeding
        Schema::disableForeignKeyConstraints();
        ActivityLog::truncate();
        MachineryDocument::truncate();
        Machinery::truncate();
        PayrollRecord::truncate();
        AttendanceRecord::truncate();
        Employee::truncate();
        DailyReport::truncate();
        Project::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Seed 6 Demo Users
        $usersData = [
            [
                'email' => 'admin@buildtrack.in',
                'name' => 'Rajesh Sharma',
                'role' => 'admin',
                'phone' => '+91 98201 11223',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'email' => 'pm@buildtrack.in',
                'name' => 'Vikram Patel',
                'role' => 'project_manager',
                'phone' => '+91 98202 22334',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'email' => 'foreman@buildtrack.in',
                'name' => 'Suresh Nair',
                'role' => 'foreman',
                'phone' => '+91 98203 33445',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'email' => 'hr@buildtrack.in',
                'name' => 'Priya Menon',
                'role' => 'hr_payroll',
                'phone' => '+91 98204 44556',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'email' => 'assets@buildtrack.in',
                'name' => 'Arun Deshmukh',
                'role' => 'asset_manager',
                'phone' => '+91 98205 55667',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'email' => 'driver@buildtrack.in',
                'name' => 'Manoj Yadav',
                'role' => 'driver',
                'phone' => '+91 98206 66778',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        ];

        $users = [];
        foreach ($usersData as $ud) {
            $users[$ud['role']] = User::create($ud);
        }

        // 2. Seed 4 Projects
        $projectsData = [
            [
                'code' => 'CW-PUN-01',
                'name' => 'NH-48 Six-Lane Flyover & Approach Concrete Works',
                'client_name' => 'National Highways Authority of India (NHAI)',
                'location' => 'Pune, Maharashtra',
                'manager_id' => $users['project_manager']->id,
                'budget_inr' => 45000000.00,
                'start_date' => $today->copy()->subMonths(8)->toDateString(),
                'target_end_date' => $today->copy()->addMonths(6)->toDateString(),
                'overall_progress' => 68,
                'status' => 'active',
                'description' => 'Construction of 2.4 km elevated flyover section with prestressed concrete girders and high-grade RCC pier caps.',
            ],
            [
                'code' => 'CW-MUM-02',
                'name' => 'Navi Mumbai Storm Water Drain & Box Culvert Package-3',
                'client_name' => 'CIDCO Maharashtra',
                'location' => 'Navi Mumbai, Maharashtra',
                'manager_id' => $users['project_manager']->id,
                'budget_inr' => 28500000.00,
                'start_date' => $today->copy()->subMonths(5)->toDateString(),
                'target_end_date' => $today->copy()->addMonths(5)->toDateString(),
                'overall_progress' => 42,
                'status' => 'active',
                'description' => 'Precast and cast-in-situ RCC box culverts with heavy monsoon channel discharge capacity along coastal highway.',
            ],
            [
                'code' => 'CW-NGP-03',
                'name' => 'MIHAN Industrial Park Internal Cement Concrete Roads',
                'client_name' => 'Maharashtra Airport Development Company',
                'location' => 'Nagpur, Maharashtra',
                'manager_id' => $users['admin']->id,
                'budget_inr' => 19000000.00,
                'start_date' => $today->copy()->subMonths(9)->toDateString(),
                'target_end_date' => $today->copy()->addMonths(2)->toDateString(),
                'overall_progress' => 85,
                'status' => 'active',
                'description' => 'Heavy-duty PQC (Pavement Quality Concrete) paving with DLC sub-base and drainage channels for logistics hub.',
            ],
            [
                'code' => 'CW-NSK-04',
                'name' => 'Godavari Canal Retaining Wall & Earthwork Phase-2',
                'client_name' => 'Maharashtra Water Resources Department',
                'location' => 'Nashik, Maharashtra',
                'manager_id' => $users['project_manager']->id,
                'budget_inr' => 12000000.00,
                'start_date' => $today->copy()->subMonth()->toDateString(),
                'target_end_date' => $today->copy()->addMonths(8)->toDateString(),
                'overall_progress' => 10,
                'status' => 'planned',
                'description' => 'Slope stabilization, gravity retaining walls, and rock-fill embankment earthworks along main canal embankment.',
            ],
        ];

        $projects = [];
        foreach ($projectsData as $pd) {
            $projects[$pd['code']] = Project::create($pd);
        }

        // 3. Seed 14 Daily Site Reports
        // Highest reviewed report progress on each project matches seeded overall_progress:
        // CW-PUN-01: 68%
        // CW-MUM-02: 42%
        // CW-NGP-03: 85%
        // CW-NSK-04: 10%
        $dailyReportsData = [
            // CW-PUN-01
            [
                'project_id' => $projects['CW-PUN-01']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(8)->toDateString(),
                'work_category' => 'Concrete Pouring (RCC/PCC)',
                'activity_summary' => 'Pier Cap P-14 M45 concrete pour completed using boom pump with two transit mixers in continuous cycle.',
                'quantity_completed' => 48.50,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 64,
                'labor_count' => 24,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(8)->setTime(18, 30),
                'review_notes' => 'Cube samples taken as per IS 456 specification. Slump test recorded 120mm.',
            ],
            [
                'project_id' => $projects['CW-PUN-01']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(5)->toDateString(),
                'work_category' => 'Reinforcement & Shuttering',
                'activity_summary' => 'High-yield Fe500D rebar tying and plywood formwork shuttering for Pier Cap P-15.',
                'quantity_completed' => 14.20,
                'quantity_unit' => 'MT',
                'reported_progress_percent' => 66,
                'labor_count' => 28,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(5)->setTime(19, 0),
                'review_notes' => 'Bar bending schedule verified and cover blocks inspected.',
            ],
            [
                'project_id' => $projects['CW-PUN-01']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(2)->toDateString(),
                'work_category' => 'Concrete Pouring (RCC/PCC)',
                'activity_summary' => 'Deck slab span 8 casting using micro-silica enriched M45 mix.',
                'quantity_completed' => 92.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 68,
                'labor_count' => 32,
                'weather' => 'Cloudy',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(2)->setTime(20, 15),
                'review_notes' => 'Pour completed cleanly. Curing burlap sheets laid immediately.',
            ],
            [
                'project_id' => $projects['CW-PUN-01']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDay()->toDateString(),
                'work_category' => 'Earthwork & Excavation',
                'activity_summary' => 'Approach ramp embankment compaction and backfilling behind abutment A1.',
                'quantity_completed' => 180.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 69,
                'labor_count' => 16,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => 'Dump truck transit slowed due to highway toll booth congestion.',
                'status' => 'submitted',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],
            [
                'project_id' => $projects['CW-PUN-01']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->toDateString(),
                'work_category' => 'Concrete Pouring (RCC/PCC)',
                'activity_summary' => 'Crash barrier footing casting on left carriage approach.',
                'quantity_completed' => 22.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 69,
                'labor_count' => 18,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'submitted',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],

            // CW-MUM-02
            [
                'project_id' => $projects['CW-MUM-02']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(9)->toDateString(),
                'work_category' => 'Earthwork & Excavation',
                'activity_summary' => 'Trench excavation in saturated soil for storm water box culvert section C-4.',
                'quantity_completed' => 240.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 38,
                'labor_count' => 20,
                'weather' => 'Cloudy',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(9)->setTime(18, 0),
                'review_notes' => 'Dewatering pumps deployed effectively.',
            ],
            [
                'project_id' => $projects['CW-MUM-02']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(6)->toDateString(),
                'work_category' => 'Concrete Pouring (RCC/PCC)',
                'activity_summary' => 'Bottom base raft concrete M30 pour for 3-cell box culvert.',
                'quantity_completed' => 65.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 40,
                'labor_count' => 22,
                'weather' => 'Light Rain',
                'issues_or_delays' => 'Light drizzle delayed transit mixer cycle by 35 minutes.',
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(6)->setTime(19, 30),
                'review_notes' => 'Waterproof plastic sheeting used over fresh concrete.',
            ],
            [
                'project_id' => $projects['CW-MUM-02']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(3)->toDateString(),
                'work_category' => 'Reinforcement & Shuttering',
                'activity_summary' => 'Box culvert vertical side walls rebar placement and modular steel shuttering.',
                'quantity_completed' => 18.50,
                'quantity_unit' => 'MT',
                'reported_progress_percent' => 42,
                'labor_count' => 25,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(3)->setTime(18, 45),
                'review_notes' => 'Tie rod spacings approved.',
            ],
            [
                'project_id' => $projects['CW-MUM-02']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDay()->toDateString(),
                'work_category' => 'Concrete Pouring (RCC/PCC)',
                'activity_summary' => 'Wall casting pour segment-2 using self-compacting concrete.',
                'quantity_completed' => 45.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 44,
                'labor_count' => 26,
                'weather' => 'Heavy Monsoon',
                'issues_or_delays' => 'Heavy downpour flooded lower trench sump; safety stand-down for 2 hours.',
                'status' => 'flagged',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDay()->setTime(21, 0),
                'review_notes' => 'Flagged due to water ingress risk during second batching. Core test required before accepting progress.',
            ],

            // CW-NGP-03
            [
                'project_id' => $projects['CW-NGP-03']->id,
                'reported_by' => $users['admin']->id,
                'report_date' => $today->copy()->subDays(7)->toDateString(),
                'work_category' => 'Road Base & Bitumen',
                'activity_summary' => 'Dry Lean Concrete (DLC) sub-base compaction of 350 meters stretch.',
                'quantity_completed' => 700.00,
                'quantity_unit' => 'sq.m',
                'reported_progress_percent' => 82,
                'labor_count' => 18,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['admin']->id,
                'reviewed_at' => $today->copy()->subDays(7)->setTime(17, 30),
                'review_notes' => 'Compaction density achieved 98.4%.',
            ],
            [
                'project_id' => $projects['CW-NGP-03']->id,
                'reported_by' => $users['admin']->id,
                'report_date' => $today->copy()->subDays(4)->toDateString(),
                'work_category' => 'Concrete Pouring (RCC/PCC)',
                'activity_summary' => 'PQC (Pavement Quality Concrete) paving with slipform paver and contraction groove cutting.',
                'quantity_completed' => 1250.00,
                'quantity_unit' => 'sq.m',
                'reported_progress_percent' => 85,
                'labor_count' => 22,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['admin']->id,
                'reviewed_at' => $today->copy()->subDays(4)->setTime(19, 0),
                'review_notes' => 'Surface texture and dowel bar alignment satisfactory.',
            ],
            [
                'project_id' => $projects['CW-NGP-03']->id,
                'reported_by' => $users['admin']->id,
                'report_date' => $today->copy()->subDay()->toDateString(),
                'work_category' => 'Masonry & Finishing',
                'activity_summary' => 'Precast roadside kerb stone installation and solar stud fixing.',
                'quantity_completed' => 280.00,
                'quantity_unit' => 'RMT',
                'reported_progress_percent' => 86,
                'labor_count' => 14,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'submitted',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ],

            // CW-NSK-04
            [
                'project_id' => $projects['CW-NSK-04']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(7)->toDateString(),
                'work_category' => 'Site Clearance',
                'activity_summary' => 'Vegetation clearing, topsoil stripping, and boundary benchmark surveying along canal reach.',
                'quantity_completed' => 1200.00,
                'quantity_unit' => 'sq.m',
                'reported_progress_percent' => 6,
                'labor_count' => 12,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(7)->setTime(18, 0),
                'review_notes' => 'Boundary markers confirmed with irrigation department survey.',
            ],
            [
                'project_id' => $projects['CW-NSK-04']->id,
                'reported_by' => $users['foreman']->id,
                'report_date' => $today->copy()->subDays(3)->toDateString(),
                'work_category' => 'Earthwork & Excavation',
                'activity_summary' => 'Hard rock excavation using hydraulic breaker attachment for retaining wall footing toe.',
                'quantity_completed' => 85.00,
                'quantity_unit' => 'm3',
                'reported_progress_percent' => 10,
                'labor_count' => 16,
                'weather' => 'Clear / Sunny',
                'issues_or_delays' => null,
                'status' => 'reviewed',
                'reviewed_by' => $users['project_manager']->id,
                'reviewed_at' => $today->copy()->subDays(3)->setTime(19, 15),
                'review_notes' => 'Solid rock bed reached at 2.8m depth.',
            ],
        ];

        foreach ($dailyReportsData as $dr) {
            DailyReport::create($dr);
        }

        // 4. Seed 10 Employees
        // 4 Monthly employees + 6 Daily wage workers
        // Link Manoj Yadav (driver user) to his Employee record
        $employeesData = [
            // Monthly Staff
            [
                'employee_code' => 'EMP-101',
                'user_id' => null,
                'full_name' => 'Anil Kulkarni',
                'designation' => 'Senior Site Engineer',
                'phone' => '+91 97110 10001',
                'primary_project_id' => $projects['CW-PUN-01']->id,
                'wage_type' => 'monthly',
                'base_salary_inr' => 48000.00,
                'overtime_rate_per_hour_inr' => 280.00,
                'joining_date' => $today->copy()->subMonths(18)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-102',
                'user_id' => null,
                'full_name' => 'Sachin Jadhav',
                'designation' => 'Quality & Safety Supervisor',
                'phone' => '+91 97110 10002',
                'primary_project_id' => $projects['CW-PUN-01']->id,
                'wage_type' => 'monthly',
                'base_salary_inr' => 38000.00,
                'overtime_rate_per_hour_inr' => 220.00,
                'joining_date' => $today->copy()->subMonths(14)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-103',
                'user_id' => null,
                'full_name' => 'Pradeep Shinde',
                'designation' => 'Junior Billing Engineer',
                'phone' => '+91 97110 10003',
                'primary_project_id' => $projects['CW-MUM-02']->id,
                'wage_type' => 'monthly',
                'base_salary_inr' => 32000.00,
                'overtime_rate_per_hour_inr' => 190.00,
                'joining_date' => $today->copy()->subMonths(8)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-104',
                'user_id' => null,
                'full_name' => 'Ganesh Gaikwad',
                'designation' => 'Storekeeper & Logistics In-Charge',
                'phone' => '+91 97110 10004',
                'primary_project_id' => $projects['CW-NGP-03']->id,
                'wage_type' => 'monthly',
                'base_salary_inr' => 28000.00,
                'overtime_rate_per_hour_inr' => 160.00,
                'joining_date' => $today->copy()->subMonths(11)->toDateString(),
                'status' => 'active',
            ],

            // Daily Wage Workers
            [
                'employee_code' => 'EMP-201',
                'user_id' => $users['driver']->id, // Manoj Yadav linked here
                'full_name' => 'Manoj Yadav',
                'designation' => 'Heavy Excavator & Machinery Operator',
                'phone' => '+91 98206 66778',
                'primary_project_id' => $projects['CW-PUN-01']->id,
                'wage_type' => 'daily',
                'base_salary_inr' => 1150.00,
                'overtime_rate_per_hour_inr' => 180.00,
                'joining_date' => $today->copy()->subMonths(20)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-202',
                'user_id' => null,
                'full_name' => 'Rameshwar Chavan',
                'designation' => 'Lead Mason (Concrete & Stone Works)',
                'phone' => '+91 97110 10006',
                'primary_project_id' => $projects['CW-PUN-01']->id,
                'wage_type' => 'daily',
                'base_salary_inr' => 950.00,
                'overtime_rate_per_hour_inr' => 150.00,
                'joining_date' => $today->copy()->subMonths(15)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-203',
                'user_id' => null,
                'full_name' => 'Deepak Patil',
                'designation' => 'Master Bar Bender & Shuttering Carpenter',
                'phone' => '+91 97110 10007',
                'primary_project_id' => $projects['CW-MUM-02']->id,
                'wage_type' => 'daily',
                'base_salary_inr' => 900.00,
                'overtime_rate_per_hour_inr' => 140.00,
                'joining_date' => $today->copy()->subMonths(10)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-204',
                'user_id' => null,
                'full_name' => 'Vijay Kamble',
                'designation' => 'Transit Mixer Operator',
                'phone' => '+91 97110 10008',
                'primary_project_id' => $projects['CW-PUN-01']->id,
                'wage_type' => 'daily',
                'base_salary_inr' => 1050.00,
                'overtime_rate_per_hour_inr' => 165.00,
                'joining_date' => $today->copy()->subMonths(7)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-205',
                'user_id' => null,
                'full_name' => 'Sunil Rathod',
                'designation' => 'General Construction Labor / Helper',
                'phone' => '+91 97110 10009',
                'primary_project_id' => $projects['CW-NGP-03']->id,
                'wage_type' => 'daily',
                'base_salary_inr' => 700.00,
                'overtime_rate_per_hour_inr' => 110.00,
                'joining_date' => $today->copy()->subMonths(6)->toDateString(),
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP-206',
                'user_id' => null,
                'full_name' => 'Dnyaneshwar More',
                'designation' => 'Compactor & Roller Operator',
                'phone' => '+91 97110 10010',
                'primary_project_id' => $projects['CW-NSK-04']->id,
                'wage_type' => 'daily',
                'base_salary_inr' => 850.00,
                'overtime_rate_per_hour_inr' => 135.00,
                'joining_date' => $today->copy()->subMonths(4)->toDateString(),
                'status' => 'active',
            ],
        ];

        $employees = [];
        foreach ($employeesData as $ed) {
            $employees[] = Employee::create($ed);
        }

        // 5. Seed Attendance Records for Current Month
        // Generate attendance for the last 10 working days up to today
        $attendanceDays = [];
        for ($i = 9; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            // Skip Sundays for realistic civil works schedule
            if ($day->dayOfWeek !== Carbon::SUNDAY) {
                $attendanceDays[] = $day;
            }
        }
        if (count($attendanceDays) < 8) {
            // Ensure at least 8-10 days
            for ($i = 10; $i <= 14; $i++) {
                $day = $today->copy()->subDays($i);
                if ($day->dayOfWeek !== Carbon::SUNDAY && count($attendanceDays) < 10) {
                    $attendanceDays[] = $day;
                }
            }
            sort($attendanceDays);
        }

        // Mix of present, half_day, paid_leave, and overtime (both approved and pending)
        foreach ($employees as $idx => $emp) {
            foreach ($attendanceDays as $dayIdx => $dateObj) {
                $dateStr = $dateObj->toDateString();
                $projectId = $emp->primary_project_id ?? $projects['CW-PUN-01']->id;

                // Deterministic variation pattern based on employee index and day index
                $mod = ($idx * 3 + $dayIdx) % 10;
                $status = 'present';
                $regHours = 8.00;
                $otHours = 0.00;
                $otApproved = false;
                $checkIn = '08:00:00';
                $checkOut = '17:00:00';

                if ($mod === 0) {
                    $status = 'half_day';
                    $regHours = 4.00;
                    $checkOut = '12:30:00';
                } elseif ($mod === 1 && $emp->wage_type === 'monthly') {
                    $status = 'paid_leave';
                    $regHours = 0.00;
                    $checkIn = null;
                    $checkOut = null;
                } elseif ($mod === 2) {
                    // Overtime pending approval
                    $otHours = 2.00;
                    $otApproved = false;
                    $checkOut = '19:30:00';
                } elseif ($mod === 3 || $mod === 4) {
                    // Overtime approved
                    $otHours = 3.00;
                    $otApproved = true;
                    $checkOut = '20:30:00';
                }

                AttendanceRecord::create([
                    'employee_id' => $emp->id,
                    'project_id' => $projectId,
                    'attendance_date' => $dateStr,
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'regular_hours' => $regHours,
                    'overtime_hours' => $otHours,
                    'overtime_approved' => $otApproved,
                    'overtime_approved_by' => $otApproved ? $users['project_manager']->id : null,
                    'overtime_approved_at' => $otApproved ? $dateObj->copy()->setTime(21, 0) : null,
                    'remarks' => $otHours > 0 ? ($otApproved ? 'Overtime approved by PM for urgent pour.' : 'Overtime recorded by foreman, pending approval.') : null,
                    'recorded_by' => $users['foreman']->id,
                ]);
            }
        }

        // 6. Seed 10 Payroll Records for Current Month ($currentMonth)
        // Calculated using the exact formula: 5 draft, 3 approved, 2 paid
        $calcService = app(PayrollCalculationService::class);

        // Calculate initial metrics for all 10 employees
        foreach ($employees as $idx => $emp) {
            $allowances = ($idx % 3 === 0) ? 1500.00 : (($idx % 2 === 0) ? 800.00 : 0.00);
            $deductions = ($idx % 4 === 0) ? 500.00 : 0.00;

            $metrics = $calcService->calculateForEmployee($emp, $currentMonth, $allowances, $deductions);

            $status = 'draft';
            $approvedBy = null;
            $approvedAt = null;
            $paidAt = null;

            if ($idx >= 5 && $idx <= 7) {
                // 3 Approved
                $status = 'approved';
                $approvedBy = $users['hr_payroll']->id;
                $approvedAt = $today->copy()->subDays(2)->setTime(16, 0);
            } elseif ($idx >= 8) {
                // 2 Paid
                $status = 'paid';
                $approvedBy = $users['hr_payroll']->id;
                $approvedAt = $today->copy()->subDays(3)->setTime(14, 0);
                $paidAt = $today->copy()->subDays(1)->setTime(11, 30);
            }

            PayrollRecord::create([
                'employee_id' => $emp->id,
                'period_month' => $currentMonth,
                'days_present' => $metrics['days_present'],
                'total_overtime_hours' => $metrics['total_overtime_hours'],
                'base_earned_inr' => $metrics['base_earned_inr'],
                'overtime_pay_inr' => $metrics['overtime_pay_inr'],
                'allowances_inr' => $metrics['allowances_inr'],
                'deductions_inr' => $metrics['deductions_inr'],
                'net_salary_inr' => $metrics['net_salary_inr'],
                'allowance_notes' => $allowances > 0 ? 'Site travel & safety footwear allowance' : null,
                'deduction_notes' => $deductions > 0 ? 'Uniform & helmet replacement deduction' : null,
                'status' => $status,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'paid_at' => $paidAt,
            ]);
        }

        // 7. Seed 6 Heavy Machinery Units & 18 Compliance Documents
        // Units:
        // 1. JCB 3DX Backhoe Loader (MH-12-EQ-4421)
        // 2. Ajax Fiori Argo 4000 Self-Loading Mixer (MH-14-CM-8810)
        // 3. Tata Prima 2830.TK Tipper (MH-43-AT-1920)
        // 4. L&T Komatsu PC210 Excavator (MH-31-EX-5502)
        // 5. Escorts TRX 15T Mobile Crane (MH-12-CR-7731)
        // 6. Hamm HD 99 Tandem Vibratory Roller (MH-15-RR-3309)
        // Assign at least 2 machines to Manoj Yadav (driver user)
        $machineriesData = [
            [
                'asset_code' => 'MCH-EX-01',
                'name' => 'JCB 3DX Backhoe Loader',
                'category' => 'Excavator / Backhoe',
                'registration_number' => 'MH-12-EQ-4421',
                'make_model' => 'JCB India 3DX EcoXcellence 2023',
                'current_project_id' => $projects['CW-PUN-01']->id,
                'assigned_driver_id' => $users['driver']->id, // Assigned to Manoj Yadav
                'operational_status' => 'active_on_site',
                'notes' => 'Primary excavation and trenching backhoe at Pier P-14.',
            ],
            [
                'asset_code' => 'MCH-MX-02',
                'name' => 'Ajax Fiori Argo 4000 Self-Loading Mixer',
                'category' => 'Transit Mixer',
                'registration_number' => 'MH-14-CM-8810',
                'make_model' => 'Ajax Engineering Argo 4000 (4.0 m3)',
                'current_project_id' => $projects['CW-PUN-01']->id,
                'assigned_driver_id' => null,
                'operational_status' => 'active_on_site',
                'notes' => 'Dedicated concrete batching transit unit for approach ramp works.',
            ],
            [
                'asset_code' => 'MCH-TP-03',
                'name' => 'Tata Prima 2830.TK Tipper',
                'category' => 'Dump Truck / Tipper',
                'registration_number' => 'MH-43-AT-1920',
                'make_model' => 'Tata Motors Prima 2830 Heavy Tipper 16m3',
                'current_project_id' => $projects['CW-MUM-02']->id,
                'assigned_driver_id' => null,
                'operational_status' => 'active_on_site',
                'notes' => 'Muck haulage and aggregate transfer for storm water culverts.',
            ],
            [
                'asset_code' => 'MCH-EX-04',
                'name' => 'L&T Komatsu PC210 Excavator',
                'category' => 'Excavator / Backhoe',
                'registration_number' => 'MH-31-EX-5502',
                'make_model' => 'L&T Construction Equipment PC210-10M0',
                'current_project_id' => $projects['CW-NGP-03']->id,
                'assigned_driver_id' => $users['driver']->id, // Also assigned to Manoj Yadav
                'operational_status' => 'active_on_site',
                'notes' => 'Equipped with heavy rock bucket and breaker line connection.',
            ],
            [
                'asset_code' => 'MCH-CR-05',
                'name' => 'Escorts TRX 15T Mobile Crane',
                'category' => 'Mobile Crane',
                'registration_number' => 'MH-12-CR-7731',
                'make_model' => 'Escorts Construction Equipment TRX 15T Pick & Carry',
                'current_project_id' => $projects['CW-NSK-04']->id,
                'assigned_driver_id' => null,
                'operational_status' => 'available_yard',
                'notes' => 'Stored in Nashik central equipment yard, ready for canal girder placement.',
            ],
            [
                'asset_code' => 'MCH-RL-06',
                'name' => 'Hamm HD 99 Tandem Vibratory Roller',
                'category' => 'Road Roller / Compactor',
                'registration_number' => 'MH-15-RR-3309',
                'make_model' => 'Wirtgen Group Hamm HD 99 (9 Tonne)',
                'current_project_id' => null,
                'assigned_driver_id' => null,
                'operational_status' => 'under_maintenance',
                'notes' => 'Hydraulic drive pump service scheduled at authorized dealer.',
            ],
        ];

        $machineries = [];
        foreach ($machineriesData as $md) {
            $machineries[] = Machinery::create($md);
        }

        // 18 Compliance Documents
        // Requirement from Section 5.6:
        // 2 Expired: $today->copy()->subDays(12), $today->copy()->subDays(4)
        // 3 Critical (<= 7 Days): $today->copy()->addDays(2), $today->copy()->addDays(5), $today->copy()->addDays(7)
        // 4 Expiring Soon (8-30 Days): $today->copy()->addDays(11), $today->copy()->addDays(16), $today->copy()->addDays(22), $today->copy()->addDays(28)
        // 9 Valid (> 30 Days): $today->copy()->addDays(60) to $today->copy()->addDays(320)
        // Pre-flag 2 upcoming/expired documents (flagged_for_renewal = true) with realistic driver notes
        $docTypes = [
            'Pollution Under Control (PUC)',
            'Comprehensive Motor Insurance',
            'Fitness Certificate (FC)',
        ];

        // Specific distribution across the 6 machines (3 docs each = 18 docs):
        $docPlans = [
            // Machine 0 (JCB 3DX - assigned to Manoj Yadav)
            [
                'type' => 'Pollution Under Control (PUC)',
                'expiry' => $today->copy()->subDays(4), // Expired
                'cost' => 450.00,
                'flagged' => true, // Flagged by driver
                'flag_notes' => 'PUC testing machine at local pump was offline; requires urgent renewal certificate.',
            ],
            [
                'type' => 'Comprehensive Motor Insurance',
                'expiry' => $today->copy()->addDays(5), // Critical <= 7 days
                'cost' => 38000.00,
                'flagged' => true, // Flagged
                'flag_notes' => 'Policy renewal quote received from ICICI Lombard; awaiting approval.',
            ],
            [
                'type' => 'Fitness Certificate (FC)',
                'expiry' => $today->copy()->addDays(120), // Valid
                'cost' => 12000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],

            // Machine 1 (Ajax Fiori)
            [
                'type' => 'Pollution Under Control (PUC)',
                'expiry' => $today->copy()->subDays(12), // Expired
                'cost' => 450.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Comprehensive Motor Insurance',
                'expiry' => $today->copy()->addDays(2), // Critical <= 7 days
                'cost' => 42500.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Fitness Certificate (FC)',
                'expiry' => $today->copy()->addDays(11), // Expiring soon 8-30
                'cost' => 14000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],

            // Machine 2 (Tata Prima Tipper)
            [
                'type' => 'Pollution Under Control (PUC)',
                'expiry' => $today->copy()->addDays(7), // Critical <= 7 days
                'cost' => 500.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Comprehensive Motor Insurance',
                'expiry' => $today->copy()->addDays(16), // Expiring soon 8-30
                'cost' => 52000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Fitness Certificate (FC)',
                'expiry' => $today->copy()->addDays(180), // Valid
                'cost' => 15000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],

            // Machine 3 (L&T Komatsu - assigned to Manoj Yadav)
            [
                'type' => 'Pollution Under Control (PUC)',
                'expiry' => $today->copy()->addDays(22), // Expiring soon 8-30
                'cost' => 600.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Comprehensive Motor Insurance',
                'expiry' => $today->copy()->addDays(28), // Expiring soon 8-30
                'cost' => 65000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Fitness Certificate (FC)',
                'expiry' => $today->copy()->addDays(90), // Valid
                'cost' => 18000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],

            // Machine 4 (Escorts Crane)
            [
                'type' => 'Pollution Under Control (PUC)',
                'expiry' => $today->copy()->addDays(75), // Valid
                'cost' => 450.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Comprehensive Motor Insurance',
                'expiry' => $today->copy()->addDays(150), // Valid
                'cost' => 31000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Fitness Certificate (FC)',
                'expiry' => $today->copy()->addDays(210), // Valid
                'cost' => 11000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],

            // Machine 5 (Hamm Roller)
            [
                'type' => 'Pollution Under Control (PUC)',
                'expiry' => $today->copy()->addDays(130), // Valid
                'cost' => 450.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Comprehensive Motor Insurance',
                'expiry' => $today->copy()->addDays(280), // Valid
                'cost' => 29000.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
            [
                'type' => 'Fitness Certificate (FC)',
                'expiry' => $today->copy()->addDays(320), // Valid
                'cost' => 13500.00,
                'flagged' => false,
                'flag_notes' => null,
            ],
        ];

        foreach ($docPlans as $i => $plan) {
            $machineIndex = (int) floor($i / 3);
            $machinery = $machineries[$machineIndex];
            $docNumPrefix = match ($plan['type']) {
                'Pollution Under Control (PUC)' => 'PUC-MH-',
                'Comprehensive Motor Insurance' => 'INS-POL-',
                default => 'FC-RTO-',
            };

            MachineryDocument::create([
                'machinery_id' => $machinery->id,
                'document_type' => $plan['type'],
                'document_number' => $docNumPrefix . (10000 + $i * 123),
                'issue_date' => $plan['expiry']->copy()->subYear()->toDateString(),
                'expiry_date' => $plan['expiry']->toDateString(),
                'renewal_cost_estimate_inr' => $plan['cost'],
                'flagged_for_renewal' => $plan['flagged'],
                'flagged_by' => $plan['flagged'] ? $users['driver']->id : null,
                'flagged_at' => $plan['flagged'] ? $today->copy()->subDays(1)->setTime(10, 0) : null,
                'flag_notes' => $plan['flag_notes'],
                'last_reminder_logged_at' => null,
            ]);
        }

        // 8. Seed Initial Activity Logs
        ActivityLog::log(
            'Auth',
            'System Initialized',
            'BuildTrack India Civil Works Management system booted in DEMO_MODE with 6 standard operational roles.',
            $users['admin']->id
        );
        ActivityLog::log(
            'Projects',
            'Project Registered',
            "Registered NH-48 Six-Lane Flyover & Approach Concrete Works (CW-PUN-01) with budget INR 45,000,000.00.",
            $users['admin']->id
        );
        ActivityLog::log(
            'Payroll',
            'Payroll Batch Calculated',
            "Generated draft payroll records for period {$currentMonth} across all active workforce members.",
            $users['hr_payroll']->id
        );
        ActivityLog::log(
            'Compliance',
            'Document Renewal Flagged',
            "Driver Manoj Yadav flagged Pollution Under Control (PUC) certificate for renewal on JCB 3DX Backhoe Loader (MH-12-EQ-4421).",
            $users['driver']->id
        );
    }
}
