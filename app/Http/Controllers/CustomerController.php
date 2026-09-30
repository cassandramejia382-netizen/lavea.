<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $customers = Customer::query();

        if ($search) {
            $customers->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        if ($request->user()->role === 'staff') {
            $customers = $customers->latest()->paginate(15)->withQueryString();

            return view('staff.customers.index', compact('customers', 'search'));
        }

        $customers = $customers->latest()->get();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('staff.customers.form', ['customer' => new Customer]);
    }

    public function store(Request $request)
    {
        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:customers,phone'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer = Customer::create($details);

        return redirect()->route('staff.customers.show', $customer)->with('success', 'Customer registered.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.service', 'orders.staff']);

        if (request()->user()->role === 'staff') {
            return view('staff.customers.show', compact('customer'));
        }

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('staff.customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer->update($details);

        return redirect()->route('staff.customers.show', $customer)->with('success', 'Customer updated.');
    }
}
