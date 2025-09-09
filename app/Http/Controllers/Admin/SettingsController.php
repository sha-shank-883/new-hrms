<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Update the specified settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email|max:255',
            'timezone' => 'required|timezone',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
