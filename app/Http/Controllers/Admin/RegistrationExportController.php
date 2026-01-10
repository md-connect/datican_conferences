<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConferenceRegistration;
use App\Exports\RegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class RegistrationExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $type = $request->get('type', 'all');
        
        $filename = 'conference_registrations_' . date('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'csv':
                return Excel::download(new RegistrationsExport($type), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
            case 'pdf':
                return Excel::download(new RegistrationsExport($type), $filename . '.pdf', \Maatwebsite\Excel\Excel::MPDF);
            default:
                return Excel::download(new RegistrationsExport($type), $filename . '.xlsx');
        }
    }
}