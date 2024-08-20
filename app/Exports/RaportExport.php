<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class RaportExport implements WithMultipleSheets
{
    protected $userData;

    public function __construct($userData)
    {
        $this->userData = $userData;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->userData as $userName => $userData) {
            $sheets[] = new class($userName, $userData) implements FromCollection, WithTitle
            {
                protected $userName;
                protected $userData;

                public function __construct($userName, $userData)
                {
                    $this->userName = $userName;
                    $this->userData = $userData;
                }

                public function title(): string
                {
                    return $this->userName;
                }

                public function collection()
                {
                    return $this->userData;
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
            };
        }

        return $sheets;
    }
}
