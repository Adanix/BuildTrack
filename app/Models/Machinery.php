<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machinery extends Model
{
    use HasFactory;

    protected $table = 'machineries';

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'registration_number',
        'make_model',
        'current_project_id',
        'assigned_driver_id',
        'operational_status',
        'notes',
    ];

    public function currentProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'current_project_id');
    }

    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MachineryDocument::class, 'machinery_id');
    }
}
