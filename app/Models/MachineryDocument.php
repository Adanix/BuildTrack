<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineryDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'machinery_id',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'renewal_cost_estimate_inr',
        'flagged_for_renewal',
        'flagged_by',
        'flagged_at',
        'flag_notes',
        'last_reminder_logged_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'renewal_cost_estimate_inr' => 'decimal:2',
            'flagged_for_renewal' => 'boolean',
            'flagged_at' => 'datetime',
            'last_reminder_logged_at' => 'datetime',
        ];
    }

    public function machinery(): BelongsTo
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }

    public function flagger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function getDaysRemainingAttribute(): int
    {
        $today = Carbon::today('Asia/Kolkata');
        $expiry = Carbon::parse($this->expiry_date, 'Asia/Kolkata')->startOfDay();
        return (int) $today->diffInDays($expiry, false);
    }

    public function getStatusBucketAttribute(): string
    {
        $days = $this->days_remaining;

        if ($days < 0) {
            return 'expired';
        }
        if ($days <= 7) {
            return 'critical';
        }
        if ($days <= 30) {
            return 'expiring_soon';
        }
        return 'valid';
    }

    public function getStatusBucketLabelAttribute(): string
    {
        return match ($this->status_bucket) {
            'expired' => 'Expired',
            'critical' => 'Critical (<= 7 Days)',
            'expiring_soon' => 'Expiring Soon (8-30 Days)',
            default => 'Valid',
        };
    }

    public function getFormattedRenewalCostAttribute(): string
    {
        if ($this->renewal_cost_estimate_inr === null) {
            return 'N/A';
        }
        return 'INR ' . number_format((float) $this->renewal_cost_estimate_inr, 2);
    }
}
