<?php

namespace App\Http\Controllers;

use App\Models\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConferenceRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('pages.conference-registration');
    }

    public function register(Request $request)
    {
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

        // Store in database
        $registration = ConferenceRegistration::create($validated);

        // Cache the registration stats
        $this->updateRegistrationStats();

        // Store in session
        session()->flash('success', 'Registration successful! Thank you for registering for DATICAN CONFERENCE 2026.');

        return redirect()->route('conference.registration.success');
    }

    public function success()
    {
        if (!session('success')) {
            return redirect()->route('conference.registration');
        }

        return view('pages.registration-success');
    }

    public function stats()
    {
        // Cache registration stats for 5 minutes
        $stats = Cache::remember('registration_stats', 300, function () {
            return [
                'total_registrations' => ConferenceRegistration::count(),
                'total_presenters' => ConferenceRegistration::presentingPapers()->count(),
                'total_datican_members' => ConferenceRegistration::daticanMembers()->count(),
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

        return view('pages.registration-stats', compact('stats'));
    }

    private function updateRegistrationStats()
    {
        Cache::forget('registration_stats');
    }
}