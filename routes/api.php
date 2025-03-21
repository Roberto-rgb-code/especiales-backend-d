<?php

use App\Http\Controllers\EspecialController;
use Illuminate\Support\Facades\Route;

Route::apiResource('especiales-destacados', EspecialController::class);
