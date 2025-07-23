<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    protected $fillable = [
        'token',
        'type',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'description',
        'reported_employees',
        'attachments',
        'date_incidents',
        'status',
        'admin_notes',
        'responded_at'
    ];

    protected $casts = [
        'reported_employees' => 'array',
        'attachments' => 'array',
        'date_incidents' => 'datetime',
        'responded_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->token) {
                $model->token = 'WB-' . strtoupper(Str::random(6));
            }
        });
    }

    public function getTypeLabelsAttribute()
    {
        return [
            'pengaduan' => 'Pengaduan',
            'gratifikasi' => 'Gratifikasi',
            'saran_keluhan' => 'Saran / Keluhan'
        ];
    }

    public function getTypeLabelAttribute()
    {
        return $this->type_labels[$this->type] ?? $this->type;
    }

    public function getStatusLabelsAttribute()
    {
        return [
            'pending' => 'Menunggu Review',
            'reviewing' => 'Sedang Direview',
            'investigating' => 'Dalam Investigasi',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ];
    }

    public function getStatusLabelAttribute()
    {
        return $this->status_labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'reviewing' => 'info',
            'investigating' => 'primary',
            'resolved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getReportedEmployeesNamesAttribute()
    {
        if (!$this->reported_employees) {
            return null;
        }

        return collect($this->reported_employees)
            ->pluck('name')
            ->join(', ');
    }

    public function hasReportedEmployees()
    {
        return !empty($this->reported_employees);
    }
}
