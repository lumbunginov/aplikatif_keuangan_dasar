<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $timezones = timezone_identifiers_list();

        return view('settings.index', compact('settings', 'timezones'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'timezone' => 'required|timezone',
            'mail_from_name' => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'session_lifetime' => 'required|integer|min:1|max:10080',
            'maintenance_mode' => 'nullable',
            'app_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico,svg|max:1024',
            'pwa_enabled' => 'nullable',
            'pwa_short_name' => 'nullable|string|max:12',
            'pwa_theme_color' => 'nullable|string|max:7',
        ]);

        $textSettings = [
            'app_name', 'primary_color', 'timezone',
            'mail_from_name', 'mail_from_address', 'session_lifetime',
            'pwa_short_name', 'pwa_theme_color',
        ];

        foreach ($textSettings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0');
        Setting::set('pwa_enabled', $request->boolean('pwa_enabled') ? '1' : '0');

        if ($request->hasFile('app_logo')) {
            $logoName = 'logo_' . time() . '.' . $request->file('app_logo')->getClientOriginalExtension();
            $request->file('app_logo')->move(storage_path('app/public/settings'), $logoName);
            Setting::set('app_logo', $logoName);
        }

        if ($request->hasFile('app_favicon')) {
            $faviconName = 'favicon_' . time() . '.' . $request->file('app_favicon')->getClientOriginalExtension();
            $request->file('app_favicon')->move(storage_path('app/public/settings'), $faviconName);
            Setting::set('app_favicon', $faviconName);
        }

        activity()
            ->causedBy(auth()->user())
            ->log('Settings updated');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
