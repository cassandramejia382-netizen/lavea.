<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function staffProfile(Request $request): View
    {
        return view('staff.profile', [
            'staff' => $request->user()->staffProfile,
            'profileAddress' => $request->user()->address,
        ]);
    }

    public function adminProfile(Request $request): View
    {
        return view('admin.profile', [
            'profilePhone' => $request->user()->phone,
            'profileAddress' => $request->user()->address,
        ]);
    }

    public function updateAdminAccountProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $hasChanges = $user->name !== $validated['name']
            || $user->phone !== ($validated['phone'] ?? null)
            || $user->address !== ($validated['address'] ?? null)
            || $request->hasFile('profile_photo');

        if (! $hasChanges) {
            return redirect()->route('admin.profile');
        }

        $previousPhoto = $user->profile_photo_path;
        $newPhoto = $request->file('profile_photo')?->store('staff-profiles', 'public');

        $user->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if ($newPhoto !== null) {
            $user->profile_photo_path = $newPhoto;
        }

        $user->save();

        if ($newPhoto !== null && $previousPhoto !== null) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()->route('admin.profile')->with('profile_saved', 'Your profile was saved successfully.');
    }

    public function updateStaffProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $staff = $user->staffProfile;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $hasChanges = $user->name !== $validated['name']
            || ($staff !== null && $staff->phone !== ($validated['phone'] ?? null))
            || $user->address !== ($validated['address'] ?? null)
            || $request->hasFile('profile_photo');

        if (! $hasChanges) {
            return redirect()->route('staff.profile');
        }

        $previousPhoto = $user->profile_photo_path;
        $newPhoto = $request->file('profile_photo')?->store('staff-profiles', 'public');

        $user->fill([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
        ]);

        if ($newPhoto !== null) {
            $user->profile_photo_path = $newPhoto;
        }

        $user->save();

        if ($staff !== null) {
            $staff->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ]);
        }

        if ($newPhoto !== null && $previousPhoto !== null) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()->route('staff.profile')->with('profile_saved', 'Your profile was saved successfully.');
    }

    public function index(Request $request): View
    {
        $settingsRole = $this->settingsRole($request);

        return view($settingsRole === 'admin' ? 'admin.settings.index' : 'staff.settings', [
            'settingsRole' => $settingsRole,
            'customerProfile' => $settingsRole === 'customer' ? $request->user()->customerProfile : null,
            'staffProfile' => $settingsRole === 'staff' ? $request->user()->staffProfile : null,
            'shopSettings' => $settingsRole === 'admin' ? DB::table('shop_settings')->find(1) : null,
        ]);
    }

    public function updateShopInformation(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $details = $request->validate([
            'shop_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
        ]);

        $shopSettings = DB::table('shop_settings')->where('id', 1)->first();
        $details['updated_at'] = now();

        if (! $shopSettings) {
            $details['created_at'] = now();
        }

        DB::table('shop_settings')->updateOrInsert(['id' => 1], $details);

        return redirect()->route('admin.settings')->with('shop_success', 'Shop information saved successfully.');
    }

    public function updateAdminProfile(Request $request): RedirectResponse
    {
        return $this->updateProfile($request);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $settingsRole = $this->settingsRole($request);
        $customer = $settingsRole === 'customer' ? $user->customerProfile : null;
        $staff = $settingsRole === 'staff' ? $user->staffProfile : null;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user),
                Rule::unique('customers', 'email')->ignore($customer?->getKey()),
                Rule::unique('staff', 'email')->ignore($staff?->getKey()),
            ],
        ];

        if ($customer !== null) {
            $rules['phone'] = 'required|string|max:20';
            $rules['address'] = 'required|string|max:255';
        } else {
            $rules['phone'] = 'nullable|string|max:20';
            $rules['address'] = 'nullable|string|max:255';
        }

        $details = $request->validate($rules);
        $emailChanged = $user->email !== $details['email'];

        DB::transaction(function () use ($user, $customer, $staff, $details, $emailChanged, $settingsRole): void {
            $user->fill([
                'name' => $details['name'],
                'email' => $details['email'],
                'phone' => $details['phone'] ?? null,
                'address' => $details['address'] ?? null,
            ]);

            if ($emailChanged && $settingsRole === 'customer') {
                $user->email_verified_at = null;
            }

            $user->save();

            if ($customer !== null) {
                $customer->update($details);
            }

            if ($staff !== null) {
                $staff->update([
                    'name' => $details['name'],
                    'email' => $details['email'],
                    'phone' => $details['phone'] ?? null,
                ]);
            }
        });

        if ($emailChanged && $settingsRole === 'customer') {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Exception $exception) {
                report($exception);

                return redirect()->route('verification.notice')
                    ->with('email_error', 'Your profile was saved, but we could not send the verification email. Please try again.');
            }

            return redirect()->route('verification.notice')
                ->with('status', 'Your profile was saved. Please verify your new email address.');
        }

        return redirect()->route($settingsRole.'.settings')->with('profile_success', 'Your profile has been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $details = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->forceFill([
            'password' => $details['password'],
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route($this->settingsRole($request).'.settings')->with('password_success', 'Password changed successfully.');
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $details = $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        return redirect()
            ->route($this->settingsRole($request).'.settings')
            ->withCookie(cookie('lavea_theme', $details['theme'], 60 * 24 * 365, httpOnly: false))
            ->with('theme_success', 'Appearance setting saved.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->validate(['show_notification_badge' => 'required|boolean']);

        $request->user()->show_notification_badge = $request->boolean('show_notification_badge');
        $request->user()->save();

        return redirect()->route($this->settingsRole($request).'.settings')
            ->with('notification_success', 'Notification preferences saved.');
    }

    private function settingsRole(Request $request): string
    {
        return match ($request->user()->role) {
            'admin' => 'admin',
            'staff' => 'staff',
            'customer', 'user' => 'customer',
            default => abort(403),
        };
    }
}
