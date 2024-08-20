<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
class RaportController extends Controller
{
   public function index()
{
    // Functie pentru calculul timpului lucrat + numarul si durata pauzelor pentru ziua curenta
    $raportData = DB::table('utilizator')
        ->leftJoin('condica as intrare', function ($join) {
            $join->on('utilizator.id', '=', 'intrare.user_id')
                ->where('intrare.stare', '=', 'intrare')
                ->whereDate('intrare.data_ora', '=', now()->toDateString())
                ->orderBy('intrare.data_ora', 'asc'); // Prima intrare de astazi
        })
        ->select(
            'utilizator.id as ID',
            'utilizator.nume as NUME',
            DB::raw('MIN(intrare.data_ora) as CHECK_IN'), //prima intrare
            DB::raw('(SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "iesire" AND DATE(data_ora) = CURDATE() AND data_ora > (SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "intrare" AND DATE(data_ora) = CURDATE() ORDER BY data_ora LIMIT 1) ORDER BY data_ora LIMIT 1) as CHECK_OUT'), // Prima stare de "iesire" care apare dupa prima intrare
            'utilizator.departament as DEPARTAMENT',
            DB::raw('(SELECT COUNT(*) FROM condica WHERE user_id = utilizator.id AND stare = "pauza" AND DATE(data_ora) = CURDATE()) as PAUZA_COUNT') //Numarul de pauze
        )
        ->groupBy('utilizator.id', 'utilizator.nume', 'utilizator.departament')
        ->get();

// Folosind functia map iteram peste fiecare item pentru a returna modificari sau transformari ale acestora
$raportData = $raportData->map(function ($item) {
    //valori default daca avem valori nule
    $item->CHECK_IN = $item->CHECK_IN ?: 'N/A';
    $item->CHECK_OUT = $item->CHECK_OUT ?: 'N/A';
    $item->PAUZA_COUNT = $item->PAUZA_COUNT ?: '0';

    // Calcul timp_lucrat
    if ($item->CHECK_IN && $item->CHECK_OUT) {
        $checkIn = strtotime($item->CHECK_IN);
        $checkOut = strtotime($item->CHECK_OUT);

        // Daca checkIn apare dupa checkOut timpul lucrat nu se va mai calcula
        if ($checkIn > $checkOut) {
            $item->TIMP_LUCRAT = 0;
            $item->PESTE_SUB_NORMA = 'N/A';
        } else {
            //calcul ore/minute lucrate, impartim la 60 de secunde pentru a afla minutele si la 3600 pentru a afla orele 
            $orePrezenta = ($checkOut - $checkIn) / 3600; 
            $minutePrezenta = ($checkOut - $checkIn) / 60;

            if ($orePrezenta > 0) {
                if ($orePrezenta > 1.1) { // caz in care avem mai mult de o ora lucrataa
                    $item->TIMP_LUCRAT = round($orePrezenta, 3) . ' ore'; //rotunjim orele lucrate la doua zecimale
                } elseif ($orePrezenta >= 1 && $orePrezenta < 1.1) { //caz in care este fix o ora lucrata
                    $item->TIMP_LUCRAT = round($orePrezenta, 1) . ' ora';
                } else { //caz in care timp_lucrat nu are cel putin o ora, afisarea se va face in minute
                    $item->TIMP_LUCRAT = round($minutePrezenta, 3) . ' minute';
                }
            } else { //error handling in caz de valori negative
                $item->TIMP_LUCRAT = 0;
            }

            // Preluam timp_de_lucru din baza de date pentru calculul SUB sau PESTE
            $timpDeLucru = DB::table('utilizator')->where('id', $item->ID)->value('timp_de_lucru');

            
            if ($orePrezenta > $timpDeLucru) { // Caz in care timpul lucrat este peste norma stabilita
                $item->PESTE_SUB_NORMA = 'PESTE :' . round($orePrezenta - $timpDeLucru, 1) . " ore";
            } else { // Caz in care timpul lucrat este sub norma stabilita
                $item->PESTE_SUB_NORMA = 'SUB :' . round($orePrezenta - $timpDeLucru, 1) . " ore";
            }
        }
    } else {
        // Error handling in cazul lipsei de check-in sau check-out
        $item->TIMP_LUCRAT = 0;
        $item->PESTE_SUB_NORMA = 'N/A';
    }
//Calculul pauzelor
    $totalPauza = 0; //variabila unde stocam durata pauzelor
    $pauzaStatus = DB::table('condica') //preluam datele utilizatorilor unde coloana 'stare' este ori 'pauza' ori 'intrare' sortate crescator
        ->select('id', 'data_ora', 'stare')
        ->where('user_id', $item->ID)
        ->whereIn('stare', ['pauza', 'intrare'])
        ->whereDate('data_ora', now()->toDateString())
        ->orderBy('data_ora')
        ->get();
    
    $pauzaPereche = null;//variabila unde stocham o pereche de o pauza si o intrare
    foreach ($pauzaStatus as $status) { //iteram pentru fiecare user
        if ($status->stare == 'pauza') {
            // Daca intalnim o pauza o setam ca inceputul unei perechi
            $pauzaPereche = $status;
        } elseif ($status->stare == 'intrare' && $pauzaPereche) {
            // Daca intalnim o intrare si avem o pauza in pereche
            $pauzaStart = strtotime($pauzaPereche->data_ora);
            $intrare = strtotime($status->data_ora);//string to time
            // Asiguram ca durata nu este negativa
            if ($intrare > $pauzaStart) {
                $durata = $intrare - $pauzaStart; //calcul durata pauza
                $totalPauza += $durata; 
            }
            // Reset pereche
            $pauzaPereche = null;
        }
    }
    
    // Calcul durata pauza in minute
    $totalPauzaMinute = floor($totalPauza / 60);
    
    // stocam durata pauzei in item
    $item->PAUZA_DURATA =  $totalPauzaMinute;
    
    //caz in care un user nu are departamentul setat
    if ($item->DEPARTAMENT === null) {
        $item->DEPARTAMENT = 'N/A';
    }
    return $item;
});



    return view('raport', ['raportData' => $raportData]);
}


public function data(Request $request, $date)  // Functie similara pentru calculul timpului lucrat + numarul si durata pauzelor pentru o zi aleasa din datepicker si stocata in variabila $date

{
    $raportData = DB::table('utilizator')
        ->leftJoin('condica as intrare', function ($join) use ($date) {
            $join->on('utilizator.id', '=', 'intrare.user_id')
                ->where('intrare.stare', '=', 'intrare')
                ->whereDate('intrare.data_ora', '=', $date)
                ->orderBy('intrare.data_ora', 'asc'); 
        })
        ->leftJoin('condica as iesire', function ($join) use ($date) {
            $join->on('utilizator.id', '=', 'iesire.user_id')
                ->where('iesire.stare', '=', 'iesire')
                ->whereDate('iesire.data_ora', '=', $date)
                ->orderBy('iesire.data_ora', 'desc'); // DE STERS !!!!
        })
        ->select(
            'utilizator.id as ID',
            'utilizator.nume as NUME',
            DB::raw('MIN(intrare.data_ora) as CHECK_IN'),
            DB::raw('(SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "iesire" AND DATE(data_ora)= "'.$date.'" AND data_ora > (SELECT data_ora FROM condica WHERE user_id = utilizator.id AND stare = "intrare" AND DATE(data_ora) = "'.$date.'" ORDER BY data_ora LIMIT 1) ORDER BY data_ora LIMIT 1) as CHECK_OUT'), 
            'utilizator.departament as DEPARTAMENT',
            DB::raw("(SELECT COUNT(*) FROM condica WHERE user_id = utilizator.id AND stare = 'pauza' AND DATE(data_ora) = '$date') as PAUZA_COUNT")
        )
        ->groupBy('utilizator.id', 'utilizator.nume', 'utilizator.departament')
        ->get();

    if ($raportData->isEmpty()) {
        return response()->json(['error' => 'Nu exista date.'], 404);
    }

    $raportData = $raportData->map(function ($item) use($date) {
        $item->CHECK_IN = $item->CHECK_IN ?: 'N/A';
        $item->CHECK_OUT = $item->CHECK_OUT ?: 'N/A';

        if ($item->CHECK_IN && $item->CHECK_OUT) {
            $checkIn = strtotime($item->CHECK_IN);
            $checkOut = strtotime($item->CHECK_OUT);

            // Caz de impartire la 0
            if ($checkOut - $checkIn != 0) {
                $orePrezenta = ($checkOut - $checkIn) / 3600;
                $minutePrezenta = ($checkOut - $checkIn) / 60;

                if ($orePrezenta > 0) {
                    if ($orePrezenta > 1.1) {
                        $item->TIMP_LUCRAT = round($orePrezenta, 1) . ' ore';
                    } elseif ($orePrezenta >= 1 && $orePrezenta < 1.1) {
                        $item->TIMP_LUCRAT = round($orePrezenta, 1) . ' ora';
                    } else {
                        $item->TIMP_LUCRAT = round($minutePrezenta, 3) . ' minute';
                    }
                } else {
                    $item->TIMP_LUCRAT = 0;
                }

                $timpDeLucru = DB::table('utilizator')->where('id', $item->ID)->value('timp_de_lucru');

                if ($checkOut) {
                    if ($orePrezenta > $timpDeLucru) {
                        $item->PESTE_SUB_NORMA = 'PESTE :' . round($orePrezenta - $timpDeLucru, 1) ." ore";
                    } else {
                        $item->PESTE_SUB_NORMA = 'SUB :' . round($orePrezenta - $timpDeLucru, 1) ." ore";
                    }
                } else {
                    $item->PESTE_SUB_NORMA = 'N/A';
                }
            } else {
                $item->TIMP_LUCRAT = 0;
                $item->PESTE_SUB_NORMA = 'N/A';
            }
        } else {
            $item->TIMP_LUCRAT = 0;
            $item->PESTE_SUB_NORMA = 'N/A';
        }

        if ($item->DEPARTAMENT === null) {
            $item->DEPARTAMENT = 'N/A';
        }
        return $item;
    });

    foreach ($raportData as $item) {
        $totalPauza = 0;
        $pauzaStatus = DB::table('condica')
            ->select('id', 'data_ora', 'stare')
            ->where('user_id', $item->ID)
            ->whereIn('stare', ['pauza', 'intrare'])
            ->whereDate('data_ora', $date)
            ->orderBy('data_ora')
            ->get();
        
        $pauzaPereche = null;
        foreach ($pauzaStatus as $status) {
            if ($status->stare == 'pauza') {
                $pauzaPereche = $status;
            } elseif ($status->stare == 'intrare' && $pauzaPereche) {
                $pauzaStart = strtotime($pauzaPereche->data_ora);
                $intrare = strtotime($status->data_ora);
                if ($intrare > $pauzaStart) {
                    $durata = $intrare - $pauzaStart;
                    $totalPauza += $durata;
                }
                $pauzaPereche = null;
            }
        }
        
        $totalPauzaMinute = floor($totalPauza / 60);
        
        $item->PAUZA_DURATA =  $totalPauzaMinute;
    }

    return response()->json(['raportData' => $raportData, 'date' => $date]);
}


    
    
