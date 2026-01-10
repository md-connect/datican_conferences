<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $stats = $this->getRegistrationStats();
        $recentRegistrations = ConferenceRegistration::latest()->take(10)->get();
        
        return view('admin.dashboard', compact('stats', 'recentRegistrations'));
    }

    public function registrations(Request $request)
{
    $query = ConferenceRegistration::query();
    
    if ($request->has('filter')) {
        switch ($request->filter) {
            case 'presenters':
                $query->where('is_presenting_paper', true);
                break;
            case 'datican_members':
                $query->where('is_datican_member', true);
                break;
            case 'recent':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
        }
    }
    
    $registrations = $query->latest()->paginate(20);
    
    return view('admin.registrations', compact('registrations'));
}

    public function showRegistration($id)
    {
        $registration = ConferenceRegistration::findOrFail($id);
        return view('admin.registration-show', compact('registration'));
    }

    private function getRegistrationStats()
    {
        return Cache::remember('admin_registration_stats', 300, function () {
            return [
                'total_registrations' => ConferenceRegistration::count(),
                'total_presenters' => ConferenceRegistration::where('is_presenting_paper', true)->count(),
                'total_datican_members' => ConferenceRegistration::where('is_datican_member', true)->count(),
                
                'gender_distribution' => ConferenceRegistration::selectRaw('gender, COUNT(*) as count')
                    ->groupBy('gender')
                    ->pluck('count', 'gender')
                    ->toArray(),
                
                'status_distribution' => ConferenceRegistration::whereNotNull('datican_status')
                    ->selectRaw('datican_status, COUNT(*) as count')
                    ->groupBy('datican_status')
                    ->pluck('count', 'datican_status')
                    ->toArray(),
                
                'title_distribution' => ConferenceRegistration::selectRaw('title, COUNT(*) as count')
                    ->groupBy('title')
                    ->pluck('count', 'title')
                    ->toArray(),
                
                'registrations_by_date' => ConferenceRegistration::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->pluck('count', 'date')
                    ->toArray(),
            ];
        });
    }
}