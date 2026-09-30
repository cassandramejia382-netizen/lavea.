<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $shopSettings = DB::table('shop_settings')->find(1);

        return view('admin.settings.index', compact('shopSettings'));
    }

    public function updateShopInformation(Request $request)
    {
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

    public function updateAdminProfile(Request $request)
    {
        $admin = $request->user();

        $details = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$admin->id,
        ]);

        $admin->update($details);

        return redirect()->route('admin.settings')->with('profile_success', 'Admin profile saved successfully.');
    }

    public function updatePassword(Request $request)
    {
        $details = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => $details['password'],
        ]);

        return redirect()->route('admin.settings')->with('password_success', 'Password changed successfully.');
    }

    public function updateAppearance(Request $request)
    {
        $details = $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        return redirect()
            ->route('admin.settings')
            ->withCookie(cookie('lavea_theme', $details['theme'], 60 * 24 * 365))
            ->with('theme_success', 'Appearance setting saved.');
    }
}