  public function lunar() //afisarea orelor lucrate pe parcursul lunii curente
    {
        
        // Preluam toti utilizatorii
        $users = DB::table('utilizator')->select('id', 'nume','departament')->get();

        // Preluam luna curenta
        $lunaCurenta = Carbon::now()->month;



        foreach ($users as $user) {
            // Preluam datele de intrare/iesire din baza de date pe luna aceasta
            $dataLunar = DB::table('condica')
                ->select('stare', 'data_ora')
                ->where('user_id', $user->id)
                ->whereMonth('data_ora', $lunaCurenta)
                ->orderBy('data_ora')
                ->get();

            // Calculul orelor totale
            $totalPrezentaOre = $this->calcul_total_prezenta($dataLunar);
            if (isset($user->departament)) { 
                $departament = $user->departament;
            } else {
                $departament = "N/A";
            }
            // Stocam orele totale pt user
            $prezentaData[$user->id] = [
                'nume' => $user->nume,
                'totalOre' => $totalPrezentaOre,
                'departament' => $departament,
            ];
        }
           
           

    return view('raportlunar', ['prezentaData' => $prezentaData]);
}
private function calcul_total_prezenta($dataLunar) //functia care calculeazaa timpul total lucrat
{
    $totalPrezentaOre = 0;

    $GroupData = $dataLunar->groupBy(function ($item) {
        return substr($item->data_ora, 0, 10); // Grupam dupa data: (YYYY-MM-DD)
    });

    foreach ($GroupData as $date => $records) {
        $primaIntrare = null;
        $iesire = null;
    
        // Loop pentru datele dintr-o zi
        foreach ($records as $record) {
            if ($record->stare === 'intrare') {
                // Setam prima intrare din ziua respectiva
                if ($primaIntrare === null) {
                    $primaIntrare = strtotime($record->data_ora);
                }
            } elseif ($record->stare === 'iesire') {
                // Verificam daca iesirea urmeaza dupa prima intrare
                if ($primaIntrare !== null && $iesire === null && strtotime($record->data_ora) > $primaIntrare) {
                    $iesire = strtotime($record->data_ora);
                }
            }
        }
    
        // Daca avem setata o intrare si o iesire in ziua respectiva calculam timpul lucrat
        if ($primaIntrare !== null && $iesire !== null) {
            $prezentaSecunde = $iesire - $primaIntrare;
            $timpPrezenta = max(0, $prezentaSecunde / 60); // Convertire din secunde in minute
            $totalPrezentaOre += $timpPrezenta;
        }
    }
    

    return (round($totalPrezentaOre/60, 2) . ' ore'); //rotunjim cu 2 zecimale
}


public function lunardata(Request $request, $date) //afisarea orelor lucrate pe parcursul lunii alese din datepicker
{
    $users = DB::table('utilizator')->select('id', 'nume', 'departament')->get();

    $prezentaData = [];

    // luna preluata
    $lunaCurenta = $date;

    foreach ($users as $user) {
        $dataLunar = DB::table('condica')
            ->select('stare', 'data_ora')
            ->where('user_id', $user->id)
            ->whereMonth('data_ora', $lunaCurenta)
            ->orderBy('data_ora')
            ->get();

        $totalPrezentaOre = $this->calcul_total_prezenta($dataLunar);
        if (isset($user->departament)) {
            $departament = $user->departament;
        } else {
            $departament = "N/A";
        }
        $prezentaData[] = [
            'id' => $user->id,
            'nume' => $user->nume,
            'totalOre' => $totalPrezentaOre,
            'departament' => $departament,
        ];
    }

    return response()->json(['prezentaData' => $prezentaData, 'date' => $date]);
}
}







