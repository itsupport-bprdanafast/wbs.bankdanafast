<div>
    @livewire('component.navbar')

    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Form Saran / Keluhan</h1>
                <p class="text-gray-600">Silakan isi form berikut untuk mengajukan saran atau keluhan Anda.</p>
            </div>

            <form wire:submit="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-input wire:model="reporter_name" label="Nama Lengkap *" placeholder="Masukkan nama lengkap" />
                    <x-input wire:model="reporter_email" type="email" label="Email *" placeholder="nama@email.com" />
                    <x-input wire:model="reporter_phone" label="No. Telepon" placeholder="08xxxxxxxxxx" />
                </div>
                <div class="space-y-4">
                    <x-textarea wire:model="description" label="Deskripsi Saran / Keluhan *"
                        placeholder="Jelaskan detail saran / keluhan dengan lengkap..." rows="5" />
                </div>
                <div class="pt-6">
                    <x-button type="submit" color="amber" sm class="w-full font-semibold">
                        Kirim Laporan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
