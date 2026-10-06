<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ServiceController extends Controller
{
    public function staffIndex(): View
    {
        $services = Service::orderBy('service_name')->get();

        return view('staff.services.index', compact('services'));
    }

    public function staffShow(Service $service): View
    {
        return view('staff.services.show', compact('service'));
    }

    /**
     * Display all services.
     */
    public function index(): View
    {
        $services = Service::latest()->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show create service form.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Save new service.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['image'] = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : null;

        try {
            Service::create($validated);
        } catch (Throwable $exception) {
            if ($validated['image']) {
                Storage::disk('public')->delete($validated['image']);
            }

            throw $exception;
        }

        $request->session()->forget('_old_input');

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service added successfully.');
    }

    /**
     * Display one service.
     */
    public function show(Service $service): View
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show edit form.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $oldImage = $service->image;
        $validated['image'] = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : $oldImage;

        try {
            $service->update($validated);
        } catch (Throwable $exception) {
            if ($validated['image'] && $validated['image'] !== $oldImage) {
                Storage::disk('public')->delete($validated['image']);
            }

            throw $exception;
        }

        if ($validated['image'] !== $oldImage) {
            $service->deleteUnusedImage($oldImage);
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $oldImage = $service->image;
        $service->delete();
        $service->deleteUnusedImage($oldImage);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('services', 'public');

        if (! $path) {
            throw ValidationException::withMessages([
                'image' => 'The image could not be saved. Please try again.',
            ]);
        }

        return $path;
    }
}
