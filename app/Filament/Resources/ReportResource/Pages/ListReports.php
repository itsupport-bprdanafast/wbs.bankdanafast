<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Laporan')
                ->icon('heroicon-m-clipboard-document-list')
                ->badge($this->getModel()::count()),

            'pengaduan' => Tab::make('Pengaduan')
                ->icon('heroicon-m-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'pengaduan'))
                ->badge($this->getModel()::where('type', 'pengaduan')->count())
                ->badgeColor('danger'),

            'gratifikasi' => Tab::make('Gratifikasi')
                ->icon('heroicon-m-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'gratifikasi'))
                ->badge($this->getModel()::where('type', 'gratifikasi')->count())
                ->badgeColor('warning'),

            'saran_keluhan' => Tab::make('Saran / Keluhan')
                ->icon('heroicon-m-chat-bubble-left-right')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'saran_keluhan'))
                ->badge($this->getModel()::where('type', 'saran_keluhan')->count())
                ->badgeColor('info'),
        ];
    }

    // Metode alternatif dengan status-based tabs
    public function getTabsWithStatus(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge($this->getModel()::count()),

            'pengaduan' => Tab::make('Pengaduan')
                ->icon('heroicon-m-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'pengaduan'))
                ->badge($this->getModel()::where('type', 'pengaduan')->count())
                ->badgeColor('danger'),

            'gratifikasi' => Tab::make('Gratifikasi')
                ->icon('heroicon-m-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'gratifikasi'))
                ->badge($this->getModel()::where('type', 'gratifikasi')->count())
                ->badgeColor('warning'),

            'saran_keluhan' => Tab::make('Saran / Keluhan')
                ->icon('heroicon-m-chat-bubble-left-right')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'saran_keluhan'))
                ->badge($this->getModel()::where('type', 'saran_keluhan')->count())
                ->badgeColor('info'),

            // Tab berdasarkan status untuk masing-masing type
            'pending' => Tab::make('Menunggu Review')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge($this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'investigating' => Tab::make('Dalam Investigasi')
                ->icon('heroicon-m-magnifying-glass')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'investigating'))
                ->badge($this->getModel()::where('status', 'investigating')->count())
                ->badgeColor('primary'),

            'resolved' => Tab::make('Selesai')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'resolved'))
                ->badge($this->getModel()::where('status', 'resolved')->count())
                ->badgeColor('success'),
        ];
    }
}
