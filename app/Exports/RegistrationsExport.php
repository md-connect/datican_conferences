<?php

namespace App\Exports;

use App\Models\ConferenceRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistrationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $type;

    public function __construct($type = 'all')
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = ConferenceRegistration::query();
        
        switch ($this->type) {
            case 'presenters':
                $query->where('is_presenting_paper', true);
                break;
            case 'datican_members':
                $query->where('is_datican_member', true);
                break;
            case 'non_presenters':
                $query->where('is_presenting_paper', false);
                break;
            case 'non_members':
                $query->where('is_datican_member', false);
                break;
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Registration Date',
            'Title',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Institution',
            'Gender',
            'DATICAN Member',
            'DATICAN Status',
            'Presenting Paper',
            'Created At',
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->id,
            $registration->created_at->format('Y-m-d'),
            $registration->title,
            $registration->firstname,
            $registration->middlename,
            $registration->lastname,
            $registration->email,
            $registration->phone_number,
            $registration->institution,
            $registration->gender,
            $registration->is_datican_member ? 'Yes' : 'No',
            $registration->datican_status ?? 'N/A',
            $registration->is_presenting_paper ? 'Yes' : 'No',
            $registration->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Set column widths
            'A' => ['width' => 10],
            'B' => ['width' => 15],
            'C' => ['width' => 10],
            'D' => ['width' => 15],
            'E' => ['width' => 15],
            'F' => ['width' => 15],
            'G' => ['width' => 25],
            'H' => ['width' => 15],
            'I' => ['width' => 25],
            'J' => ['width' => 10],
            'K' => ['width' => 15],
            'L' => ['width' => 20],
            'M' => ['width' => 15],
            'N' => ['width' => 20],
        ];
    }
}