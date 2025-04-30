<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::post('/scan-license-plate', [\App\Http\Controllers\LicensePlateController::class, 'scan']);
