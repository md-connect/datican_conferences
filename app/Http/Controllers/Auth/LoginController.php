<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ConferenceRegistration;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Log login activity
            \Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'is_reviewer' => $user->is_reviewer
            ]);
            
            // ========== ADMIN ==========
            if ($user->is_admin) {
                return redirect()->route('admin.conference.dashboard')
                    ->with('success', 'Welcome back, Administrator!');
            } 
            
            // ========== REVIEWER ==========
            if ($user->is_reviewer) {
                // Check if reviewer has pending assignments
                $pendingCount = \App\Models\ReviewAssignment::where('reviewer_id', $user->id)
                    ->where('status', 'pending')
                    ->count();
                
                if ($pendingCount > 0) {
                    return redirect()->route('dashboard')
                        ->with('info', "Welcome back, Reviewer! You have {$pendingCount} pending review assignments.");
                }
                
                return redirect()->route('dashboard')
                    ->with('success', 'Welcome back, Reviewer!');
            }
            
            // ========== REGULAR USERS ==========
            // Check if user has conference registration
            $conferenceRegistration = ConferenceRegistration::where('email', $user->email)
                ->orWhere('user_id', $user->id)
                ->first();
            
            // Check if user has papers
            $paperCount = $user->papers()->count();
            
            // Check if user has ever had conference registration
            $hasEverRegistered = ConferenceRegistration::where('email', $user->email)
                ->orWhere('user_id', $user->id)
                ->exists();
            
            // Get the dashboard route based on user status
            $dashboardRoute = $this->determineDashboardRoute($user, $conferenceRegistration, $paperCount, $hasEverRegistered);
            
            // Prepare welcome message
            $message = $this->prepareWelcomeMessage($user, $conferenceRegistration, $paperCount);
            
            // Redirect to appropriate dashboard
            if ($dashboardRoute === 'dashboard.author') {
                return redirect()->route('dashboard')
                    ->with('success', $message);
            } else if ($dashboardRoute === 'dashboard.user') {
                return redirect()->route('dashboard')
                    ->with('success', $message);
            } else if ($dashboardRoute === 'dashboard.chair') {
                return redirect()->route('dashboard')
                    ->with('success', $message);
            }
            
            // Default fallback
            return redirect()->route('dashboard')
                ->with('success', $message);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    /**
     * Determine which dashboard route to use based on user status
     */
    private function determineDashboardRoute($user, $conferenceRegistration, $paperCount, $hasEverRegistered)
    {
        // If user has active conference registration
        if ($conferenceRegistration) {
            // Check if user is presenting a paper
            if ($conferenceRegistration->is_presenting_paper || $paperCount > 0) {
                return 'dashboard.author';
            }
            
            // User is registered but not presenting - check primary role
            if ($conferenceRegistration->primary_role === 'chair' || $conferenceRegistration->primary_role === 'program_chair') {
                return 'dashboard.chair';
            }
            
            // Registered attendee
            return 'dashboard.user';
        }
        
        // User without conference registration
        return 'dashboard.user';
    }
    
    /**
     * Prepare appropriate welcome message based on user status
     */
    private function prepareWelcomeMessage($user, $conferenceRegistration, $paperCount)
    {
        $message = 'Welcome back, ' . $user->first_name . '! ';
        
        if ($conferenceRegistration) {
            if ($paperCount > 0) {
                $message .= 'You have conference registration and ' . $paperCount . ' submitted paper(s).';
            } else if ($conferenceRegistration->is_presenting_paper) {
                $message .= 'You are registered to present at the conference. Ready to submit your paper?';
            } else {
                $message .= 'You are registered for the conference.';
                
                // Add role-specific message
                if ($conferenceRegistration->primary_role === 'chair') {
                    $message .= ' You have chair access.';
                } else if ($conferenceRegistration->primary_role === 'attendee') {
                    $message .= ' You are registered as an attendee.';
                }
            }
        } else {
            if ($paperCount > 0) {
                $message .= 'You have ' . $paperCount . ' submitted paper(s). Consider registering for the conference to present.';
            } else {
                $message .= 'Ready to explore the conference portal? Register to submit papers or attend.';
            }
        }
        
        return $message;
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            \Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}