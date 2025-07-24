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

                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Catatan Admin'),
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
                    ->sortable()
                    ->copyable(),

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

                TextColumn::make('admin_notes')
                    ->label('Catatan Admin')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Actions\Action::make('review')
                    ->label('Review Laporan')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Review Laporan')
                    ->modalDescription('Apakah kamu ingin mereview laporan ini?')
                    ->modalSubmitActionLabel('Ya, Review')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'reviewing',
                            'responded_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Laporan berhasil direview')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('investigate')
                    ->label('Investigasi Laporan')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->visible(fn($record) => $record->status === 'reviewing')
                    ->requiresConfirmation()
                    ->modalHeading('Investigasi Laporan')
                    ->modalDescription('Apakah kamu ingin investigasi laporan ini?')
                    ->modalSubmitActionLabel('Ya, Investigasi')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record) {
                        $record->update(['status' => 'investigating']);

                        \Filament\Notifications\Notification::make()
                            ->title('Status berhasil diubah ke investigasi')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject_review')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'reviewing')
                    ->form([
                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Alasan Penolakan')
                            ->placeholder('Masukkan alasan penolakan laporan...')
                            ->required()
                            ->maxLength(5000)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->modalHeading('Tolak Laporan')
                    ->modalDescription('Silakan masukkan alasan penolakan untuk laporan ini.')
                    ->modalSubmitActionLabel('Tolak Laporan')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                            'responded_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Laporan telah ditolak')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\Action::make('resolve_report')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'investigating')
                    ->form([
                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Catatan Admin')
                            ->placeholder('Masukkan catatan hasil investigasi...')
                            ->required()
                            ->maxLength(5000)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->modalHeading('Selesaikan Laporan')
                    ->modalDescription('Silakan masukkan catatan hasil investigasi.')
                    ->modalSubmitActionLabel('Selesaikan Laporan')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'resolved',
                            'admin_notes' => $data['admin_notes'],
                            'responded_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Laporan berhasil diselesaikan')
                            ->success()
                            ->send();
                    }),

                // Action untuk reject (terpisah)
                Tables\Actions\Action::make('reject_investigation')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'investigating')
                    ->form([
                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Alasan Penolakan')
                            ->placeholder('Masukkan alasan penolakan laporan...')
                            ->required()
                            ->maxLength(5000)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->modalHeading('Tolak Laporan')
                    ->modalDescription('Silakan masukkan alasan penolakan.')
                    ->modalSubmitActionLabel('Tolak Laporan')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                            'responded_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Laporan telah ditolak')
                            ->danger()
                            ->send();
                    }),


                Tables\Actions\ViewAction::make('view_report')
                    ->label('Lihat Laporan')
                    ->visible(fn($record) => in_array($record->status, ['resolved', 'rejected'])),

                // Tables\Actions\DeleteAction::make(),
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

    public static function canCreate(): bool
    {
        return false;
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
