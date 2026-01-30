<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'title' => 'required|in:Prof.,Dr.,Mr.,Mrs.,Miss.',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'gender' => 'required|in:Male,Female',
            'email' => 'required|string|email|max:255|unique:users',
            'affiliation' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'presenting_paper' => 'nullable|in:yes,no',
        ]);

        // Create user account
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'institution' => $request->affiliation,
            'department' => $request->department,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
            'is_reviewer' => false,
        ]);

            // Create conference registration
            $conferenceData = [
                'user_id' => $user->id,
                'title' => $request->title,
                'firstname' => $user->first_name,
                'middlename' => $user->middle_name,
                'lastname' => $user->last_name,
                'email' => $user->email,
                'phone_number' => $request->phone_number,
                'institution' => $user->institution,
                'department' => $request->department,
                'gender' => $request->gender,
                'is_datican_member' => $request->boolean('is_datican_member'),
                'datican_status' => $request->datican_status,
                'is_presenting_paper' => $request->has('presenting_paper') && $request->presenting_paper === 'yes',
            ];
            
            // Note: Removed primary_role as requested
            ConferenceRegistration::create($conferenceData);

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Account created successfully!');
    }
}