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



    public function exportData(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $user = $request->user(); // Assuming you are using Laravel's authentication

    // Fetch the required data from the database
    $query = DB::table('utilizator')
        ->leftJoin('condica as intrare', function ($join) use ($startDate, $endDate) {
            $join->on('utilizator.id', '=', 'intrare.user_id')
                ->where('intrare.stare', '=', 'intrare')
                ->whereBetween(DB::raw('DATE(intrare.data_ora)'), [$startDate, $endDate]);

        })
        ->leftJoin('condica as iesire', function ($join) use ($startDate, $endDate) {
            $join->on('utilizator.id', '=', 'iesire.user_id')
                ->where('iesire.stare', '=', 'iesire')
                ->whereBetween(DB::raw('DATE(iesire.data_ora)'), [$startDate, $endDate])
                ->whereRaw('DATE(iesire.data_ora) = DATE(intrare.data_ora)'); // Match check-out date with check-in date

        })
        ->select(
            'utilizator.id as ID',
            'utilizator.nume as NUME',
            'utilizator.departament as DEPARTAMENT',
            DB::raw('DATE(intrare.data_ora) as DATA'),
            DB::raw('MIN(intrare.data_ora) as CHECK_IN'),
            DB::raw('(SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "iesire" AND DATE(data_ora) BETWEEN \'' . $startDate . '\' AND \'' . $endDate . '\' AND DATE(data_ora)= DATA AND data_ora > (SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "intrare" AND DATE(data_ora) BETWEEN \'' . $startDate . '\' AND \'' . $endDate . '\' ORDER BY data_ora LIMIT 1) ORDER BY data_ora LIMIT 1) as CHECK_OUT'),
        )
        ->groupBy('utilizator.id', 'utilizator.nume', 'utilizator.departament', DB::raw('DATE(intrare.data_ora)')); // Group by date as well

    // Retrieve the filtered data
    $raportData = $query->get();

    // Calculate TIMP_PREZENTA and PESTE/SUB_TIMP for each record
    foreach ($raportData as $record) {
        $record->CHECK_IN = $record->CHECK_IN ?: 'N/A';
        $record->CHECK_OUT = $record->CHECK_OUT ?: 'N/A';

        if ($record->CHECK_IN && $record->CHECK_OUT) {
            $checkIn = strtotime($record->CHECK_IN);
            $checkOut = strtotime($record->CHECK_OUT);

            if ($checkIn > $checkOut) {
                $record->TIMP_PREZENTA = 0;
                $record->PESTE_SUB_TIMP = 'N/A';
            } else {
                $workedHours = ($checkOut - $checkIn) / 3600;
                $timpDeLucru = $this->fetchTimpDeLucru($record->ID); // Corrected the property name to ID

                if ($workedHours > 0) {
                    if ($workedHours >= 1) {
                        $record->TIMP_PREZENTA = round($workedHours, 1) . ' ore';
                    } else {
                        $record->TIMP_PREZENTA = round($workedHours * 60) . ' minute';
                    }
                } else {
                    $record->TIMP_PREZENTA = '0 ore';
                }

                if ($workedHours > $timpDeLucru) {
                    $record->PESTE_SUB_TIMP = 'PESTE :' . round($workedHours - $timpDeLucru, 1);
                } else {
                    $record->PESTE_SUB_TIMP = 'SUB :' . round($workedHours - $timpDeLucru, 1);
                }
            }
        } else {
            // Handle case when CHECK_IN or CHECK_OUT is null
            $record->TIMP_PREZENTA = 'N/A';
            $record->PESTE_SUB_TIMP = 'N/A';
        }

        // Initialize PAUZA_COUNT and PAUZA_DURATION for each day
        $record->PAUZA_COUNT = 0;
        $record->PAUZA_DURATION = 'N/A';

        // Calculate PAUZA_COUNT and PAUZA_DURATION for each day
        $pauzaCount = DB::table('condica')
                ->where('user_id', $record->ID)
                ->where('stare', 'pauza')
                ->whereDate('data_ora', $record->DATA)
                ->count();
        $pauzaStatuses = DB::table('condica')
            ->select('data_ora', 'stare')
            ->where('user_id', $record->ID)
            ->whereDate('data_ora', $record->DATA)
            ->orderBy('data_ora')
            ->get();

        $dailyPauzaDuration = 0;
        $pauzaPair = null;

        foreach ($pauzaStatuses as $status) {
            if ($status->stare == 'pauza') {
                // If a pauza status is encountered, store it as the beginning of a pair
                $pauzaPair = $status;
            } elseif ($status->stare == 'intrare' && $pauzaPair) {
                // If an intrare status is encountered and there's a paired pauza status
                $pauzaStartTime = strtotime($pauzaPair->data_ora);
                $intrareTime = strtotime($status->data_ora);

                if ($intrareTime > $pauzaStartTime) {
                    $pauzaDuration = $intrareTime - $pauzaStartTime;
                    $dailyPauzaDuration += $pauzaDuration;
                }

                $pauzaPair = null;
            }
        }

        // Set PAUZA_DURATION for each day
        if ($dailyPauzaDuration > 0) {
            $record->PAUZA_DURATION = floor($dailyPauzaDuration / 60) . ' minute';
            $record->PAUZA_COUNT = $pauzaCount; // Set PAUZA_COUNT to 1 if there was at least one pauza for the day
        }
    }

    // Generate and export the file (Excel) with the filtered data
    return Excel::download(new RaportExport($raportData), 'raport_' . $startDate . '_' . $endDate . '.xlsx');
} 
    private function fetchTimpDeLucru($userId)
    {
        // Fetch timp_de_lucru from the database based on $userId
        return DB::table('utilizator')->where('id', $userId)->value('timp_de_lucru');
    }
    
    
    



}
