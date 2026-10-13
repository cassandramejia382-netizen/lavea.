<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search', ''));
        $staff = Staff::query()->when($search !== '', function (Builder $query) use ($search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%');
            });
        })->latest()->get();

        return view('admin.staff.index', compact('staff', 'search'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Staff::create($this->validateWorker($request));

        return redirect()->route($this->routePrefix().'.index')->with('success', 'Staff added successfully.');
    }

    public function show(Staff $staff): View
    {
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff): View
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $staff->update($this->validateWorker($request, $staff));

        return redirect()->route($this->routePrefix().'.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        if ($staff->orders()->exists() || $staff->deliveryOrders()->exists()) {
            return back()->with('error', 'Staff with order history cannot be deleted. Set their status to Inactive instead.');
        }
        $staff->delete();

        return redirect()->route($this->routePrefix().'.index')->with('success', 'Staff deleted successfully.');
    }

    /** @return array{name: string, email?: ?string, phone?: ?string, role: string, status: string} */
    private function validateWorker(Request $request, ?Staff $staff = null): array
    {
        if ($request->input('role') === 'Other' && is_string($request->input('custom_position'))) {
            $request->merge(['custom_position' => trim($request->input('custom_position'))]);
        }
        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('staff', 'email')->ignore($staff)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(array_filter([...Staff::JOB_POSITIONS, 'Other', $staff?->role]))],
            'custom_position' => ['exclude_unless:role,Other', 'required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);
        if ($details['role'] === 'Other') {
            $details['role'] = $details['custom_position'];
        }
        unset($details['custom_position']);

        return $details;
    }

    private function routePrefix(): string
    {
        return auth()->user()->role.'.staff';
    }
}
