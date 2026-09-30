<?php

use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/services', function () {
    return response()->json(Service::all());
});
