<?php

namespace App\Livewire;

use App\Models\Report;
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

    protected $rules = [
        'reporter_name' => 'required|string|max:255',
        'reporter_email' => 'required|email|max:255',
        'reporter_phone' => 'nullable|string|max:20',
        'description' => 'required|string|min:10|max:5000',
    ];

    public function submit()
    {
        try {
            $report = Report::create([
                'type' => 'saran_keluhan',
                'reporter_name' => $this->reporter_name,
                'reporter_email' => $this->reporter_email,
                'reporter_phone' => $this->reporter_phone,
                'description' => $this->description,
                'status' => 'pending',
            ]);

            $this->dialog()->success(
                $report->token,
                "Silakan simpan token ini untuk melacak status laporan Anda.<br>
             <a href='" . route('status', ['token' => $report->token]) . "' 
                class='text-blue-600 hover:text-blue-800 underline'>
                Klik disini untuk tracking
             </a>"
            )->send();

            if ($this->reporter_email) {
                $this->sendReportConfirmationEmail($report);
            }

            $this->reset([
                'reporter_name',
                'reporter_email',
                'reporter_phone',
                'description',
            ]);

            $this->toast()->success('Pengaduan berhasil dikirim!')->send();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation 
            $this->toast()->error('Periksa kembali data yang Anda masukkan.')->send();
            throw $e;
        } catch (\Exception $e) {
            logger("Report submission error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'type' => 'pengaduan',
                    'reporter_name' => $this->reporter_name,
                    'description' => substr($this->description, 0, 100) . '...'
                ]
            ]);

            report($e);
            $this->toast()->error('Gagal mengirim pengaduan. Silakan coba lagi atau hubungi administrator.')->send();
        }
    }

    private function sendReportConfirmationEmail($report)
    {
        try {
            // You can implement email notification here
            // Mail::to($report->reporter_email)->send(new ReportConfirmationMail($report));
            logger("Confirmation email should be sent to: " . $report->reporter_email);
        } catch (\Exception $e) {
            logger("Failed to send confirmation email", ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.saran-keluhan');
    }
}
