<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'client_name',
        'location',
        'manager_id',
        'budget_inr',
        'start_date',
        'target_end_date',
        'overall_progress',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'budget_inr' => 'decimal:2',
            'start_date' => 'date',
            'target_end_date' => 'date',
            'overall_progress' => 'integer',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'project_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'primary_project_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'project_id');
    }

    public function machineries(): HasMany
    {
        return $this->hasMany(Machinery::class, 'current_project_id');
    }

    public function getFormattedBudgetAttribute(): string
    {
        return 'INR ' . number_format((float) $this->budget_inr, 2);
    }
}
