<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'reported_by',
        'report_date',
        'work_category',
        'activity_summary',
        'quantity_completed',
        'quantity_unit',
        'reported_progress_percent',
        'labor_count',
        'weather',
        'issues_or_delays',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'quantity_completed' => 'decimal:2',
            'reported_progress_percent' => 'integer',
            'labor_count' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
