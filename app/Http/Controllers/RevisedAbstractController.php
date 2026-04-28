<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RevisedAbstractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show paper selection page for revised abstract upload
     */
    public function selectPaper()
    {
        $papers = Paper::where('created_by', Auth::id())
            ->whereIn('decision', ['accept_with_minor_revision', 'accept_with_major_revision'])
            ->whereNull('revised_abstract_file_path')
            ->get();

        return view('author.revised-abstract.select', compact('papers'));
    }

    /**
     * Show upload form for a specific paper
     */
    public function showUploadForm(Paper $paper)
    {
        // Verify ownership
        if ($paper->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if already uploaded
        if ($paper->revised_abstract_file_path) {
            return redirect()->route('dashboard')
                ->with('error', 'You have already uploaded the revised abstract for this paper.');
        }

        // Check if paper requires revision
        if (!in_array($paper->decision, ['accept_with_minor_revision', 'accept_with_major_revision'])) {
            return redirect()->route('author.dashboard')
                ->with('error', 'This paper does not require revision.');
        }

        return view('author.revised-abstract.upload', compact('paper'));
    }

    /**
     * Process the revised abstract upload
     */
    public function upload(Request $request, Paper $paper)
    {
        // Verify ownership
        if ($paper->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Validate request
        $request->validate([
            'revised_abstract' => 'required|file|mimes:doc,docx|max:5120', // 5MB max
        ]);

        // Check if already uploaded
        if ($paper->revised_abstract_file_path) {
            return redirect()->back()->with('error', 'Revised abstract already uploaded.');
        }

        try {
            // Delete old file if exists
            if ($paper->revised_abstract_file_path && Storage::disk('public')->exists($paper->revised_abstract_file_path)) {
                Storage::disk('public')->delete($paper->revised_abstract_file_path);
            }

            // Upload new file
            $file = $request->file('revised_abstract');
            $filename = 'revised_abstract_' . $paper->anonymous_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('revised_abstracts/' . $paper->id, $filename, 'public');

            // Update paper record
            $paper->revised_abstract_file_path = $path;
            $paper->revised_abstract_file_name = $file->getClientOriginalName();
            $paper->revised_abstract_file_size = $file->getSize();
            $paper->revised_abstract_uploaded_at = now();
            
            // You can also extract content from DOCX if needed (requires additional package)
            // $paper->revised_abstract_content = $this->extractDocxContent($path);
            
            $paper->save();

            \Log::info('Revised abstract uploaded', [
                'paper_id' => $paper->id,
                'paper_title' => $paper->title,
                'author_id' => Auth::id(),
                'file_path' => $path
            ]);

            return redirect()->route('author.dashboard')
                ->with('success', 'Revised abstract uploaded successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to upload revised abstract', [
                'paper_id' => $paper->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to upload revised abstract: ' . $e->getMessage());
        }
    }

    /**
     * Download the revised abstract
     */
    public function download(Paper $paper)
    {
        // Verify ownership
        if ($paper->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if (!$paper->revised_abstract_file_path) {
            return redirect()->back()->with('error', 'No revised abstract found.');
        }

        $filePath = storage_path('app/public/' . $paper->revised_abstract_file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $paper->revised_abstract_file_name);
    }
}