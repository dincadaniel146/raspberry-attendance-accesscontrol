<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
class RaportController extends Controller
{
   public function index()
{
    // Fetch the required data from the database
    $raportData = DB::table('utilizator')
        ->leftJoin('condica as intrare', function ($join) {
            $join->on('utilizator.id', '=', 'intrare.user_id')
                ->where('intrare.stare', '=', 'intrare')
                ->whereDate('intrare.data_ora', '=', now()->toDateString())
                ->orderBy('intrare.data_ora', 'asc'); // Get the first entry of the day
        })
        ->leftJoin('condica as iesire', function ($join) {
            $join->on('utilizator.id', '=', 'iesire.user_id')
                ->where('iesire.stare', '=', 'iesire')
                ->whereDate('iesire.data_ora', '=', now()->toDateString())
                ->orderBy('iesire.data_ora', 'asc'); // Get the last entry of the day
        })
        ->select(
            'utilizator.id as ID',
            'utilizator.nume as NUME',
            DB::raw('MIN(intrare.data_ora) as CHECK_IN'),
            DB::raw('(SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "iesire" AND DATE(data_ora) = CURDATE() AND data_ora > (SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "intrare" AND DATE(data_ora) = CURDATE() ORDER BY data_ora LIMIT 1) ORDER BY data_ora LIMIT 1) as CHECK_OUT'), // Select the first check-out after the check-in
            'utilizator.departament as DEPARTAMENT',
            DB::raw('(SELECT COUNT(*) FROM condica WHERE user_id = utilizator.id AND stare = "pauza" AND DATE(data_ora) = CURDATE()) as PAUZA_COUNT')
        )
        ->groupBy('utilizator.id', 'utilizator.nume', 'utilizator.departament')
        ->get();

    // Calculate TIMP_LUCRAT, PESTE/SUB NORMA, and PAUZA_DURATION
// Calculate TIMP_LUCRAT, PESTE/SUB NORMA, and PAUZA_DURATION
$raportData = $raportData->map(function ($item) {
    $item->CHECK_IN = $item->CHECK_IN ?: 'N/A';
    $item->CHECK_OUT = $item->CHECK_OUT ?: 'N/A';
    $item->PAUZA_COUNT = $item->PAUZA_COUNT ?: '0';

    // Calculate TIMP_LUCRAT
    if ($item->CHECK_IN && $item->CHECK_OUT) {
        $checkIn = strtotime($item->CHECK_IN);
        $checkOut = strtotime($item->CHECK_OUT);

        // Handle case when check-in time is later than check-out time
        if ($checkIn > $checkOut) {
            $item->TIMP_LUCRAT = 0;
            $item->PESTE_SUB_NORMA = 'N/A';
        } else {
            // Calculate TIMP_LUCRAT
            $workedHours = ($checkOut - $checkIn) / 3600;
            $workedMinutes = ($checkOut - $checkIn) / 60;

            if ($workedHours > 0) {
                if ($workedHours > 1.1) {
                    $item->TIMP_LUCRAT = round($workedHours, 1) . ' ore';
                } elseif ($workedHours >= 1 && $workedHours < 1.1) {
                    $item->TIMP_LUCRAT = round($workedHours, 1) . ' ora';
                } else {
                    $item->TIMP_LUCRAT = round($workedMinutes, 1) . ' minute';
                }
            } else {
                $item->TIMP_LUCRAT = 0;
            }

            // Fetch the timp_de_lucru from the database for each user
            $timpDeLucru = DB::table('utilizator')->where('id', $item->ID)->value('timp_de_lucru');

            // Determine if worked hours are PESTE or SUB NORMA
            if ($workedHours > $timpDeLucru) {
                $item->PESTE_SUB_NORMA = 'PESTE :' . round($workedHours - $timpDeLucru, 1) . " ore";
            } else {
                $item->PESTE_SUB_NORMA = 'SUB :' . round($workedHours - $timpDeLucru, 1) . " ore";
            }
        }
    } else {
        // Handle cases where there are missing check-in or check-out times
        $item->TIMP_LUCRAT = 0;
        $item->PESTE_SUB_NORMA = 'N/A';
    }

    $totalPauzaDuration = 0;
    $pauzaStatuses = DB::table('condica')
        ->select('id', 'data_ora', 'stare')
        ->where('user_id', $item->ID)
        ->whereIn('stare', ['pauza', 'intrare'])
        ->whereDate('data_ora', now()->toDateString())
        ->orderBy('data_ora')
        ->get();
    
    $pauzaPair = null;
    foreach ($pauzaStatuses as $status) {
        if ($status->stare == 'pauza') {
            // If a pauza status is encountered, store it as the beginning of a pair
            $pauzaPair = $status;
        } elseif ($status->stare == 'intrare' && $pauzaPair) {
            // If an intrare status is encountered and there's a paired pauza status
            $pauzaStartTime = strtotime($pauzaPair->data_ora);
            $intrareTime = strtotime($status->data_ora);
            // Ensure the duration is not negative
            if ($intrareTime > $pauzaStartTime) {
                $pauzaDuration = $intrareTime - $pauzaStartTime;
                $totalPauzaDuration += $pauzaDuration;
            }
            // Reset the pauza pair
            $pauzaPair = null;
        }
    }
    
    // Convert the total pauza duration to hours and minutes
    $totalPauzaHours = floor($totalPauzaDuration / 60);
    
    // Store the total pauza duration in the item
    $item->PAUZA_DURATION =  $totalPauzaHours;
    
    
    if ($item->DEPARTAMENT === null) {
        $item->DEPARTAMENT = 'N/A';
    }
    return $item;
});



    // Return the view with the data
    return view('raport', ['raportData' => $raportData]);
}


public function data(Request $request, $date)
{
    // Fetch the required data from the database
    $raportData = DB::table('utilizator')
        ->leftJoin('condica as intrare', function ($join) use ($date) {
            $join->on('utilizator.id', '=', 'intrare.user_id')
                ->where('intrare.stare', '=', 'intrare')
                ->whereDate('intrare.data_ora', '=', $date)
                ->orderBy('intrare.data_ora', 'asc'); // Get the first entry of the day
        })
        ->leftJoin('condica as iesire', function ($join) use ($date) {
            $join->on('utilizator.id', '=', 'iesire.user_id')
                ->where('iesire.stare', '=', 'iesire')
                ->whereDate('iesire.data_ora', '=', $date)
                ->orderBy('iesire.data_ora', 'desc'); // Get the last entry of the day
        })
        ->select(
            'utilizator.id as ID',
            'utilizator.nume as NUME',
            DB::raw('MIN(intrare.data_ora) as CHECK_IN'),
            DB::raw('(SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "iesire" AND DATE(data_ora)= "'.$date.'" AND data_ora > (SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "intrare" AND DATE(data_ora) = "'.$date.'" ORDER BY data_ora LIMIT 1) ORDER BY data_ora LIMIT 1) as CHECK_OUT'), // Select the first check-out after the check-in
            'utilizator.departament as DEPARTAMENT',
            DB::raw("(SELECT COUNT(*) FROM condica WHERE user_id = utilizator.id AND stare = 'pauza' AND DATE(data_ora) = '$date') as PAUZA_COUNT")
        )
        ->groupBy('utilizator.id', 'utilizator.nume', 'utilizator.departament')
        ->get();

    if ($raportData->isEmpty()) {
        // Return a response indicating that no data is available for the specified date
        return response()->json(['error' => 'Nu exista date.'], 404);
    }

    // Calculate TIMP_LUCRAT and PESTE/SUB NORMA
    $raportData = $raportData->map(function ($item) use($date) {
        $item->CHECK_IN = $item->CHECK_IN ?: 'N/A';
        $item->CHECK_OUT = $item->CHECK_OUT ?: 'N/A';

        // Check for null values before processing
        if ($item->CHECK_IN && $item->CHECK_OUT) {
            $checkIn = strtotime($item->CHECK_IN);
            $checkOut = strtotime($item->CHECK_OUT);

            // Check for division by zero
            if ($checkOut - $checkIn != 0) {
                // Calculate TIMP_LUCRAT
                $workedHours = ($checkOut - $checkIn) / 3600;
                $workedMinutes = ($checkOut - $checkIn) / 60;

                if ($workedHours > 0) {
                    if ($workedHours > 1.1) {
                        $item->TIMP_LUCRAT = round($workedHours, 1) . ' ore';
                    } elseif ($workedHours >= 1 && $workedHours < 1.1) {
                        $item->TIMP_LUCRAT = round($workedHours, 1) . ' ora';
                    } else {
                        $item->TIMP_LUCRAT = round($workedMinutes, 1) . ' minute';
                    }
                } else {
                    $item->TIMP_LUCRAT = 0;
                }

                // Fetch the timp_de_lucru from the database for each user
                $timpDeLucru = DB::table('utilizator')->where('id', $item->ID)->value('timp_de_lucru');

                // Determine if worked hours are PESTE or SUB NORMA
                if ($checkOut) {
                    if ($workedHours > $timpDeLucru) {
                        $item->PESTE_SUB_NORMA = 'PESTE :' . round($workedHours - $timpDeLucru, 1) ." ore";
                    } else {
                        $item->PESTE_SUB_NORMA = 'SUB :' . round($workedHours - $timpDeLucru, 1) ." ore";
                    }
                } else {
                    $item->PESTE_SUB_NORMA = 'N/A';
                }
            } else {
                // Handle division by zero case
                $item->TIMP_LUCRAT = 0;
                $item->PESTE_SUB_NORMA = 'N/A';
            }
        } else {
            // Handle null values
            $item->TIMP_LUCRAT = 0;
            $item->PESTE_SUB_NORMA = 'N/A';
        }

        if ($item->DEPARTAMENT === null) {
            $item->DEPARTAMENT = 'N/A';
        }
        return $item;
    });

    // Calculate pauza durations
    foreach ($raportData as $item) {
        $totalPauzaDuration = 0;
        $pauzaStatuses = DB::table('condica')
            ->select('id', 'data_ora', 'stare')
            ->where('user_id', $item->ID)
            ->whereIn('stare', ['pauza', 'intrare'])
            ->whereDate('data_ora', $date)
            ->orderBy('data_ora')
            ->get();
        
        $pauzaPair = null;
        foreach ($pauzaStatuses as $status) {
            if ($status->stare == 'pauza') {
                // If a pauza status is encountered, store it as the beginning of a pair
                $pauzaPair = $status;
            } elseif ($status->stare == 'intrare' && $pauzaPair) {
                // If an intrare status is encountered and there's a paired pauza status
                $pauzaStartTime = strtotime($pauzaPair->data_ora);
                $intrareTime = strtotime($status->data_ora);
                // Ensure the duration is not negative
                if ($intrareTime > $pauzaStartTime) {
                    $pauzaDuration = $intrareTime - $pauzaStartTime;
                    $totalPauzaDuration += $pauzaDuration;
                }
                // Reset the pauza pair
                $pauzaPair = null;
            }
        }
        
        // Convert the total pauza duration to hours and minutes
        $totalPauzaHours = floor($totalPauzaDuration / 60);
        
        // Store the total pauza duration in the item
        $item->PAUZA_DURATION =  $totalPauzaHours;
    }

    // Return the view with the data
    return response()->json(['raportData' => $raportData, 'date' => $date]);
}


    
    
