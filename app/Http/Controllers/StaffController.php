<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Notifications\LaveaPasswordResetNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('user')->latest()->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:staff,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        DB::transaction(function () use ($details) {
            $user = User::create([
                'name' => $details['name'],
                'email' => $details['email'],
                'password' => Hash::make($details['password']),
                'role' => 'staff',
                'email_verified_at' => now(),
            ]);

            Staff::create([
                'name' => $details['name'],
                'email' => $details['email'],
                'phone' => $details['phone'] ?? null,
                'role' => $details['role'],
                'status' => $details['status'],
            ]);

        });

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff added successfully.');
    }

    public function show(Staff $staff)
    {
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $user = User::where('email', $staff->email)->where('role', 'staff')->first();

        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.($user->id ?? 0), 'unique:staff,email,'.$staff->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        if ($user) {
            $user->update([
                'name' => $details['name'],
                'email' => $details['email'],
            ]);
        }

        $staff->update($details);

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $user = User::where('email', $staff->email)->where('role', 'staff')->first();

        DB::transaction(function () use ($staff, $user) {
            if ($user) {
                $user->delete();
            }

            $staff->delete();
        });

        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted successfully.');
    }

    public function sendInvitation(Staff $staff)
    {
        $user = User::where('email', $staff->email)->where('role', 'staff')->first();

        if (! $user) {
            return back()->with('email_error', 'This staff record does not have a login account.');
        }

        if ($this->sendInvitationEmail($user)) {
            return back()->with('success', 'A password setup link has been emailed.');
        }

        return back()->with('email_error', 'We could not send the email. Please try again later.');
    }

    private function sendInvitationEmail($user)
    {
        try {
            $token = Password::broker()->createToken($user);
            $user->notify(new LaveaPasswordResetNotification($token));

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
