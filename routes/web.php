<?php

use Illuminate\Support\Facades\Route;
use App\Livewire;

Route::get('/', Livewire\Home::class)->name('home');
Route::get('/pengaduan', Livewire\Pengaduan::class)->name('pengaduan');
Route::get('/gratifikasi', Livewire\Gratifikasi::class)->name('gratifikasi');
Route::get('/saran-keluhan', Livewire\SaranKeluhan::class)->name('saran-keluhan');
Route::get('/status', Livewire\TrackReport::class)->name('status');
