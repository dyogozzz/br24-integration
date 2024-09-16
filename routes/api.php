<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Br24Controller;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/companies', [Br24Controller::class, 'getCompanies']);
Route::get('/contacts', [Br24Controller::class, 'getContacts']);