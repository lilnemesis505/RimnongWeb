<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::post('/register', [CustomerController::class, 'register']);
