<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\User;
use App\Models\ConferenceRegistration;
use App\Http\Requests\StorePaperRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Rules\MaxWords;
use Illuminate\Support\Facades\DB;


class PaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'index']);
        $this->authorizeResource(Paper::class, 'paper');
        
        // Additional authorization for custom methods
        $this->middleware(function ($request, $next) {
            $route = $request->route();
            $action = $route->getActionMethod();
            $paper = $route->parameter('paper');
            
            if ($action === 'submit') {
                $this->authorize('submit', $paper);
            }
            
            if ($action === 'updateStatus') {
                $this->authorize('updateStatus', $paper);
            }
            
            if ($action === 'download') {
                $this->authorize('download', $paper);
            }
            
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', date('Y'));
        
        $papers = Paper::with(['authors', 'reviews'])
            ->when(!$user->is_admin, function ($query) use ($user) {
                return $query->forUser($user->id); // your scope exists and works
            })
            ->byYear($year)
            ->latest('created_at') // <-- drafts will show first
            ->paginate(15);


        return view('papers.index', compact('papers', 'year'));
    }

    public function create()
    {
        // Check if the user has an existing draft
        $draft = Paper::where('created_by', Auth::id())
            ->where('status', 'draft')
            ->latest()
            ->first();

        if ($draft) {
            return redirect()->route('papers.edit', $draft)
                ->with('info', 'You have an existing draft. Please complete it before creating a new submission.');
        }

        $users = User::orderBy('first_name')->get();
        $registrations = ConferenceRegistration::where('email', Auth::user()->email)
            ->orWhereHas('papers', function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->get();
            
        return view('papers.create', compact('users', 'registrations'));
    }

    public function store(StorePaperRequest $request)
    {
        // The request is already validated at this point
        
        // Check for duplicate title
        $existingPaper = Paper::where('title', $request->title)
            ->where('created_by', Auth::id())
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingPaper) {
            return back()->withErrors([
                'title' => 'You already have a paper with this title. Please use a different title.'
            ])->withInput();
        }

        $paperData = [
            'title' => $request->title,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'topic_area' => $request->topic_area,
            'submission_type' => $request->submission_type,
            'author_comments' => $request->author_comments ?? null,
            'conference_year' => $request->conference_year,
            'is_anonymous' => $request->boolean('is_anonymous', true),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        // Determine status based on submission type and action
        if ($request->action === 'submit') {
            $paperData['status'] = $request->submission_type === 'abstract_only' 
                ? 'abstract_submitted' 
                : 'submitted';
            $paperData['submitted_at'] = now();
        } else {
            $paperData['status'] = 'draft';
        }

        // Handle file upload if not abstract only
        if ($request->submission_type !== 'abstract_only' && $request->hasFile('paper_file')) {
            $file = $request->file('paper_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('papers/' . $request->conference_year, $fileName, 'public');
            
            $paperData['file_path'] = $filePath;
            $paperData['file_name'] = $file->getClientOriginalName();
            $paperData['file_size'] = $file->getSize();
        }

        // Create the paper
        $paper = Paper::create($paperData);

        // Attach authors
        foreach ($request->authors as $index => $authorData) {
            $paper->authors()->attach($authorData['user_id'], [
                'is_corresponding' => $authorData['is_corresponding'] ?? ($index === 0),
                'author_order' => $index,
            ]);
        }

        // Attach registrations if provided
        if ($request->has('registration_ids')) {
            $paper->registrations()->attach($request->registration_ids);
        }

        $message = $request->action === 'submit' 
            ? ($request->submission_type === 'abstract_only' 
                ? 'Abstract submitted successfully! You can submit the full paper later.'
                : 'Paper submitted successfully! Your paper ID: ' . $paper->anonymous_id)
            : 'Paper saved as draft.';

        return redirect()->route('papers.show', $paper)
            ->with('success', $message);
    }

    public function show(Paper $paper)
    {
        // Debug - check actual status
        \Log::info('=== PAPER SHOW DEBUG ===', [
            'paper_id' => $paper->id,
            'paper_status' => $paper->status,
            'user_id' => Auth::id(),
            'is_author' => $paper->authors()->where('users.id', Auth::id())->exists()
        ]);
        
        $paper->load(['authors', 'reviews.reviewer', 'registrations']);
        
        // Get all assignments that are not declined
        $totalAssignments = ReviewAssignment::where('paper_id', $paper->id)
            ->where('status', '!=', 'declined')
            ->count();

        // Check if this is an abstract-only paper that needs full paper submission
        $canSubmitFullPaper = $paper->submission_type === 'abstract_only' && 
                            !$paper->file_path && 
                            $paper->status !== 'draft' &&
                            $paper->authors()->where('users.id', Auth::id())->exists();
        
        // Check if paper needs revision and user is author
        $needsRevision = $paper->status === 'needs_revision' && 
                        $paper->authors()->where('users.id', Auth::id())->exists();
        
        return view('papers.show', compact('paper', 'canSubmitFullPaper', 'needsRevision', 'totalAssignments'));
    }

    /**
     * Show the form for editing the specified paper.
     */
    public function edit(Paper $paper)
    {
        // Check if paper can be edited
        if (!$paper->canBeEditedBy(Auth::user())) {
            \Log::warning('Edit attempt blocked by canBeEditedBy', [
                'user_id' => Auth::id(),
                'paper_id' => $paper->id,
                'paper_status' => $paper->status,
                'is_author' => $paper->authors()->where('users.id', Auth::id())->exists()
            ]);
            abort(403, 'This paper cannot be edited at this stage.');
        }

        $users = User::orderBy('first_name')->get();
        $registrations = ConferenceRegistration::where('email', Auth::user()->email)
            ->orWhereHas('papers', function ($query) use ($paper) {
                $query->where('created_by', Auth::id());
            })
            ->get();
            
        return view('papers.edit', compact('paper', 'users', 'registrations'));
    }

    public function update(StorePaperRequest $request, Paper $paper)
    {
        if (!$paper->canBeEditedBy(Auth::user())) {
            abort(403, 'This paper cannot be edited at this stage.');
        }

        // Check for duplicate title (excluding current paper)
        $existingPaper = Paper::where('title', $request->title)
            ->where('created_by', Auth::id())
            ->where('id', '!=', $paper->id)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingPaper) {
            return back()->withErrors([
                'title' => 'You already have another paper with this title. Please use a different title.'
            ])->withInput();
        }

        $data = [
            'title' => $request->title,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'topic_area' => $request->topic_area,
            'submission_type' => $request->submission_type,
            'author_comments' => $request->author_comments,
            'is_anonymous' => $request->boolean('is_anonymous', true),
            'updated_by' => Auth::id(),
        ];

        // Handle file upload
        if ($request->hasFile('paper_file')) {
            // Delete old file if exists
            if ($paper->file_path) {
                Storage::disk('public')->delete($paper->file_path);
            }

            $file = $request->file('paper_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('papers/' . $paper->conference_year, $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            
            // If this was an abstract-only paper and now uploading full paper
            if ($paper->submission_type === 'abstract_only' && $request->submission_type === 'full_paper') {
                $data['submission_type'] = 'full_paper';
            }
        }

        // Handle submission from draft
        if ($request->action === 'submit' && $paper->status === 'draft') {
            $data['status'] = $paper->submission_type === 'abstract_only'
                ? 'abstract_submitted'
                : 'submitted';
            $data['submitted_at'] = now();
        }
        
        // Handle revision submission
        if ($request->action === 'submit_revision' && $paper->status === 'needs_revision') {
            $data['status'] = 'under_review';
            $data['revision_submitted_at'] = now();
            $data['revision_notes'] = $request->revision_notes;
            
            // Reset revision fields
            $data['needs_revision'] = false;
            $data['revision_requested_at'] = null;
        }

        $paper->update($data);

        // Update authors
        $paper->authors()->detach();
        foreach ($request->authors as $index => $authorData) {
            $paper->authors()->attach($authorData['user_id'], [
                'is_corresponding' => $authorData['is_corresponding'] ?? false,
                'author_order' => $index,
            ]);
        }

        if ($request->has('registration_ids')) {
            $paper->registrations()->sync($request->registration_ids);
        }

        $message = 'Paper updated successfully!';
        
        if ($request->action === 'submit_revision') {
            $message = 'Revision submitted successfully! The paper is now back under review.';
        } elseif ($request->action === 'submit') {
            $message = 'Paper submitted successfully!';
        }

        return redirect()->route('papers.show', $paper)
            ->with('success', $message);
    }

    public function submit(Request $request, Paper $paper)
    {
        $this->authorize('submit', $paper);

        if ($paper->status !== 'draft') {
            return back()->with('error', 'Only draft papers can be submitted.');
        }

        $paper->update([
            'status' => $paper->submission_type === 'abstract_only' ? 'abstract_submitted' : 'submitted',
            'submitted_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Paper submitted for review!');
    }

    public function download(Paper $paper)
    {
        $this->authorize('download', $paper);

        if (!Storage::disk('public')->exists($paper->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $paper->file_path, 
            $paper->anonymous_id . '_' . $paper->file_name
        );
    }

    public function updateStatus(Request $request, Paper $paper)
    {
    
        $this->authorize('updateStatus', $paper);

        $request->validate([
            'status' => 'required|in:draft,submitted,under_review,accepted,rejected,camera_ready,needs_revision,abstract_submitted',
            'decision' => 'nullable|in:accept,minor_revisions,major_revisions,reject',
            'decision_notes' => 'nullable|string',
            'revision_deadline' => 'required_if:status,needs_revision|nullable|date|after:today',
        ]);

        $updateData = [
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ];

        if ($request->has('decision')) {
            $updateData['decision'] = $request->decision;
            $updateData['decision_notes'] = $request->decision_notes;
            $updateData['decision_at'] = now();
        }

        // Handle revision request
        if ($request->status === 'needs_revision') {
            $updateData['needs_revision'] = true;
            $updateData['revision_requested_at'] = now();
            $updateData['revision_deadline'] = $request->revision_deadline;
            $updateData['revision_notes'] = $request->decision_notes;
        }

        $paper->update($updateData);

        $message = 'Paper status updated!';
        
        if ($request->status === 'needs_revision') {
            $message = 'Revision requested. Author has been notified.';
        } elseif ($request->status === 'accepted') {
            $message = 'Paper accepted! Author can now submit camera-ready version.';
        }

        return back()->with('success', $message);
    }

    public function submitFullForm(Paper $paper)
    {
        // Check if paper is abstract only
        if ($paper->submission_type !== 'abstract_only') {
            abort(403, 'Only abstract-only papers can submit full paper.');
        }
        
        // Check if user is author of this paper
        if (!$paper->authors()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Only authors can submit full paper.');
        }
        
        // Check if full paper is already submitted (has file)
        if ($paper->file_path) {
            abort(403, 'Full paper already submitted.');
        }
        
        // Check if paper is in a state that allows full paper submission
        $allowedStatuses = ['abstract_submitted', 'submitted', 'under_review', 'accepted', 'needs_revision'];
        
        if (!in_array($paper->status, $allowedStatuses)) {
            abort(403, 'Paper is not in a state that allows full paper submission.');
        }
        
        return view('papers.submit-full', compact('paper'));
    }

    public function submitFull(Request $request, Paper $paper)
    {
        // Check if paper is abstract only
        if ($paper->submission_type !== 'abstract_only') {
            abort(403, 'Only abstract-only papers can submit full paper.');
        }
        
        // Check if user is author of this paper
        if (!$paper->authors()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Only authors can submit full paper.');
        }
        
        // Check if full paper is already submitted (has file)
        if ($paper->file_path) {
            abort(403, 'Full paper already submitted.');
        }
        
        // Check if paper is in a state that allows full paper submission
        $allowedStatuses = ['abstract_submitted', 'submitted', 'under_review', 'accepted', 'needs_revision'];
        
        if (!in_array($paper->status, $allowedStatuses)) {
            abort(403, 'Paper is not in a state that allows full paper submission.');
        }

        $request->validate([
            'paper_file' => 'required|file|mimes:pdf|max:10240',
            'revision_notes' => 'nullable|string|max:1000',
        ]);

        // Upload new file
        $file = $request->file('paper_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('papers/' . $paper->conference_year, $fileName, 'public');

        // Update paper
        $updateData = [
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'submission_type' => 'full_paper', // Change from abstract_only to full_paper
            'updated_by' => Auth::id(),
            'revision_notes' => $request->revision_notes,
        ];
        
        // If paper was accepted and now submitting full paper, change status to 'camera_ready'
        if ($paper->status === 'accepted') {
            $updateData['status'] = 'camera_ready';
        } else {
            // For other statuses, keep the same status but now with full paper
            $updateData['status'] = $paper->status;
        }

        $paper->update($updateData);

        return redirect()->route('papers.show', $paper)
            ->with('success', 'Full paper submitted successfully!');
    }
    
    /**
     * Show revision form for papers that need revisions
     */
    public function reviseForm(Paper $paper)
    {
        // Check if paper needs revision
        if ($paper->status !== 'needs_revision') {
            abort(403, 'This paper does not need revision at this time.');
        }
        
        // Check if user is author of this paper
        if (!$paper->authors()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Only authors can revise this paper.');
        }
        
        // Check if revision deadline has passed
        if ($paper->revision_deadline && now()->gt($paper->revision_deadline)) {
            return redirect()->route('papers.show', $paper)
                ->with('error', 'Revision deadline has passed. Please contact the conference chair.');
        }
        
        $paper->load(['authors', 'reviews' => function($query) {
            $query->where('status', 'completed')->with('reviewer');
        }]);
        
        $users = User::orderBy('first_name')->get();
        
        return view('papers.revise', compact('paper', 'users'));
    }
    
    /**
     * Submit revision
     */
    public function submitRevision(Request $request, Paper $paper)
    {
        // Check if paper needs revision
        if ($paper->status !== 'needs_revision') {
            abort(403, 'This paper does not need revision at this time.');
        }
        
        // Check if user is author of this paper
        if (!$paper->authors()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Only authors can revise this paper.');
        }
        
        // Check if revision deadline has passed
        if ($paper->revision_deadline && now()->gt($paper->revision_deadline)) {
            return redirect()->route('papers.show', $paper)
                ->with('error', 'Revision deadline has passed. Please contact the conference chair.');
        }
        
        $request->validate([
            'abstract' => 'required|string',
            'keywords' => 'required|string',
            'topic_area' => 'required|string',
            'author_comments' => 'nullable|string',
            'paper_file' => 'nullable|file|mimes:pdf|max:10240',
            'revision_notes' => 'required|string|max:2000',
            'authors' => 'required|array|min:1',
            'authors.*.user_id' => 'required|exists:users,id',
            'authors.*.is_corresponding' => 'nullable|boolean',
        ]);

        // Check for duplicate title (excluding current paper)
        $existingPaper = Paper::where('title', $paper->title)
            ->where('created_by', Auth::id())
            ->where('id', '!=', $paper->id)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingPaper) {
            return back()->withErrors([
                'title' => 'You already have another paper with this title. Please use a different title.'
            ])->withInput();
        }

        $data = [
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'topic_area' => $request->topic_area,
            'author_comments' => $request->author_comments,
            'updated_by' => Auth::id(),
            'status' => 'under_review',
            'revision_submitted_at' => now(),
            'revision_notes' => $request->revision_notes,
            'needs_revision' => false,
            'revision_requested_at' => null,
            'revision_deadline' => null,
        ];

        // Handle file upload if provided
        if ($request->hasFile('paper_file')) {
            // Delete old file if exists
            if ($paper->file_path) {
                Storage::disk('public')->delete($paper->file_path);
            }

            $file = $request->file('paper_file');
            $fileName = time() . '_rev_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('papers/' . $paper->conference_year, $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $paper->update($data);

        // Update authors
        $paper->authors()->detach();
        foreach ($request->authors as $index => $authorData) {
            $paper->authors()->attach($authorData['user_id'], [
                'is_corresponding' => $authorData['is_corresponding'] ?? false,
                'author_order' => $index,
            ]);
        }

        return redirect()->route('papers.show', $paper)
            ->with('success', 'Revision submitted successfully! The paper is now back under review.');
    }
}