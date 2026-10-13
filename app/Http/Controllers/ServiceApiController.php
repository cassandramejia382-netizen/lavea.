<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Service::all());
    }
}
