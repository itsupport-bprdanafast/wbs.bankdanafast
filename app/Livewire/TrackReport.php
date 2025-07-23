<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;

class TrackReport extends Component
{
    public $token = '';
    public $report = null;

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

        $this->report = Report::where('token', strtoupper($this->token))->first();

        if (!$this->report) {
            $this->addError('token', 'Token tidak ditemukan atau tidak valid');
        }
    }

    public function resetSearch()
    {
        $this->reset(['token', 'report']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.track-report');
    }
}
