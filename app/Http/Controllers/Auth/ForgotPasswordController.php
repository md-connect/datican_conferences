<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Validate email and first name, then redirect to reset form
     */
    public function validateUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'first_name' => 'required|string',
        ]);

        // Find user by email and first name
        $user = User::where('email', $request->email)
            ->where('first_name', $request->first_name)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'first_name' => 'The provided first name does not match our records.'
            ])->withInput();
        }

        // Generate a reset token
        $token = Str::random(60);

        // Store in password_resets table
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Store email and token in session for the reset form
        session([
            'reset_email' => $request->email,
            'reset_token' => $token,
            'reset_first_name' => $request->first_name
        ]);

        return redirect()->route('password.reset.form')
            ->with('success', 'Verification successful. Please choose your new password.');
    }

    /**
     * Show the reset password form
     */
    public function showResetForm()
    {
        // Check if session has reset data
        if (!session()->has('reset_email') || !session()->has('reset_token')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Please verify your email and first name first.');
        }

        return view('auth.reset-password', [
            'email' => session('reset_email'),
            'first_name' => session('reset_first_name')
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify session data
        if (session('reset_email') !== $request->email || 
            session('reset_first_name') !== $request->first_name) {
            return back()->withErrors([
                'email' => 'Invalid reset session. Please start over.'
            ])->withInput();
        }

        // Find the user
        $user = User::where('email', $request->email)
            ->where('first_name', $request->first_name)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User not found.'
            ])->withInput();
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear reset data from session
        session()->forget(['reset_email', 'reset_token', 'reset_first_name']);

        // Delete used reset token
        DB::table('password_resets')
            ->where('email', $request->email)
            ->where('first_name', $request->first_name)
            ->delete();

        // Redirect to confirmation page
        return redirect()->route('password.confirmation')
            ->with('email', $request->email);
    }

    /**
     * Show password change confirmation page
     */
    public function showConfirmation()
    {
        $email = session('email');
        return view('auth.password-change-confirmation', compact('email'));
    }
}