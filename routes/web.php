<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Br24Controller;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/', [Br24Controller::class, 'handlePost']);
Route::get('/companies', [Br24Controller::class, 'getCompanies']);
Route::post('/companies', [Br24Controller::class, 'createCompany']);
Route::patch('/companies/{id}', [Br24Controller::class, 'editCompany']);
Route::get('/contacts', [Br24Controller::class, 'getContacts']);