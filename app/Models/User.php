<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProjectManager(): bool
    {
        return $this->role === 'project_manager';
    }

    public function isForeman(): bool
    {
        return $this->role === 'foreman';
    }

    public function isHrPayroll(): bool
    {
        return $this->role === 'hr_payroll';
    }

    public function isAssetManager(): bool
    {
        return $this->role === 'asset_manager';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Owner / Director (Admin)',
            'project_manager' => 'Project Manager',
            'foreman' => 'Site Foreman',
            'hr_payroll' => 'HR & Payroll Officer',
            'asset_manager' => 'Fleet & Asset Manager',
            'driver' => 'Machine Operator / Driver',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    public function reportedDailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'reported_by');
    }

    public function reviewedDailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'reviewed_by');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function assignedMachineries(): HasMany
    {
        return $this->hasMany(Machinery::class, 'assigned_driver_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
}
