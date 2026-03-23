<?php

use App\Livewire\ChatBot;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ia', ChatBot::class)->name('ia');
