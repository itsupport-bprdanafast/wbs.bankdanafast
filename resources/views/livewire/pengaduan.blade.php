<div>
    @livewire('component.navbar')

    <div class="max-w-4xl mx-auto md:p-6">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Form Pengaduan</h1>
                <p class="text-gray-600">Laporkan pelanggaran atau tindakan yang tidak sesuai dengan ketentuan</p>
            </div>

            <form wire:submit="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-input wire:model="reporter_name" label="Nama Lengkap *" placeholder="Masukkan nama lengkap" />
                    <x-input wire:model="reporter_email" type="email" label="Email *" placeholder="nama@email.com" />
                    <x-input wire:model="reporter_phone" label="No. Telepon" placeholder="08xxxxxxxxxx" />
                </div>
                <div class="space-y-4">
                    <x-date label="Tanggal Kejadian *" format="DD MMMM YYYY" wire:model="date_incidents" />
                    <x-textarea wire:model="description" label="Deskripsi Pengaduan *"
                        placeholder="Jelaskan detail pengaduan dengan lengkap..." rows="5" />
                </div>
                <x-input label="Pegawai yang Dilaporkan" wire:model="reported_employees" />
                <div>
                    <x-upload wire:model="attachments" label="Lampiran Bukti Pengaduan"
                        accept="application/pdf,image/jpg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        hint="Maksimal 10MB per file. Format: PDF, JPG, PNG, DOCX" multiple delete />
                </div>
                <div class="pt-6">
                    <x-button type="submit" color="red" sm class="w-full font-semibold">
                        Kirim Pengaduan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
