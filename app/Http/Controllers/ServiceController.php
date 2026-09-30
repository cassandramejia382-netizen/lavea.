<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function staffIndex()
    {
        $services = Service::orderBy('service_name')->get();

        return view('staff.services.index', compact('services'));
    }

    /**
     * Display all services.
     */
    public function index()
    {
        $services = Service::latest()->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show create service form.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Save new service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();

            $image->move(
                public_path('uploads/services'),
                $imageName
            );
        }

        Service::create([
            'service_name' => $request->service_name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        $request->session()->forget('_old_input');

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service added successfully.');
    }

    /**
     * Display one service.
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show edit form.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update service.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = $service->image;

        if ($request->hasFile('image')) {

            if (
                $service->image &&
                file_exists(public_path('uploads/services/'.$service->image))
            ) {
                unlink(public_path('uploads/services/'.$service->image));
            }

            $imageName = time().'.'.$request->image->extension();

            $request->image->move(
                public_path('uploads/services'),
                $imageName
            );
        }

        $service->update([
            'service_name' => $request->service_name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service.
     */
    public function destroy(Service $service)
    {
        if (
            $service->image &&
            file_exists(public_path('uploads/services/'.$service->image))
        ) {
            unlink(public_path('uploads/services/'.$service->image));
        }

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
