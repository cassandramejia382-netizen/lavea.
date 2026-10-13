<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search', ''));
        $managers = User::where('role', 'manager')
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.managers.index', compact('managers', 'search'));
    }

    public function create(): View
    {
        return view('admin.managers.form', ['manager' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $details = $this->validateAccount($request);
        $details['password'] = Hash::make($details['password']);
        $manager = new User($details);
        $manager->role = 'manager';
        $manager->email_verified_at = now();
        $manager->save();

        return redirect()->route('admin.managers.index')->with('success', 'Manager account created successfully.');
    }

    public function edit(User $manager): View
    {
        abort_unless($manager->role === 'manager', 404);

        return view('admin.managers.form', compact('manager'));
    }

    public function update(Request $request, User $manager): RedirectResponse
    {
        abort_unless($manager->role === 'manager', 404);
        $details = $this->validateAccount($request, $manager);
        if (filled($details['password'] ?? null)) {
            $details['password'] = Hash::make($details['password']);
            $manager->remember_token = null;
        } else {
            unset($details['password']);
        }
        $manager->fill($details)->save();

        return redirect()->route('admin.managers.index')->with('success', 'Manager account updated successfully.');
    }

    /** @return array{name: string, email: string, phone?: ?string, password?: ?string} */
    private function validateAccount(Request $request, ?User $manager = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($manager)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$manager === null ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
