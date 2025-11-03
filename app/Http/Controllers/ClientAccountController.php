<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ClientAccountController extends Controller
{
    public function dashboard()
    {
        return view('client.dashboard');
    }

    // GET /client/profile
    public function profile()
    {
        return view('client.profile');
    }

    // POST /client/profile (basic profile update)
    public function updateProfile(Request $request)
    {
        $client = Auth::guard('client')->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['required', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
        ]);

        $client->update($data);

        return back()->with('status', 'Profile updated successfully.');
    }

    // POST /client/profile/password (optional: change password)
    public function updatePassword(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, $client->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $client->forceFill(['password' => Hash::make($request->password)])->save();

        return back()->with('status', 'Password updated successfully.');
    }

    // POST /client/logout
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }
}