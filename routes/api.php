<?php

use App\Http\Controllers\ServiceApiController;
use Illuminate\Support\Facades\Route;

Route::get('/services', [ServiceApiController::class, 'index']);
