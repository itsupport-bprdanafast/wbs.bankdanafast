<div>
    @livewire('component.navbar')

    <div class="max-w-2xl mx-auto space-y-6">
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

        <!-- Report Details -->
        @if ($report)
            <x-card>
                <!-- Header Info -->
                <div class="flex justify-between items-start mb-6 pb-4 border-b">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $report->token }}</h3>
                        <p class="text-gray-600">{{ $report->type_label }}</p>
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

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <x-label>Tanggal Dibuat</x-label>
                        <p class="mt-1 text-gray-900">{{ $report->created_at->format('d M Y H:i') }}</p>
                    </div>

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

                <!-- Admin Notes -->
                @if ($report->admin_notes)
                    <div class="mb-6">
                        <x-label>Catatan/Respon Admin</x-label>
                        <div class="mt-2 p-4 bg-blue-50 rounded-lg">
                            <p class="text-blue-800 whitespace-pre-line">{{ $report->admin_notes }}</p>
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
