<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\User;
use App\Models\ConferenceRegistration;
use App\Http\Requests\StorePaperRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $file = $request->file('paper_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('papers/' . $request->conference_year, $fileName, 'public');

        $paper = Paper::create([
            'title' => $request->title,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'topic_area' => $request->topic_area,
            'submission_type' => $request->submission_type,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'author_comments' => $request->author_comments,
            'conference_year' => $request->conference_year,
            'is_anonymous' => $request->boolean('is_anonymous', true),
            'status' => $request->action === 'submit' ? 'submitted' : 'draft',
            'submitted_at' => $request->action === 'submit' ? now() : null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        foreach ($request->authors as $index => $authorData) {
            $paper->authors()->attach($authorData['user_id'], [
                'is_corresponding' => $authorData['is_corresponding'] ?? false,
                'author_order' => $index,
            ]);
        }

        if ($request->has('registration_ids')) {
            $paper->registrations()->attach($request->registration_ids);
        }

        return redirect()->route('papers.show', $paper)
            ->with('success', $request->action === 'submit' 
                ? 'Paper submitted successfully! Your paper ID: ' . $paper->anonymous_id
                : 'Paper saved as draft.');
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

        $users = User::orderBy('first_name')->get();
        $registrations = ConferenceRegistration::whereIn('email', 
            $paper->authors->pluck('email')
        )->get();

        return view('papers.edit', compact('paper', 'users', 'registrations'));
    }

    public function update(StorePaperRequest $request, Paper $paper)
    {
        if (!$paper->canBeEditedBy(Auth::user())) {
            abort(403, 'This paper cannot be edited at this stage.');
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
            $data['status'] = 'submitted';
            $data['submitted_at'] = now();
        }

        $paper->update($data);

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
}