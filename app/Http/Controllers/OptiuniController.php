<?php

namespace App\Http\Controllers;
use App\Exports\RaportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class OptiuniController extends Controller
{
    public function index()
    {
return view ('optiuni');
    }

    public function exportData(Request $request) //functia pt afisarea datelor in fisierul excel exportat
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $users = DB::table('utilizator')->get();

        // array date utilizator
        $userData = [];

        // Datele fiecarui utilizator in parte
        foreach ($users as $user) {
            $userData[$user->nume] = $this->date_utilizator($user->id, $startDate, $endDate);
        }

        // Generarea raportului si exportul fisierului excel
        return Excel::download(new RaportExport($userData), 'raport_' . $startDate . '_' . $endDate . '.xlsx');
    }

    private function date_utilizator($userId, $startDate, $endDate) // Functia pt. calculul timpului lucrat + numarul si durata pauzelor pe parcursul startDate - endDate
    {
        $query = DB::table('utilizator')
            ->leftJoin('condica as intrare', function ($join) use ($startDate, $endDate) {
                $join->on('utilizator.id', '=', 'intrare.user_id')
                    ->where('intrare.stare', '=', 'intrare')
                    ->whereBetween(DB::raw('DATE(intrare.data_ora)'), [$startDate, $endDate]);
            })
            ->select(
                'utilizator.id as ID',
                'utilizator.nume as NUME',
                'utilizator.departament as DEPARTAMENT',
                DB::raw('DATE(intrare.data_ora) as DATA'),
                DB::raw('MIN(intrare.data_ora) as CHECK_IN'),
                DB::raw('(SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "iesire" AND DATE(data_ora) BETWEEN \'' . $startDate . '\' AND \'' . $endDate . '\' AND DATE(data_ora)= DATA AND data_ora > (SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "intrare" AND DATE(data_ora) BETWEEN \'' . $startDate . '\' AND \'' . $endDate . '\' ORDER BY data_ora LIMIT 1) ORDER BY data_ora LIMIT 1) as CHECK_OUT'),
            )
            ->where('utilizator.id', $userId) // filtram dupa id-ul utilizatorului
            ->orderBy('utilizator.id') 
            ->groupBy('utilizator.id', 'utilizator.nume', 'utilizator.departament', DB::raw('DATE(intrare.data_ora)')); // Group by date as well

        // Datele filtrate
        $userData = $query->get();

        // Calcul TIMP_PREZENTA/PESTE_SUB_NORMA
        foreach ($userData as $record) {
            $record->CHECK_IN = $record->CHECK_IN ?: 'N/A';
            $record->CHECK_OUT = $record->CHECK_OUT ?: 'N/A';

        if ($record->CHECK_IN && $record->CHECK_OUT) {
            $checkIn = strtotime($record->CHECK_IN);
            $checkOut = strtotime($record->CHECK_OUT);

            if ($checkIn > $checkOut) {
                $record->TIMP_PREZENTA = 0;
                $record->PESTE_SUB_TIMP = 'N/A';
            } else {
                $orePrezenta = ($checkOut - $checkIn) / 3600;
                $timpDeLucru = $this->timp_de_lucru($record->ID); // Corrected the property name to ID

                if ($orePrezenta > 0) {
                    if ($orePrezenta >= 1) {
                        $record->TIMP_PREZENTA = round($orePrezenta, 1) . ' ore';
                    } else {
                        $record->TIMP_PREZENTA = round($orePrezenta * 60) . ' minute';
                    }
                } else {
                    $record->TIMP_PREZENTA = '0 ore';
                }

                if ($orePrezenta > $timpDeLucru) {
                    $record->PESTE_SUB_TIMP = 'PESTE :' . round($orePrezenta - $timpDeLucru, 1);
                } else {
                    $record->PESTE_SUB_TIMP = 'SUB :' . round($orePrezenta - $timpDeLucru, 1);
                }
            }
        } else {

            $record->TIMP_PREZENTA = 'N/A';
            $record->PESTE_SUB_TIMP = 'N/A';
        }

        $record->PAUZA_COUNT = 0;
        $record->PAUZA_DURATA = 'N/A';

        // Calculul numarului si duratele pauzelor
        $pauzaCount = DB::table('condica')
                ->where('user_id', $record->ID)
                ->where('stare', 'pauza')
                ->whereDate('data_ora', $record->DATA)
                ->count();
        $pauzaStatus = DB::table('condica')
            ->select('data_ora', 'stare')
            ->where('user_id', $record->ID)
            ->whereDate('data_ora', $record->DATA)
            ->orderBy('data_ora')
            ->get();

        $durataPauza = 0;
        $perechePauza = null;

        foreach ($pauzaStatus as $status) {
            if ($status->stare == 'pauza') {
                $perechePauza = $status;
            } elseif ($status->stare == 'intrare' && $perechePauza) {
                $pauzaStart = strtotime($perechePauza->data_ora);
                $intrare = strtotime($status->data_ora);

                if ($intrare > $pauzaStart) {
                    $durata = $intrare - $pauzaStart;
                    $durataPauza += $durata;
                }

                $perechePauza = null;
            }
        }

        if ($durataPauza > 0) {
            $record->PAUZA_DURATA = floor($durataPauza / 60) . ' minute';
            $record->PAUZA_COUNT = $pauzaCount; 
        } //de sters?
     
    }

    return $userData;
} 
    private function timp_de_lucru($userId)
    {
        // Luam timpul de lucru pentru fiecare utilizator
        return DB::table('utilizator')->where('id', $userId)->value('timp_de_lucru');
    }
    
    
    



}