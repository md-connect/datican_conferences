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
                return $query->forUser($user->id);
            })
            ->byYear($year)
            ->latest('submitted_at')
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
            return redirect()->route('papers.edit', $draft);
        }

        $users = User::orderBy('first_name')->get();
        $registrations = ConferenceRegistration::where('email', Auth::user()->email)
            ->orWhereHas('papers', function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->get();
            
        return view('papers.create', compact('users', 'registrations'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $rules = [
            'title' => 'required|string|max:255',
            'abstract' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount > 250) {
                        $fail('The abstract must not exceed 250 words.');
                    }
                }
            ],
            'keywords' => 'required|string',
            'topic_area' => 'required|string',
            'submission_type' => 'required|in:abstract_only,full_paper',
            'conference_year' => 'required|string',
            'author_comments' => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
            'authors' => 'required|array|min:1',
            'authors.*.user_id' => 'required|exists:users,id',
            'corresponding_author' => 'required|integer|min:0', // Radio button for corresponding author
            'registration_ids' => 'nullable|array',
            'registration_ids.*' => 'exists:conference_registrations,id',
        ];

        // Conditionally add file validation for full paper submissions
        if ($request->submission_type === 'full_paper') {
            $rules['paper_file'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $rules['paper_file'] = 'nullable|file|mimes:pdf|max:10240';
        }

        // Validate the request
        $validated = $request->validate($rules);

        $paperData = [
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'],
            'topic_area' => $validated['topic_area'],
            'submission_type' => $validated['submission_type'],
            'author_comments' => $validated['author_comments'] ?? null,
            'conference_year' => $validated['conference_year'],
            'is_anonymous' => $request->boolean('is_anonymous', true),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        // Determine status based on submission type and action
        if ($request->action === 'submit') {
            $paperData['status'] = $validated['submission_type'] === 'abstract_only' 
                ? 'abstract_submitted' 
                : 'submitted';
            $paperData['submitted_at'] = now();
        } else {
            $paperData['status'] = 'draft';
        }

        // Handle file upload if not abstract only
        if ($validated['submission_type'] !== 'abstract_only' && $request->hasFile('paper_file')) {
            $file = $request->file('paper_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('papers/' . $validated['conference_year'], $fileName, 'public');
            
            $paperData['file_path'] = $filePath;
            $paperData['file_name'] = $file->getClientOriginalName();
            $paperData['file_size'] = $file->getSize();
        }

        // Create the paper
        $paper = Paper::create($paperData);

        // Attach authors with corresponding author flag from radio button
        foreach ($validated['authors'] as $index => $authorData) {
            $isCorresponding = ($index == $validated['corresponding_author']);
            
            $paper->authors()->attach($authorData['user_id'], [
                'is_corresponding' => $isCorresponding,
                'author_order' => $index,
            ]);
        }

        // Attach registrations if provided
        if (!empty($validated['registration_ids'])) {
            $paper->registrations()->attach($validated['registration_ids']);
        }

        // Success message
        $message = $request->action === 'submit' 
            ? ($validated['submission_type'] === 'abstract_only' 
                ? 'Abstract submitted successfully! You can submit the full paper later.'
                : 'Paper submitted successfully! Your paper ID: ' . $paper->anonymous_id)
            : 'Paper saved as draft.';

        return redirect()->route('papers.show', $paper)
            ->with('success', $message);
    }

    public function show(Paper $paper)
    {
        $paper->load(['authors', 'reviews.reviewer', 'registrations']);
        return view('papers.show', compact('paper'));
    }

   public function edit(Paper $paper)
    {
        if (!$paper->canBeEditedBy(Auth::user())) {
            abort(403, 'This paper cannot be edited at this stage.');
        }

        $paper->load('authors'); // ensure authors relationship is loaded
        $users = User::orderBy('first_name')->get();
        $registrations = ConferenceRegistration::whereIn('email', $paper->authors->pluck('email'))->get();

        return view('papers.edit', compact('paper', 'users', 'registrations'));
    }


    public function update(Request $request, Paper $paper)
    {
        if (!$paper->canBeEditedBy(Auth::user())) {
            abort(403, 'This paper cannot be edited at this stage.');
        }

        // Create validation rules array
        $rules = [
            'title' => 'required|string|max:255',
            'abstract' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount > 250) {
                        $fail('The abstract must not exceed 250 words.');
                    }
                }
            ],
            'keywords' => 'required|string',
            'topic_area' => 'required|string',
            'submission_type' => 'required|in:abstract_only,full_paper',
            'author_comments' => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
            'authors' => 'required|array|min:1',
            'authors.*.user_id' => 'required|exists:users,id',
            'corresponding_author' => 'required|integer|min:0', // Radio button for corresponding author
        ];

        // Conditionally add file validation for full paper submissions
        if ($request->submission_type === 'full_paper') {
            $rules['paper_file'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $rules['paper_file'] = 'nullable|file|mimes:pdf|max:10240';
        }

        $validated = $request->validate($rules);

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

        if ($request->hasFile('paper_file')) {
            Storage::disk('public')->delete($paper->file_path);

            $file = $request->file('paper_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('papers/' . $paper->conference_year, $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        if ($request->action === 'submit' && $paper->status === 'draft') {
            $data['status'] = $paper->submission_type === 'abstract_only'
                ? 'abstract_submitted'
                : 'submitted';

            $data['submitted_at'] = now();
        }

        $paper->update($data);

        // Update authors with corresponding author flag from radio button
        $paper->authors()->detach();
        foreach ($validated['authors'] as $index => $authorData) {
            $isCorresponding = ($index == $validated['corresponding_author']);
            
            $paper->authors()->attach($authorData['user_id'], [
                'is_corresponding' => $isCorresponding,
                'author_order' => $index,
            ]);
        }

        if ($request->has('registration_ids')) {
            $paper->registrations()->sync($request->registration_ids);
        }

        return redirect()->route('papers.show', $paper)
            ->with('success', 'Paper updated successfully!');
    }

    public function submit(Request $request, Paper $paper)
    {
        $this->authorize('submit', $paper);

        if ($paper->status !== 'draft') {
            return back()->with('error', 'Only draft papers can be submitted.');
        }

        $paper->update([
            'status' => 'submitted',
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
            'status' => 'required|in:draft,submitted,under_review,accepted,rejected,camera_ready',
            'decision' => 'nullable|in:accept,minor_revisions,major_revisions,reject',
            'decision_notes' => 'nullable|string',
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

        $paper->update($updateData);

        return back()->with('success', 'Paper status updated!');
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
        // Allow submission even if paper is accepted (for camera ready version)
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
}