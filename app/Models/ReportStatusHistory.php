<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStatusHistory extends Model
{
    protected $fillable = [
        'report_id',
        'status',
        'notes',
        'changed_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Review',
            'reviewing' => 'Sedang Direview',
            'investigating' => 'Dalam Investigasi',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'reviewing' => 'blue',
            'investigating' => 'purple',
            'resolved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'clock',
            'reviewing' => 'eye',
            'investigating' => 'magnifying-glass',
            'resolved' => 'check-circle',
            'rejected' => 'x-circle',
            default => 'question-mark-circle',
        };
    }
}
