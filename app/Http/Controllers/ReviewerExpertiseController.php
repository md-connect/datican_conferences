<?php

namespace App\Http\Controllers;

use App\Models\ReviewerExpertise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerExpertiseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the expertise management form
     */
    public function index()
    {
        $expertiseAreas = ReviewerExpertise::where('user_id', Auth::id())->get();
        
        // Define available topic areas
        $topicAreas = [
            'ai_ml' => 'Artificial Intelligence & Machine Learning',
            'data_science' => 'Data Science & Analytics',
            'healthcare' => 'Healthcare Applications',
            'clinical' => 'Clinical Decision Support',
            'imaging' => 'Medical Imaging',
            'other' => 'Other'
        ];
        
        $expertiseLevels = [
            'expert' => 'Expert (I have published extensively in this area)',
            'proficient' => 'Proficient (I have significant experience)',
            'familiar' => 'Familiar (I have working knowledge)',
            'basic' => 'Basic (I have some understanding)'
        ];
        
        $confidenceLevels = [
            5 => 'Very High - I am certain of my expertise',
            4 => 'High - I am confident',
            3 => 'Moderate - I have reasonable knowledge',
            2 => 'Low - I have limited knowledge',
            1 => 'Very Low - I have minimal knowledge'
        ];
        
        return view('reviewer.expertise', compact('expertiseAreas', 'topicAreas', 'expertiseLevels', 'confidenceLevels'));
    }

    /**
     * Store a new expertise area
     */
    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'level' => 'required|in:expert,proficient,familiar,basic',
            'confidence' => 'required|integer|min:1|max:5',
        ]);

        // Check if expertise already exists for this topic
        $existing = ReviewerExpertise::where('user_id', Auth::id())
            ->where('topic', $request->topic)
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have expertise in this topic. You can edit it instead.');
        }

        ReviewerExpertise::create([
            'user_id' => Auth::id(),
            'topic' => $request->topic,
            'level' => $request->level,
            'confidence' => $request->confidence,
        ]);

        return redirect()->route('reviewer.expertise')
            ->with('success', 'Expertise area added successfully.');
    }

    /**
     * Update an expertise area
     */
    public function update(Request $request, $id)
    {
        $expertise = ReviewerExpertise::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'topic' => 'required|string|max:255',
            'level' => 'required|in:expert,proficient,familiar,basic',
            'confidence' => 'required|integer|min:1|max:5',
        ]);

        $expertise->update([
            'topic' => $request->topic,
            'level' => $request->level,
            'confidence' => $request->confidence,
        ]);

        return redirect()->route('reviewer.expertise')
            ->with('success', 'Expertise area updated successfully.');
    }

    /**
     * Delete an expertise area
     */
    public function destroy($id)
    {
        $expertise = ReviewerExpertise::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $expertise->delete();

        return redirect()->route('reviewer.expertise')
            ->with('success', 'Expertise area removed successfully.');
    }
}