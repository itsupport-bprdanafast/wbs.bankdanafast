<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Laporan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('token')
                                    ->label('Token')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Select::make('type')
                                    ->label('Jenis Laporan')
                                    ->options([
                                        'pengaduan' => 'Pengaduan',
                                        'gratifikasi' => 'Gratifikasi',
                                        'saran_keluhan' => 'Saran / Keluhan'
                                    ])
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Data Pelapor')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('reporter_name')
                                    ->label('Nama Pelapor')
                                    ->disabled(),

                                Forms\Components\TextInput::make('reporter_email')
                                    ->label('Email Pelapor')
                                    ->email()
                                    ->disabled(),

                                Forms\Components\TextInput::make('reporter_phone')
                                    ->label('Telepon Pelapor')
                                    ->tel()
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Detail Laporan')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->disabled(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('reported_employees')
                                    ->label('Karyawan yang Dilaporkan')
                                    ->disabled(),

                                Forms\Components\DatePicker::make('date_incidents')
                                    ->label('Tanggal Kejadian')
                                    ->disabled(),
                            ]),

                        Forms\Components\TextInput::make('gratification_value')
                            ->label('Nilai Gratifikasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->visible(fn($get) => $get('type') === 'gratifikasi'),

                        Forms\Components\Repeater::make('attachments')
                            ->label('Lampiran')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama File')
                                    ->disabled(),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->disabled(),
                            ])
                            ->disabled()
                            ->collapsed(),
                    ]),

                Section::make('Status & Tindak Lanjut')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Menunggu Review',
                                        'reviewing' => 'Sedang Direview',
                                        'investigating' => 'Dalam Investigasi',
                                        'resolved' => 'Selesai',
                                        'rejected' => 'Ditolak'
                                    ])
                                    ->required(),

                                Forms\Components\DateTimePicker::make('responded_at')
                                    ->label('Waktu Respon'),
                            ]),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Admin')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token')
                    ->label('Token')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pengaduan' => 'Pengaduan',
                        'gratifikasi' => 'Gratifikasi',
                        'saran_keluhan' => 'Saran / Keluhan',
                        default => $state
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pengaduan' => 'danger',
                        'gratifikasi' => 'warning',
                        'saran_keluhan' => 'info',
                        default => 'secondary'
                    }),

                TextColumn::make('reporter_name')
                    ->label('Pelapor')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'investigating' => 'primary',
                        'resolved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Menunggu Review',
                        'reviewing' => 'Sedang Direview',
                        'investigating' => 'Dalam Investigasi',
                        'resolved' => 'Selesai',
                        'rejected' => 'Ditolak',
                        default => $state
                    }),

                TextColumn::make('date_incidents')
                    ->label('Tanggal Kejadian')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('responded_at')
                    ->label('Waktu Respon')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis Laporan')
                    ->options([
                        'pengaduan' => 'Pengaduan',
                        'gratifikasi' => 'Gratifikasi',
                        'saran_keluhan' => 'Saran / Keluhan'
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Review',
                        'reviewing' => 'Sedang Direview',
                        'investigating' => 'Dalam Investigasi',
                        'resolved' => 'Selesai',
                        'rejected' => 'Ditolak'
                    ]),

                TrashedFilter::make()
                    ->label('Status Data'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
