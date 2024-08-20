<?php

namespace App\Exports;

use App\Models\Raport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RaportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nume',
            'Departament',
            'Data',
            'Check-in',
            'Check-out',
            'Timp prezenta',
            'Peste/Sub timp',
            'Pauze',
            'Durata Pauza'
        ];
    }
}