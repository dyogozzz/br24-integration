<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Br24Controller;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/', [Br24Controller::class, 'handlePost']);

Route::get('/teste', [Br24Controller::class, 'geter']);