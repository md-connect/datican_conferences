<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display bidding interface
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', date('Y'));
        
        // Get papers available for bidding (submitted or under review, not authored by user)
        $papers = Paper::where('conference_year', $year)
            ->where(function($query) {
                $query->where('status', 'submitted')
                      ->orWhere('status', 'under_review');
            })
            ->whereDoesntHave('authors', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with(['bids' => function($q) use ($user) {
                $q->where('reviewer_id', $user->id);
            }, 'reviews'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(12);
        
        // Calculate bidding stats
        $userBids = Bid::where('reviewer_id', $user->id)
            ->whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })
            ->get();
        
        $biddingStats = [
            'very_high' => $userBids->where('preference', 'very_high')->count(),
            'high' => $userBids->where('preference', 'high')->count(),
            'medium' => $userBids->where('preference', 'medium')->count(),
            'low' => $userBids->where('preference', 'low')->count(),
            'very_low' => $userBids->where('preference', 'very_low')->count(),
            'conflict' => $userBids->where('preference', 'conflict')->count(),
        ];
        
        return view('bidding.index', compact('papers', 'biddingStats'));
    }

    /**
     * Store or update bids
     */
    public function store(Request $request)
    {
        $request->validate([
            'paper_id' => 'required_without:bids|exists:papers,id',
            'preference' => 'required_without:bids|in:very_high,high,medium,low,very_low,conflict,no_bid',
            'comments' => 'nullable|string',
            'expertise_scores' => 'nullable|array',
            'bids' => 'nullable|array' // For bulk updates
        ]);
        
        $user = Auth::user();
        
        if ($request->has('bids')) {
            // Bulk update
            foreach ($request->bids as $bidData) {
                Bid::updateOrCreate(
                    [
                        'paper_id' => $bidData['paper_id'],
                        'reviewer_id' => $user->id
                    ],
                    [
                        'preference' => $bidData['preference'] ?? 'no_bid',
                        'comments' => $bidData['comments'] ?? null,
                        'expertise_scores' => $bidData['expertise_scores'] ?? null
                    ]
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All bids saved successfully!'
            ]);
        } else {
            // Single bid
            Bid::updateOrCreate(
                [
                    'paper_id' => $request->paper_id,
                    'reviewer_id' => $user->id
                ],
                [
                    'preference' => $request->preference,
                    'comments' => $request->comments,
                    'expertise_scores' => $request->expertise_scores
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Bid saved successfully!'
            ]);
        }
    }

    /**
     * Update a specific bid
     */
    public function update(Request $request, Bid $bid)
    {
        $this->authorize('update', $bid);
        
        $request->validate([
            'preference' => 'required|in:very_high,high,medium,low,very_low,conflict,no_bid',
            'comments' => 'nullable|string',
            'expertise_scores' => 'nullable|array'
        ]);
        
        $bid->update($request->only(['preference', 'comments', 'expertise_scores']));
        
        return response()->json([
            'success' => true,
            'message' => 'Bid updated successfully!'
        ]);
    }
}