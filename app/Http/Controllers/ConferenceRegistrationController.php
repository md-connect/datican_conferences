<?php

namespace App\Http\Controllers;

use App\Models\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ConferenceRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Please login to register for the conference.');
        }
        
        // Check if user already has a registration
        $existingRegistration = ConferenceRegistration::where('user_id', auth()->id())
            ->orWhere('email', auth()->user()->email)
            ->first();
        
        if ($existingRegistration) {
            // Store registration in session for success page
            session(['existing_registration' => $existingRegistration]);
            
            // Show "Already Registered" view instead of redirecting
            return view('auth.already-registered', compact('existingRegistration'));
        }
        
        return view('auth.conference-registration');
    }

    public function register(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to register.');
        }
        
        $user = auth()->user();
        
        // Check if user already has a registration
        $existingRegistration = ConferenceRegistration::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
        
        if ($existingRegistration) {
            return redirect()->route('dashboard')
                ->with('info', 'You have already registered for DATICAN Conference 2026.');
        }

        $validated = $request->validate([
            'title' => 'required|in:Prof.,Dr.,Mr.,Mrs.,Miss',
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:conference_registrations,email',
            'phone_number' => 'required|string|max:20',
            'institution' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female',
            'is_datican_member' => 'required|in:Yes,No',
            'datican_status' => 'required_if:is_datican_member,Yes|in:PI,Faculty,Trainer,PhD Student,MSc. Student',
            'is_presenting_paper' => 'required|in:Yes,No',
        ]);

        // Convert Yes/No to boolean
        $validated['is_datican_member'] = $validated['is_datican_member'] === 'Yes';
        $validated['is_presenting_paper'] = $validated['is_presenting_paper'] === 'Yes';
        
        // Add user_id to registration
        $validated['user_id'] = $user->id;

        // Store in database
        $registration = ConferenceRegistration::create($validated);

        // Cache the registration stats
        $this->updateRegistrationStats();

        // Store registration in session for success page
        session(['registration' => $registration]);

        return redirect()->route('conference.registration.success');
    }

    public function success()
    {
        // Check for existing registration in session (from showRegistrationForm)
        $existingRegistration = session('existing_registration');
        if ($existingRegistration) {
            return view('auth.registration-success', ['registration' => $existingRegistration]);
        }
        
        // Check for new registration in session (from register method)
        $registration = session('registration');
        if (!$registration) {
            return redirect()->route('conference.registration');
        }

        return view('auth.registration-success', compact('registration'));
    }

    public function stats()
    {
        // Only admins can view stats
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
        
        // Cache registration stats for 5 minutes
        $stats = Cache::remember('registration_stats', 300, function () {
            return [
                'total_registrations' => ConferenceRegistration::count(),
                'total_presenters' => ConferenceRegistration::where('is_presenting_paper', true)->count(),
                'total_datican_members' => ConferenceRegistration::where('is_datican_member', true)->count(),
                'gender_distribution' => ConferenceRegistration::selectRaw('gender, COUNT(*) as count')
                    ->groupBy('gender')
                    ->pluck('count', 'gender')
                    ->toArray(),
                'status_distribution' => ConferenceRegistration::whereNotNull('datican_status')
                    ->selectRaw('datican_status, COUNT(*) as count')
                    ->groupBy('datican_status')
                    ->pluck('count', 'datican_status')
                    ->toArray(),
            ];
        });

        return view('admin.registration-stats', compact('stats'));
    }

    private function updateRegistrationStats()
    {
        Cache::forget('registration_stats');
    }
}