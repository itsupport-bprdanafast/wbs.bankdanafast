<?php

namespace App\Livewire;

use App\Models\Report;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class SaranKeluhan extends Component
{
    use WithFileUploads, Interactions;

    // Form data
    public $reporter_name = '';
    public $reporter_email = '';
    public $reporter_phone = '';
    public $description = '';

    public function submit()
    {
        try {
            $report = Report::create([
                'type' => 'saran_keluhan',
                'reporter_name' => $this->reporter_name,
                'reporter_email' => $this->reporter_email,
                'reporter_phone' => $this->reporter_phone,
                'description' => $this->description,
            ]);

            $this->dialog()->success(
                title: $report->token,
                description: 'Silakan simpan token ini untuk tracking laporan Anda.'
            )->send();

            $this->reset();
        } catch (\Exception $e) {
            report($e);
            $this->toast()->error('Gagal mengirim pengaduan. Periksa kembali lampiran dan data lainnya.');
        }
    }

    public function render()
    {
        return view('livewire.saran-keluhan');
    }
}
