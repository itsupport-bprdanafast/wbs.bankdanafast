<?php

namespace App\Filament\Resources\ReportResource\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;

class ReportChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Laporan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $pengaduan = Report::where('type', 'pengaduan')->count();
        $gratifikasi = Report::where('type', 'gratifikasi')->count();
        $saranKeluhan = Report::where('type', 'saran_keluhan')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Laporan',
                    'data' => [$pengaduan, $gratifikasi, $saranKeluhan],
                    'backgroundColor' => [
                        'rgb(239, 68, 68)', // red-500 untuk pengaduan
                        'rgb(245, 158, 11)', // amber-500 untuk gratifikasi
                        'rgb(59, 130, 246)', // blue-500 untuk saran keluhan
                    ],
                    'borderColor' => [
                        'rgb(220, 38, 38)',
                        'rgb(217, 119, 6)',
                        'rgb(37, 99, 235)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pengaduan', 'Gratifikasi', 'Saran & Keluhan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
