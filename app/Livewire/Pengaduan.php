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

class Pengaduan extends Component
{
    use WithFileUploads, Interactions;

    // Form data
    public $reporter_name = '';
    public $reporter_email = '';
    public $reporter_phone = '';
    public $description = '';
    public $attachments = [];
    public $reported_employees = '';
    public $backup = [];
    public $date_incidents = '';

    protected $rules = [
        'reporter_name' => 'required|string|max:255',
        'reporter_email' => 'required|email|max:255',
        'reporter_phone' => 'nullable|string|max:20',
        'description' => 'required|string|min:10|max:5000',
        'reported_employees' => 'nullable|string|max:1000',
        'date_incidents' => 'required|date|before_or_equal:today',
        'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx', // Max 10MB per file
    ];

    public function updatingAttachments(): void
    {
        $this->backup = $this->attachments;
    }

    public function updatedAttachments(): void
    {
        if (!$this->attachments) return;

        $file = Arr::flatten(array_merge($this->backup, [$this->attachments]));
        $this->attachments = collect($file)
            ->unique(fn(UploadedFile $item) => $item->getClientOriginalName())
            ->toArray();
    }

    public function deleteUpload(array $content): void
    {
        $files = Arr::wrap($this->attachments);
        $file = collect($files)->first(fn(UploadedFile $item) => $item->getFilename() === $content['temporary_name']);

        if ($file) {
            rescue(fn() => $file->delete(), report: false);
            $this->attachments = collect($files)
                ->filter(fn(UploadedFile $item) => $item->getFilename() !== $content['temporary_name'])
                ->values()
                ->toArray();
        }
    }

    public function submit()
    {
        try {
            $this->validate();

            $attachmentFiles = collect($this->attachments)->map(function ($file) {
                if (!($file instanceof UploadedFile)) return null;

                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = "attachment/{$filename}";

                $mime = $file->getMimeType();

                if (is_string($mime) && str_starts_with($mime, 'image/')) {
                    $resizedImage = Image::read($file)
                        ->scaleDown(width: 1280)
                        ->encodeByExtension(quality: 75);

                    Storage::disk('public')->put($path, (string) $resizedImage);
                } else {
                    Storage::disk('public')->putFileAs('attachment', $file, $filename);
                }

                return $path;
            })->filter()->values()->toArray();

            $report = Report::create([
                'type' => 'pengaduan',
                'reporter_name' => $this->reporter_name,
                'reporter_email' => $this->reporter_email,
                'reporter_phone' => $this->reporter_phone,
                'description' => $this->description,
                'reported_employees' => $this->reported_employees,
                'attachments' => $attachmentFiles,
                'date_incidents' => $this->date_incidents,
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
                'reported_employees',
                'attachments',
                'date_incidents'
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
        return view('livewire.pengaduan');
    }
}
