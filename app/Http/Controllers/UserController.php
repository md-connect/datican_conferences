<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Remove the admin-only middleware, we'll handle permissions in methods
    }
    
    /**
     * List all users (admin only)
     */
    public function index(Request $request)
    {
        // Only admin can view all users
        if (!auth()->user()->is_admin) {
            abort(403, 'Only administrators can view all users.');
        }
        
        $query = User::query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('affiliation', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $role = $request->input('role');
            if ($role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($role === 'chair') {
                $query->where('is_chair', true);
            } elseif ($role === 'reviewer') {
                $query->where('is_reviewer', true);
            }
        }
        
        $users = $query->latest()->paginate(20);
        
        return view('users.index', compact('users'));
    }
    
    /**
     * Show user details (admin only)
     */
    public function show(User $user)
    {
        // Only admin can view user details
        if (!auth()->user()->is_admin) {
            abort(403, 'Only administrators can view user details.');
        }
        
        $user->load(['papers', 'reviewAssignments', 'conferenceRegistration']);
        return view('users.show', compact('user'));
    }
    
    /**
     * Update user (admin only)
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Only administrators can update users.');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'affiliation' => 'nullable|string|max:255',
            'is_admin' => 'boolean',
            'is_chair' => 'boolean',
            'is_reviewer' => 'boolean',
        ]);
        
        $user->update($request->all());
        
        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully!');
    }
    
    /**
     * Delete user (admin only)
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Only administrators can delete users.');
        }
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }
    
    /**
     * List reviewers (for chairs)
     */
    public function reviewers(Request $request)
    {
        // Only chairs and admins can view reviewers
        if (!auth()->user()->is_chair && !auth()->user()->is_admin) {
            abort(403, 'Only chairs and administrators can view reviewers.');
        }
        
        $year = $request->input('year', date('Y'));
        
        $reviewers = User::where('is_reviewer', true)
            ->withCount(['reviewAssignments as assigned_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                });
            }])
            ->withCount(['reviewAssignments as completed_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->where('status', 'completed');
            }])
            ->withCount(['reviewAssignments as pending_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->whereIn('status', ['pending', 'accepted', 'in_progress']);
            }])
            ->orderByDesc('assigned_count')
            ->get();
        
        return view('chair.reviewers', compact('reviewers', 'year'));
    }
    
    /**
     * Toggle reviewer status (for chairs)
     */
    public function toggleReviewer(User $user)
    {
        // Only chairs and admins can toggle reviewer status
        if (!auth()->user()->is_chair && !auth()->user()->is_admin) {
            abort(403, 'Only chairs and administrators can modify reviewer status.');
        }
        
        $user->update(['is_reviewer' => !$user->is_reviewer]);
        
        $status = $user->is_reviewer ? 'enabled' : 'disabled';
        
        return redirect()->back()
            ->with('success', "Reviewer status {$status} for {$user->full_name}");
    }
    
    /**
     * Toggle chair status (admin only)
     */
    public function toggleChair(User $user)
    {
        // Only admins can toggle chair status
        if (!auth()->user()->is_admin) {
            abort(403, 'Only administrators can modify chair status.');
        }
        
        $user->update(['is_chair' => !$user->is_chair]);
        
        $status = $user->is_chair ? 'granted' : 'revoked';
        
        return redirect()->back()
            ->with('success', "Chair privileges {$status} for {$user->full_name}");
    }
}