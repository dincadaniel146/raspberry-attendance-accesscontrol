<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PrezentaController extends Controller
{


    public function index()
    {
        $users = DB::table('utilizator')->get();
        $prezenta = [];
        
        foreach ($users as $user) {
            $intrare = DB::table('condica')
                ->where('user_id', $user->id)
                ->where('stare', 'intrare')
                ->whereDate('data_ora', now()->toDateString())
                ->orderBy('data_ora')
                ->first();
                
            // Check if 'departament' key exists in the $user object
            if (isset($user->departament)) {
                // If 'departament' exists, use it
                $departament = $user->departament;
            } else {
                // If 'departament' doesn't exist, assign a default value or handle it as needed
                $departament = 'N/A'; // Assigning a default value
            }    
    
            // Check if $intrare exists and has 'data_ora' property
            if ($intrare && isset($intrare->data_ora)) {
                // If 'intrare' record exists and 'data_ora' is set, add user's attendance data
                $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Da' , 'data_ora'=>$intrare->data_ora , 'departament'=> $departament];
            } else {
                // If 'intrare' record doesn't exist or 'data_ora' is not set, mark user as absent
                $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Nu', 'departament'=>$departament ];
            }
        }
        
        return view('prezenta', ['prezenta' => $prezenta, 'users' => $users]);
    }
    


    
    public function data(Request $request, $date)
{

    $users = DB::table('utilizator')->get();

    $prezenta = []; // Sample data, replace it with your actual implementation
    foreach ($users as $user) {
        $intrare = DB::table('condica')
            ->where('user_id', $user->id)
            ->where('stare', 'intrare')
            ->whereDate('data_ora', $date)
            ->orderBy('data_ora')
            ->first();
            if (isset($user->departament)) {
                // If 'departament' exists, use it
                $departament = $user->departament;
            } else {
                // If 'departament' doesn't exist, assign a default value or handle it as needed
                $departament = 'N/A'; // Assigning a default value
            }
        if ($intrare) {
            $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Da' , 'data_ora' => $intrare->data_ora , 'departament' => $departament];
        } else {
            $prezenta[] = ['nume' => $user->nume,'id'=> $user->id, 'stare' => 'Nu' , 'departament' =>$departament];
        }
    }
    // Return the attendance data as JSON
    return response()->json(['prezenta' => $prezenta, 'date' => $date]);
}
public function lunar(Request $request)
{
    $users = DB::table('utilizator')->get();
    $prezenta=[];
    $currentMonth = Carbon::now()->month;
    foreach ($users as $user) {
        $userIntrariCounts = DB::table('condica')
            ->where('user_id', $user->id)        
            ->whereMonth('data_ora', $currentMonth)
            ->select(DB::raw('COUNT(DISTINCT DATE(data_ora)) as intrari_count'))
            ->first(); 
            if (isset($user->departament)) {
                // If 'departament' exists, use it
                $departament = $user->departament;
            } else {
                // If 'departament' doesn't exist, assign a default value or handle it as needed
                $departament = 'N/A'; // Assigning a default value
            }
        $prezenta[] = ['nume' => $user->nume,'departament'=>$departament,'id'=> $user->id, 'prezente'=> $userIntrariCounts->intrari_count];
        
}
return view('prezentalunara', ['prezenta' => $prezenta]);
}

public function lunardata(Request $request, $date)
{
    $users = DB::table('utilizator')->get();
    $prezenta=[];
   // $selectedMonth = Carbon::create(null, $date, 1)->format('m');

    foreach ($users as $user) {
        $userIntrariCounts = DB::table('condica')
            ->where('user_id', $user->id)
            ->whereMonth('data_ora', $date) 
            ->select(DB::raw('COUNT(DISTINCT DATE(data_ora)) as intrari_count'))
            ->first(); 
            if (isset($user->departament)) {
                // If 'departament' exists, use it
                $departament = $user->departament;
            } else {
                // If 'departament' doesn't exist, assign a default value or handle it as needed
                $departament = 'N/A'; // Assigning a default value
            }
        $prezenta[] = ['nume' => $user->nume,'id'=> $user->id,'departament'=>$departament, 'prezente' => $userIntrariCounts->intrari_count ?? 0];
    }
    return response()->json(['prezentalunara' => $prezenta, 'date' => $date]);
}

}