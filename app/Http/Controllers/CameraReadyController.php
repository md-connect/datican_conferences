<?php

namespace App\Http\Controllers;

use App\Models\CameraReady;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CameraReadyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display camera ready submission form
     */
    public function show(Paper $paper)
    {
        // Check if user can submit camera ready
        if (!$paper->authors()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Only authors can submit camera ready versions.');
        }
        
        if (!in_array($paper->status, ['accepted', 'camera_ready'])) {
            abort(403, 'Only accepted papers can submit camera ready versions.');
        }
        
        $cameraReady = CameraReady::where('paper_id', $paper->id)->first();
        
        return view('camera-ready.show', compact('paper', 'cameraReady'));
    }

    /**
     * Store camera ready submission
     */
    public function store(Request $request, Paper $paper)
    {
        // Authorization check
        if (!$paper->authors()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Only authors can submit camera ready versions.');
        }
        
        if (!in_array($paper->status, ['accepted', 'camera_ready'])) {
            abort(403, 'Only accepted papers can submit camera ready versions.');
        }
        
        $request->validate([
            'camera_ready_file' => 'required|file|mimes:pdf,docx,zip|max:10240',
            'format' => 'required|in:pdf,docx,latex',
            'changes_summary' => 'required|string|min:50',
            'copyright_signed' => 'required|accepted',
            'copyright_form' => 'nullable|file|mimes:pdf|max:5120'
        ]);
        
        // Handle file upload
        $file = $request->file('camera_ready_file');
        $fileName = time() . '_cameraready_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('camera_ready/' . $paper->conference_year, $fileName, 'public');
        
        $data = [
            'paper_id' => $paper->id,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'format' => $request->format,
            'changes_summary' => $request->changes_summary,
            'copyright_signed' => true,
            'status' => 'submitted',
            'submitted_by' => Auth::id(),
            'submitted_at' => now()
        ];
        
        // Handle copyright form if uploaded
        if ($request->hasFile('copyright_form')) {
            $copyrightFile = $request->file('copyright_form');
            $copyrightFileName = time() . '_copyright_' . $copyrightFile->getClientOriginalName();
            $copyrightFilePath = $copyrightFile->storeAs('copyrights/' . $paper->conference_year, $copyrightFileName, 'public');
            $data['copyright_form_path'] = $copyrightFilePath;
        }
        
        // Create or update camera ready submission
        $cameraReady = CameraReady::updateOrCreate(
            ['paper_id' => $paper->id],
            $data
        );
        
        // Update paper status
        $paper->update(['status' => 'camera_ready']);
        
        return redirect()->route('camera-ready.show', $paper)
            ->with('success', 'Camera ready version submitted successfully!');
    }

    /**
     * Approve camera ready submission (Chair only)
     */
    public function approve(CameraReady $cameraReady)
    {
        $this->authorize('admin', Paper::class);
        
        if ($cameraReady->status !== 'submitted') {
            return back()->with('error', 'Only submitted camera ready versions can be approved.');
        }
        
        $cameraReady->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);
        
        return back()->with('success', 'Camera ready version approved!');
    }

    /**
     * Reject camera ready submission (Chair only)
     */
    public function reject(Request $request, CameraReady $cameraReady)
    {
        $this->authorize('admin', Paper::class);
        
        $request->validate([
            'rejection_reason' => 'required|string|min:20'
        ]);
        
        $cameraReady->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);
        
        return back()->with('success', 'Camera ready version rejected. Author will be asked to resubmit.');
    }

    /**
     * Download camera ready file
     */
    public function download(CameraReady $cameraReady)
    {
        // Check if user can download
        $paper = $cameraReady->paper;
        $user = Auth::user();
        
        if (!$user->is_admin && !$paper->authors()->where('users.id', $user->id)->exists()) {
            abort(403, 'You do not have permission to download this file.');
        }
        
        if (!Storage::disk('public')->exists($cameraReady->file_path)) {
            abort(404, 'File not found.');
        }
        
        return Storage::disk('public')->download(
            $cameraReady->file_path,
            $paper->anonymous_id . '_cameraready_' . $cameraReady->file_name
        );
    }
}