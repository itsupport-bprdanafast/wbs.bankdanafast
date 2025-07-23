<?php

namespace App\Filament\Resources\ReportResource\Widgets;

use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stats berdasarkan Type
            Stat::make('Total Pengaduan', Report::where('type', 'pengaduan')->count())
                ->description('Laporan pengaduan yang masuk')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Gratifikasi', Report::where('type', 'gratifikasi')->count())
                ->description('Laporan gratifikasi yang masuk')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart([17, 16, 14, 15, 14, 13, 12]),

            Stat::make('Total Saran & Keluhan', Report::where('type', 'saran_keluhan')->count())
                ->description('Saran dan keluhan yang masuk')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),

            // Stats berdasarkan Status - Lengkap
            Stat::make('Menunggu Review', Report::where('status', 'pending')->count())
                ->description('Laporan yang belum direview')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray')
                ->chart([2, 1, 3, 2, 4, 1, 2]),

            Stat::make('Sedang Direview', Report::where('status', 'reviewing')->count())
                ->description('Laporan yang sedang direview')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info')
                ->chart([1, 3, 2, 4, 1, 3, 2]),

            Stat::make('Dalam Investigasi', Report::where('status', 'investigating')->count())
                ->description('Laporan yang sedang diinvestigasi')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('primary')
                ->chart([1, 2, 1, 3, 2, 1, 3]),

            Stat::make('Laporan Selesai', Report::where('status', 'resolved')->count())
                ->description('Laporan yang sudah diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([3, 2, 5, 3, 8, 4, 10]),

            Stat::make('Laporan Ditolak', Report::where('status', 'rejected')->count())
                ->description('Laporan yang ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([0, 1, 0, 2, 1, 0, 1]),

            // Summary Stats
            Stat::make('Total Laporan', Report::count())
                ->description('Total semua laporan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([25, 20, 35, 28, 45, 32, 50]),
        ];
    }

    protected function getColumns(): int
    {
        return 3; // Akan menampilkan 3 kolom per baris
    }
}
