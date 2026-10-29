<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_month',
        'days_present',
        'total_overtime_hours',
        'base_earned_inr',
        'overtime_pay_inr',
        'allowances_inr',
        'deductions_inr',
        'net_salary_inr',
        'allowance_notes',
        'deduction_notes',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'days_present' => 'decimal:1',
            'total_overtime_hours' => 'decimal:2',
            'base_earned_inr' => 'decimal:2',
            'overtime_pay_inr' => 'decimal:2',
            'allowances_inr' => 'decimal:2',
            'deductions_inr' => 'decimal:2',
            'net_salary_inr' => 'decimal:2',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFormattedBaseEarnedAttribute(): string
    {
        return 'INR ' . number_format((float) $this->base_earned_inr, 2);
    }

    public function getFormattedOvertimePayAttribute(): string
    {
        return 'INR ' . number_format((float) $this->overtime_pay_inr, 2);
    }

    public function getFormattedAllowancesAttribute(): string
    {
        return 'INR ' . number_format((float) $this->allowances_inr, 2);
    }

    public function getFormattedDeductionsAttribute(): string
    {
        return 'INR ' . number_format((float) $this->deductions_inr, 2);
    }

    public function getFormattedNetSalaryAttribute(): string
    {
        return 'INR ' . number_format((float) $this->net_salary_inr, 2);
    }
}
