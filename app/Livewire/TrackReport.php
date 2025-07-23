<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;

class TrackReport extends Component
{
    public $token = '';
    public $report = null;
    public $statusHistories = [];

    protected $rules = [
        'token' => 'required|string|min:3'
    ];

    protected $messages = [
        'token.required' => 'Token laporan harus diisi',
        'token.min' => 'Token minimal 3 karakter'
    ];

    public function searchReport()
    {
        $this->validate();

        $this->report = Report::with(['statusHistories' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->where('token', strtoupper($this->token))->first();

        if (!$this->report) {
            $this->addError('token', 'Token tidak ditemukan atau tidak valid');
            return;
        }

        $this->statusHistories = $this->report->statusHistories;
    }

    public function resetSearch()
    {
        $this->reset(['token', 'report', 'statusHistories']);
        $this->resetErrorBag();
        return redirect()->route('status');
    }

    public function mount()
    {
        // Jika ada token dari query parameter, langsung cari
        if (request('token')) {
            $this->token = request('token');
            $this->searchReport();
        }
    }

    public function render()
    {
        return view('livewire.track-report');
    }
}
