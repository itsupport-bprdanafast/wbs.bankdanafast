<div class="mb-12">
    <div class="fixed top-0 w-full shadow-sm p-2 py-3 bg-white mx-auto">
        <div class="flex items-center max-w-4xl gap-3 mx-auto">

            <img src="{{ asset('image/logo.png') }}" width="120" class="mr-12" />
            <x-dropdown position="bottom">
                <x-slot:action>
                    <x-button x-on:click="show = !show" color="blue" class="font-medium" sm outline flat
                        text="Laporan" />
                </x-slot:action>
                <x-dropdown.items :href="route('pengaduan')" text="Pengaduan" />
                <x-dropdown.items :href="route('gratifikasi')" text="Gratifikasi" />
                <x-dropdown.items :href="route('saran-keluhan')" text="Saran / Keluhan" />
            </x-dropdown>
            <x-button color="blue" class="font-medium" sm outline flat text="Status Pelaporan" />
        </div>
    </div>
</div>
