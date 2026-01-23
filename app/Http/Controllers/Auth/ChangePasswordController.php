<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }
    
    public function change(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('profile')->with('success', 'Password changed successfully.');
    }
}