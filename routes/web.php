<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Resources\UserResource\Pages\UserKanban;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('users/kanban', UserKanban::class)->name('users.kanban');