<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display discussions for a paper
     */
    public function index(Paper $paper)
    {
        // Check if user can view discussions for this paper
        if (!$this->canViewPaperDiscussions($paper)) {
            abort(403, 'You do not have permission to view discussions for this paper.');
        }
        
        $discussions = Discussion::where('paper_id', $paper->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('discussions.paper', compact('paper', 'discussions'));
    }

    /**
     * Store a new discussion
     */
    public function store(Request $request)
    {
        $request->validate([
            'paper_id' => 'required|exists:papers,id',
            'content' => 'required|string|min:10',
            'type' => 'required|in:general,review,rebuttal,decision,meta',
            'visibility' => 'required|in:public,reviewers,chairs,authors',
            'parent_id' => 'nullable|exists:discussions,id'
        ]);
        
        $paper = Paper::findOrFail($request->paper_id);
        
        // Check permissions based on visibility
        if (!$this->canPostDiscussion($paper, $request->visibility)) {
            abort(403, 'You do not have permission to post discussions with this visibility level.');
        }
        
        $discussion = Discussion::create([
            'paper_id' => $paper->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'type' => $request->type,
            'visibility' => $request->visibility
        ]);
        
        // Add participants based on visibility
        $this->addParticipants($discussion, $paper, $request->visibility);
        
        $redirectRoute = $request->parent_id 
            ? url()->previous() . '#discussion-' . $request->parent_id
            : route('discussions.paper', $paper);
        
        return redirect($redirectRoute)
            ->with('success', 'Discussion posted successfully!');
    }

    /**
     * Display a specific discussion
     */
    public function show(Discussion $discussion)
    {
        $this->authorize('view', $discussion);
        
        $discussion->load(['paper', 'user', 'replies.user', 'participants']);
        return view('discussions.show', compact('discussion'));
    }

    /**
     * Update a discussion
     */
    public function update(Request $request, Discussion $discussion)
    {
        $this->authorize('update', $discussion);
        
        $request->validate([
            'content' => 'required|string|min:10',
            'type' => 'required|in:general,review,rebuttal,decision,meta',
            'visibility' => 'required|in:public,reviewers,chairs,authors'
        ]);
        
        $discussion->update($request->only(['content', 'type', 'visibility']));
        
        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Discussion updated successfully!');
    }

    /**
     * Delete a discussion
     */
    public function destroy(Discussion $discussion)
    {
        $this->authorize('delete', $discussion);
        
        $paperId = $discussion->paper_id;
        $discussion->delete();
        
        return redirect()->route('discussions.paper', $paperId)
            ->with('success', 'Discussion deleted successfully!');
    }

    /**
     * Toggle resolved status
     */
    public function resolve(Discussion $discussion)
    {
        $this->authorize('resolve', $discussion);
        
        $discussion->update([
            'is_resolved' => !$discussion->is_resolved
        ]);
        
        return response()->json([
            'success' => true,
            'is_resolved' => $discussion->is_resolved,
            'message' => $discussion->is_resolved ? 'Discussion marked as resolved.' : 'Discussion re-opened.'
        ]);
    }

    /**
     * Helper: Check if user can view paper discussions
     */
    private function canViewPaperDiscussions(Paper $paper)
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            return true; // Chairs can view all discussions
        }
        
        if ($paper->authors()->where('users.id', $user->id)->exists()) {
            return true; // Authors can view their paper's discussions
        }
        
        if ($paper->reviews()->where('reviewer_id', $user->id)->exists()) {
            return true; // Reviewers can view discussions for papers they're reviewing
        }
        
        return false;
    }

    /**
     * Helper: Check if user can post discussion with given visibility
     */
    private function canPostDiscussion(Paper $paper, $visibility)
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            return true; // Chairs can post any visibility
        }
        
        if ($visibility === 'authors' && $paper->authors()->where('users.id', $user->id)->exists()) {
            return true; // Authors can post to authors-only discussions
        }
        
        if ($visibility === 'reviewers' && $paper->reviews()->where('reviewer_id', $user->id)->exists()) {
            return true; // Reviewers can post to reviewers-only discussions
        }
        
        if ($visibility === 'public') {
            return true; // Anyone can post public discussions if they have access
        }
        
        return false;
    }

    /**
     * Helper: Add participants to discussion based on visibility
     */
    private function addParticipants(Discussion $discussion, Paper $paper, $visibility)
    {
        $participants = [];
        
        if ($visibility === 'chairs' || $visibility === 'public') {
            // Add all chairs
            $chairs = User::where('is_admin', true)->get();
            foreach ($chairs as $chair) {
                $participants[$chair->id] = ['role' => 'chair'];
            }
        }
        
        if ($visibility === 'authors' || $visibility === 'public') {
            // Add all authors
            foreach ($paper->authors as $author) {
                $participants[$author->id] = ['role' => 'author'];
            }
        }
        
        if ($visibility === 'reviewers' || $visibility === 'public') {
            // Add all reviewers
            foreach ($paper->reviews as $review) {
                $participants[$review->reviewer_id] = ['role' => 'reviewer'];
            }
        }
        
        // Add the discussion creator
        $participants[Auth::id()] = ['role' => 'author'];
        
        // Sync participants
        $discussion->participants()->sync($participants);
    }
}