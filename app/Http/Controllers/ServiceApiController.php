<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $details = $request->validate([
            'search' => ['sometimes', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $services = Service::where('is_active', true)
            ->when(isset($details['search']), fn (Builder $query): Builder => $query->where('service_name', 'like', '%'.$details['search'].'%'))
            ->orderBy('service_name')->paginate($details['per_page'] ?? 15)->withQueryString();

        return ServiceResource::collection($services);
    }

    public function show(Service $service): ServiceResource
    {
        abort_unless($service->is_active, 404);

        return new ServiceResource($service);
    }
}
