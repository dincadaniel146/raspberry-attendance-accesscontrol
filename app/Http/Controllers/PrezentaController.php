<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PrezentaController extends Controller
{


    public function index() //calcul prezenta pentru ziua curenta
    {
        $users = DB::table('utilizator')->get();
        $prezenta = []; //array unde stocham datele pentru utilizator
        
        foreach ($users as $user) {
            $intrare = DB::table('condica')
                ->where('user_id', $user->id)
                ->where('stare', 'intrare')
                ->whereDate('data_ora', now()->toDateString())
                ->orderBy('data_ora')
                ->first();
                
            // Verificam daca user-ul are un departament setat
            if (isset($user->departament)) {
                $departament = $user->departament;
            } else {
                // Daca nu, este setata o valoare default
                $departament = 'N/A';
            }    
    
            // Verificam daca avem o intrare in ziua respectiva si are proprietatea data_ora
            if ($intrare && isset($intrare->data_ora)) {
                // Daca un utilizator are o stare de intrare este considerat prezent si stocham in array datele respective
                $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Da' , 'data_ora'=>$intrare->data_ora , 'departament'=> $departament];
            } else {
                $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Nu', 'departament'=>$departament ];
            }
        }
        
        return view('prezenta', ['prezenta' => $prezenta, 'users' => $users]);
    }
    

    

    
    public function data(Request $request, $date)//calcul prezenta pentru o zi aleasa din datepicker
{

    $users = DB::table('utilizator')->get();

    $prezenta = []; 
    foreach ($users as $user) {
        $intrare = DB::table('condica')
            ->where('user_id', $user->id)
            ->where('stare', 'intrare')
            ->whereDate('data_ora', $date)
            ->orderBy('data_ora')
            ->first();
            if (isset($user->departament)) {

                $departament = $user->departament;
            } else {

                $departament = 'N/A'; 
            }
        if ($intrare) {
            $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Da' , 'data_ora' => $intrare->data_ora , 'departament' => $departament];
        } else {
            $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Nu' , 'departament' =>$departament];
        }
    }
    return response()->json(['prezenta' => $prezenta, 'date' => $date]);
}

public function lunar(Request $request) //calcul numarului de prezente pentru luna curenta
{
    $users = DB::table('utilizator')->get();
    $prezenta=[];
    $lunaCurenta = Carbon::now()->month; //preluam luna curenta
    foreach ($users as $user) {
        $intrariCount = DB::table('condica')
            ->where('user_id', $user->id)        
            ->whereMonth('data_ora', $lunaCurenta)
            ->select(DB::raw('COUNT(DISTINCT DATE(data_ora)) as intrari_count')) //selectam o intrare in ziua respectiva 
            ->first(); 
            if (isset($user->departament)) {
                $departament = $user->departament;
            } else {
                $departament = 'N/A'; 
            }
        $prezenta[] = ['nume' => $user->nume,'departament'=>$departament,'id'=> $user->id, 'prezente'=> $intrariCount->intrari_count];
        
}
return view('prezentalunara', ['prezenta' => $prezenta]);
}

public function lunardata(Request $request, $date) //calcul numarului de prezente pentru o luna aleasa din datepicker
{
    $users = DB::table('utilizator')->get();
    $prezenta=[];

    foreach ($users as $user) {
        $intrariCount = DB::table('condica')
            ->where('user_id', $user->id)
            ->whereMonth('data_ora', $date) 
            ->select(DB::raw('COUNT(DISTINCT DATE(data_ora)) as intrari_count'))
            ->first(); 
            if (isset($user->departament)) {
                $departament = $user->departament;
            } else {
                $departament = 'N/A'; 
            }
        $prezenta[] = ['nume' => $user->nume,'id'=> $user->id,'departament'=>$departament, 'prezente' => $intrariCount->intrari_count ?? 0];
    }
    return response()->json(['prezentalunara' => $prezenta, 'date' => $date]);
}

}