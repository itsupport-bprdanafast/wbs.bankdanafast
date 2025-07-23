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

class Gratifikasi extends Component
{
    use WithFileUploads, Interactions;

    // Form data
    public $reporter_name = '';
    public $reporter_email = '';
    public $reporter_phone = '';
    public $description = '';
    public $attachments = [];
    public $reported_employees = '';
    public $gratification_value = '';
    public $date_incidents = '';
    public $backup = [];

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
                'type' => 'gratifikasi',
                'reporter_name' => $this->reporter_name,
                'reporter_email' => $this->reporter_email,
                'reporter_phone' => $this->reporter_phone,
                'description' => $this->description,
                'reported_employees' => $this->reported_employees,
                'gratification_value' => $this->gratification_value,
                'attachments' => $attachmentFiles,
                'date_incidents' => $this->date_incidents,
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
        return view('livewire.gratifikasi');
    }
}
