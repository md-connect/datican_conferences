<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Paper;
use App\Models\ReviewAssignment;
use App\Models\Bid;
use App\Models\ConferenceRegistration;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            return view('dashboard.admin');
        }
    
    // If chair, show chair dashboard
    if ($user->is_chair) {
        return app(ChairController::class)->dashboard(new Request());
    }
    
    if ($user->is_reviewer) {
        return $this->reviewerDashboard();
    }
    
    // Determine if user has conference registration or papers
    return $this->determineUserDashboard($user);
    }
    
    /**
     * Determine which dashboard to show for regular users
     */
    protected function determineUserDashboard($user)
    {
        // Check if user has conference registration
        $conferenceRegistration = ConferenceRegistration::where('email', $user->email)
            ->orWhere('user_id', $user->id)
            ->first();
        
        // Check if user has papers
        $hasPapers = $user->papers()->exists();
        
        // If user has conference registration
        if ($conferenceRegistration) {
            // Check if user is presenting paper or has papers
            if ($conferenceRegistration->is_presenting_paper || $hasPapers) {
                return $this->authorDashboard($conferenceRegistration);
            }
            
            // If user has conference registration but no papers, show user dashboard
            return $this->userDashboard($conferenceRegistration);
        }
        
        // If user has papers but no conference registration, show author dashboard
        if ($hasPapers) {
            return $this->authorDashboard(null);
        }
        
        // User without conference registration and without papers
        return $this->userDashboard(null);
    }
    
    /**
     * Chair Dashboard (for admins and chairs)
     */
    public function chairDashboard()
    {
        $user = Auth::user();
        
        // Check if user has chair or admin privileges
        if (!$user->is_chair && !$user->is_admin) {
            abort(403, 'You do not have chair privileges.');
        }
        
        // CMT System Stats
        $cmtStats = [
            'total_papers' => Paper::count(),
            'papers_submitted' => Paper::whereIn('status', ['submitted', 'under_review'])->count(),
            'papers_accepted' => Paper::where('status', 'accepted')->count(),
            'papers_rejected' => Paper::where('status', 'rejected')->count(),
            'active_reviewers' => \App\Models\User::where('is_reviewer', true)->count(),
            'pending_reviews' => ReviewAssignment::where('status', 'pending')->count(),
        ];
        
        // Conference Registration Stats
        $conferenceStats = [
            'total_registrations' => ConferenceRegistration::count(),
            'total_presenters' => ConferenceRegistration::where('is_presenting_paper', true)->count(),
            'total_datican_members' => ConferenceRegistration::where('is_datican_member', true)->count(),
        ];
        
        // Recent activity
        $recentPapers = Paper::latest()->take(5)->get();
        $recentRegistrations = ConferenceRegistration::latest()->take(5)->get();
        
        return view('dashboard.chair', compact(
            'user', 'cmtStats', 'conferenceStats', 'recentPapers', 'recentRegistrations'
        ));
    }
    
    /**
     * Old admin dashboard (renamed for clarity)
     */
    protected function unifiedAdminDashboard()
    {
        // This method is now redundant since chairDashboard handles both
        return $this->chairDashboard();
    }
    
    public function reviewerDashboard()
    {
        $reviewerId = auth()->id();
        
        // Get all review assignments for this reviewer
        $reviewAssignments = ReviewAssignment::where('reviewer_id', $reviewerId)
            ->with(['paper' => function($query) {
                $query->select('id', 'anonymous_id', 'title', 'topic_area', 'submission_type');
            }])
            ->get();
        
        // Calculate basic stats
        $reviewStats = [
            'assigned' => $reviewAssignments->where('status', 'pending')->count(),
            'in_progress' => $reviewAssignments->where('status', 'accepted')->count() + 
                            $reviewAssignments->where('status', 'in_progress')->count(),
            'completed' => $reviewAssignments->where('status', 'completed')->count(),
            'overdue' => $reviewAssignments->where(function($assignment) {
                return in_array($assignment->status, ['accepted', 'in_progress']) && 
                    $assignment->deadline && 
                    $assignment->deadline->isPast();
            })->count(),
        ];
        
        // Calculate performance metrics (only if there are completed reviews)
        $completedReviews = $reviewAssignments->where('status', 'completed');
        $totalAssigned = $reviewAssignments->whereIn('status', ['accepted', 'in_progress', 'completed'])->count();
        
        if ($completedReviews->count() > 0) {
            // Calculate average rating
            $reviewStats['avg_rating'] = round($completedReviews->avg('overall_score'), 1);
            
            // Calculate on-time percentage
            $onTimeReviews = $completedReviews->filter(function($review) {
                return $review->deadline && $review->submitted_at && 
                    $review->submitted_at <= $review->deadline;
            })->count();
            
            $reviewStats['on_time_percentage'] = $completedReviews->count() > 0 ? 
                round(($onTimeReviews / $completedReviews->count()) * 100) : 0;
            
            // Calculate completion rate (completed vs assigned+in_progress)
            $reviewStats['completion_rate'] = $totalAssigned > 0 ? 
                round(($completedReviews->count() / $totalAssigned) * 100) : 0;
        } else {
            // Set default values when no completed reviews
            $reviewStats['avg_rating'] = 'N/A';
            $reviewStats['on_time_percentage'] = 0;
            $reviewStats['completion_rate'] = 0;
        }
        
        // Get active reviews (pending, accepted, in_progress)
        $activeReviews = $reviewAssignments->whereIn('status', ['pending', 'accepted', 'in_progress']);
        
        // Get available papers for bidding (exclude already assigned)
        $assignedPaperIds = $reviewAssignments->pluck('paper_id')->toArray();
        $availablePapers = Paper::where('status', 'under_review')
            ->whereNotIn('id', $assignedPaperIds)
            ->take(5)
            ->get();
        
        return view('dashboard.reviewer', compact('reviewStats', 'activeReviews', 'availablePapers'));
    }
    
    public function authorDashboard($conferenceRegistration = null)
    {
        $user = auth()->user();
        
        $data = [
            'paperStats' => [
                'total' => $user->papers()->count(),
                'submitted' => $user->papers()->where('status', 'submitted')->count(),
                'under_review' => $user->papers()->where('status', 'under_review')->count(),
                'accepted' => $user->papers()->where('status', 'accepted')->count(),
                'rejected' => $user->papers()->where('status', 'rejected')->count(),
                'camera_ready' => $user->papers()->where('status', 'camera_ready')->count(),
            ],
            'conferenceRegistration' => $conferenceRegistration,
            'recentPapers' => $user->papers()->latest()->take(5)->get(),
            'recentReviews' => ReviewAssignment::whereHas('paper.authors', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('reviewer', 'paper')
            ->latest()
            ->take(5)
            ->get(),
            'dashboardType' => 'author',
        ];
        
        return view('dashboard.author', $data);
    }
    
    /**
     * User Dashboard for users without conference registration or papers
     */
    protected function userDashboard($conferenceRegistration = null)
    {
        $user = Auth::user();
        
        $hasRegistration = !is_null($conferenceRegistration);
        
        $data = [
            'user' => $user,
            'conferenceRegistration' => $conferenceRegistration,
            'hasRegistration' => $hasRegistration,
            'hasPapers' => $user->papers()->exists(),
            'paperCount' => $user->papers()->count(),
            'dashboardType' => 'user',
        ];
        
        return view('dashboard.user', $data);
    }

    
}