  public function lunar()
    {
        
        // Fetch all users from the 'utilizator' table
        $users = DB::table('utilizator')->select('id', 'nume','departament')->get();

        // Initialize an array to store presence hours for each user
        $presenceHours = [];

        // Define the start and end dates of the month
        $currentMonth = Carbon::now()->month;


        // Loop through each user
        foreach ($users as $user) {
            // Query the database to retrieve attendance data for the user and the month
            $attendanceData = DB::table('condica')
                ->select('stare', 'data_ora')
                ->where('user_id', $user->id)
                ->whereMonth('data_ora', $currentMonth)
                ->orderBy('data_ora')
                ->get();

            // Calculate total presence hours for the user
            $totalPresenceMinutes = $this->calculateTotalPresenceMinutes($attendanceData);
            if (isset($user->departament)) {
                $departament = $user->departament;
            } else {
                $departament = "N/A";
            }
            // Store the total presence hours for the user
            $presenceMinutes[$user->id] = [
                'name' => $user->nume,
                'totalMinutes' => $totalPresenceMinutes,
                'departament' => $departament,
            ];
        }
           
           

    // Return the view with the data
    return view('raportlunar', ['presenceMinutes' => $presenceMinutes]);
}
private function calculateTotalPresenceMinutes($attendanceData)
{
    $totalPresenceMinutes = 0;
    $previousDate = null;
    $entryTimestamp = null;
    $exitTimestamp = null;

    // Group attendance data by date
    $groupedAttendance = $attendanceData->groupBy(function ($item) {
        return substr($item->data_ora, 0, 10); // Group by date (YYYY-MM-DD)
    });

    // Loop through grouped attendance data
    foreach ($groupedAttendance as $date => $records) {
        $firstIntrare = null;
        $firstIesireAfterIntrare = null;
    
        // Loop through records of the day
        foreach ($records as $record) {
            if ($record->stare === 'intrare') {
                // Set the first 'intrare' of the day
                if ($firstIntrare === null) {
                    $firstIntrare = strtotime($record->data_ora);
                }
            } elseif ($record->stare === 'iesire') {
                // Check if it's the first 'iesire' after the first 'intrare'
                if ($firstIntrare !== null && $firstIesireAfterIntrare === null && strtotime($record->data_ora) > $firstIntrare) {
                    $firstIesireAfterIntrare = strtotime($record->data_ora);
                }
            }
        }
    
        // If both 'intrare' and 'iesire' exist for the day, and firstIesireAfterIntrare is set, calculate presence time
        if ($firstIntrare !== null && $firstIesireAfterIntrare !== null) {
            $presenceTimeSeconds = $firstIesireAfterIntrare - $firstIntrare;
            $presenceTimeMinutes = max(0, $presenceTimeSeconds / 60); // Convert seconds to minutes
            $totalPresenceMinutes += $presenceTimeMinutes;
        }
    }
    

    return (round($totalPresenceMinutes/60, 2) . ' ore');
}


public function lunardata(Request $request, $date)
{
    // Fetch all users from the 'utilizator' table
    $users = DB::table('utilizator')->select('id', 'nume', 'departament')->get();

    // Initialize an array to store presence minutes for each user
    $presenceMinutes = [];

    // Define the start and end dates of the month
    $currentMonth = $date;

    // Loop through each user
    foreach ($users as $user) {
        // Query the database to retrieve attendance data for the user and the month
        $attendanceData = DB::table('condica')
            ->select('stare', 'data_ora')
            ->where('user_id', $user->id)
            ->whereMonth('data_ora', $currentMonth)
            ->orderBy('data_ora')
            ->get();

        // Calculate total presence minutes for the user
        $totalPresenceMinutes = $this->calculateTotalPresenceMinutes($attendanceData);
        if (isset($user->departament)) {
            $departament = $user->departament;
        } else {
            $departament = "N/A";
        }
        // Store the presence minutes data for the user
        $presenceMinutes[] = [
            'id' => $user->id,
            'name' => $user->nume,
            'totalMinutes' => $totalPresenceMinutes,
            'departament' => $departament,
        ];
    }

    // Return the presence minutes data
    return response()->json(['presenceMinutes' => $presenceMinutes, 'date' => $date]);
}




}









