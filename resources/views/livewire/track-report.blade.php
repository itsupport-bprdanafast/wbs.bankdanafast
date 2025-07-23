<div>
    @livewire('component.navbar')

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Search Form -->
        <x-card>
            <form wire:submit.prevent="searchReport" class="space-y-4">
                <x-input label="Token Laporan" wire:model="token" placeholder="Contoh: WB-ABC123"
                    hint="Masukkan token yang Anda terima setelah membuat laporan" />

                <div class="flex gap-2">
                    <x-button type="submit" primary wire:loading.attr="disabled" sm class="font-medium flex-1">
                        <span wire:loading.remove>Lacak Laporan</span>
                        <span wire:loading>Mencari...</span>
                    </x-button>

                    @if ($report)
                        <x-button sm type="button" secondary wire:click="resetSearch" class="font-medium">
                            Reset
                        </x-button>
                    @endif
                </div>
            </form>
        </x-card>

        @if ($report)
            <!-- Report Basic Info -->
            <x-card>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $report->token }}</h3>
                        <p class="text-gray-600">{{ $report->type_label }}</p>
                        <p class="text-sm text-gray-500 mt-1">Dibuat: {{ $report->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <x-badge :color="match ($report->status) {
                        'pending' => 'yellow',
                        'reviewing' => 'blue',
                        'investigating' => 'purple',
                        'resolved' => 'green',
                        'rejected' => 'red',
                        default => 'gray',
                    }">
                        {{ $report->status_label }}
                    </x-badge>
                </div>
            </x-card>

            <!-- Status Timeline -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-900">Riwayat Status Laporan</h4>

                @if ($statusHistories && count($statusHistories) > 0)
                    <div class="space-y-4">
                        @foreach ($statusHistories as $index => $history)
                            <x-card
                                class="relative {{ $history->status === $report->status ? 'ring-2 ring-blue-200 bg-blue-50/30' : '' }}">
                                <!-- Timeline connector -->
                                @if (!$loop->last)
                                    <div class="absolute left-8 top-16 w-0.5 h-8 bg-gray-300"></div>
                                @endif

                                <div class="flex items-start space-x-4">
                                    <!-- Status Icon -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center {{ match ($history->status_color) {
                                                'yellow' => 'bg-yellow-100 text-yellow-600',
                                                'blue' => 'bg-blue-100 text-blue-600',
                                                'purple' => 'bg-purple-100 text-purple-600',
                                                'green' => 'bg-green-100 text-green-600',
                                                'red' => 'bg-red-100 text-red-600',
                                                default => 'bg-gray-100 text-gray-600',
                                            } }}">
                                            <x-icon name="{{ $history->status_icon }}" class="w-4 h-4" />
                                        </div>
                                    </div>

                                    <!-- Status Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h5 class="text-sm font-medium text-gray-900">
                                                    {{ $history->status_label }}
                                                    @if ($history->status === $report->status)
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            Status Saat Ini
                                                        </span>
                                                    @endif
                                                </h5>
                                                <p class="text-xs text-gray-500">
                                                    {{ $history->created_at->format('d M Y H:i') }}
                                                    @if ($history->changed_by)
                                                        • oleh {{ $history->changed_by }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        @if ($history->notes)
                                            <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                                <p class="text-sm text-gray-700 whitespace-pre-line">
                                                    {{ $history->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                @else
                    <x-card>
                        <p class="text-gray-500 text-center py-4">Belum ada riwayat status untuk laporan ini.</p>
                    </x-card>
                @endif
            </div>

            <!-- Report Details -->
            <x-card>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Detail Laporan</h4>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    @if ($report->date_incidents)
                        <div>
                            <x-label>Tanggal Kejadian</x-label>
                            <p class="mt-1 text-gray-900">{{ $report->date_incidents->format('d M Y') }}</p>
                        </div>
                    @endif

                    @if ($report->type == 'gratifikasi' && $report->gratification_value)
                        <div>
                            <x-label>Nilai Gratifikasi</x-label>
                            <p class="mt-1 text-gray-900">Rp
                                {{ number_format($report->gratification_value, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    @if ($report->responded_at)
                        <div>
                            <x-label>Tanggal Respon</x-label>
                            <p class="mt-1 text-gray-900">{{ $report->responded_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <x-label>Deskripsi Laporan</x-label>
                    <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-800 whitespace-pre-line">{{ $report->description }}</p>
                    </div>
                </div>

                <!-- Reported Employees -->
                @if ($report->hasReportedEmployees())
                    <div class="mb-6">
                        <x-label>Pegawai yang Dilaporkan</x-label>
                        <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-800">{{ $report->reported_employees }}</p>
                        </div>
                    </div>
                @endif

                <!-- Attachments -->
                @if ($report->attachments && count($report->attachments) > 0)
                    <div>
                        <x-label>Lampiran ({{ count($report->attachments) }} file)</x-label>
                        <div class="mt-2 space-y-2">
                            @foreach ($report->attachments as $attachment)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <x-icon name="paper-clip" class="w-5 h-5 text-gray-400 mr-3" />
                                    <span class="text-gray-800">{{ basename($attachment) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-card>
        @endif
    </div>
</div>
