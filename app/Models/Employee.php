<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'user_id',
        'full_name',
        'designation',
        'phone',
        'primary_project_id',
        'wage_type',
        'base_salary_inr',
        'overtime_rate_per_hour_inr',
        'joining_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'base_salary_inr' => 'decimal:2',
            'overtime_rate_per_hour_inr' => 'decimal:2',
            'joining_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function primaryProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'primary_project_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id');
    }

    public function payrollRecords(): HasMany
    {
        return $this->hasMany(PayrollRecord::class, 'employee_id');
    }

    public function getFormattedBaseSalaryAttribute(): string
    {
        return 'INR ' . number_format((float) $this->base_salary_inr, 2);
    }

    public function getFormattedOvertimeRateAttribute(): string
    {
        return 'INR ' . number_format((float) $this->overtime_rate_per_hour_inr, 2);
    }
}

