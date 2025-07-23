<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Report extends Model
{
    use SoftDeletes;

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
        'gratification_value',
        'status',
        'admin_notes',
        'responded_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'date_incidents' => 'date',
        'responded_at' => 'datetime',
        'gratification_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'pengaduan' => 'Pengaduan',
            'gratifikasi' => 'Gratifikasi',
            'saran_keluhan' => 'Saran & Keluhan',
            default => ucfirst($this->type),
        };
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

    public function hasReportedEmployees(): bool
    {
        return !empty($this->reported_employees);
    }

    /**
     * Generate unique token for report
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = 'WB-' . strtoupper(Str::random(6));
        } while (self::where('token', $token)->exists());

        return $token;
    }

    // Event untuk membuat history ketika status berubah
    protected static function booted()
    {
        // Generate token sebelum create
        static::creating(function ($report) {
            if (empty($report->token)) {
                $report->token = self::generateUniqueToken();
            }

            // Set default status jika belum ada
            if (empty($report->status)) {
                $report->status = 'pending';
            }
        });

        static::created(function ($report) {
            // Buat history untuk status awal
            $report->statusHistories()->create([
                'status' => $report->status,
                'notes' => 'Laporan dibuat oleh ' . ($report->reporter_name ?? 'Anonim'),
                'changed_by' => 'System'
            ]);
        });

        static::updated(function ($report) {
            // Jika status berubah, buat history baru
            if ($report->isDirty('status')) {
                $report->statusHistories()->create([
                    'status' => $report->status,
                    'notes' => $report->admin_notes ?? 'Status diperbarui',
                    'changed_by' => Auth::user()->name ?? 'Admin'
                ]);
            }
        });
    }
}
