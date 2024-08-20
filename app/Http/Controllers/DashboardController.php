<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function IntrariIesiri()
    {
        // 5 cele mai recente intrari
        $intrari = DB::table('condica')
            ->select('user_id', 'nume', 'stare', 'data_ora')
            ->whereDate('data_ora', now()->toDateString())
            ->where('stare', 'intrare')
            ->orderBy('data_ora', 'desc')
            ->limit(4)
            ->get();
    
        // 5 cele mai recente iesiri
        $iesiri = DB::table('condica')
            ->select('user_id', 'nume', 'stare', 'data_ora')
            ->whereDate('data_ora', now()->toDateString())
            ->where('stare', 'iesire')
            ->orderBy('data_ora', 'desc')
            ->limit(4)
            ->get();
    

        return view('dashboard')->with('intrari', $intrari)->with('iesiri', $iesiri);
    }
    
    
    
    public function userCount()
    {
        //endpoin nr. total user
        $userCount = DB::table('utilizator')->count();
        return response()->json(['userCount' => $userCount]);
    }
    public function date()
    {
        //afisare data de astazi
        Carbon::setLocale('ro');
        $data = Carbon::now()->timezone('Europe/Bucharest')->isoFormat('dddd, D MMMM YYYY');
        return response()->json(['data'=> $data]);
    }
    public function checkInCount()
    {
        //numar utilizatori care au dat check-in astazi
        $id_exclus= 0;
        $checkInCount = DB::table('condica')
        ->select('user_id')
        ->whereDate('data_ora', now()->toDateString())
        ->where('stare', 'intrare')
        ->where('user_id','!=',$id_exclus)
        ->groupBy('user_id')
        ->havingRaw('MIN(data_ora) IS NOT NULL')
        ->count();
        
        return response()->json(['checkInCount' => $checkInCount]);
    }
    public function checkOutCount()
    {
        //numar utilizatori care au dat check-out astazi
        $id_exclus= 0;
        $checkOutCount = DB::table('condica')
        ->select('user_id')
        ->whereDate('data_ora', now()->toDateString())
        ->where('stare', 'iesire')
        ->where('user_id','!=',$id_exclus)
        ->groupBy('user_id')
        ->havingRaw('MAX(data_ora) IS NOT NULL')
        ->count();
        
        return response()->json(['checkOutCount' => $checkOutCount]);
    }
    public function AbsentiCount()
    {
        //numar utilizatori care nu au dat check-in astazi
        $users = DB::table('utilizator')->get();
        $absentUserCount = 0;
    
        foreach ($users as $user) {
            $intrare = DB::table('condica')
                ->where('user_id', $user->id)
                ->where('stare', 'intrare') 
                ->whereDate('data_ora', now()->toDateString())
                ->orderBy('data_ora')
                ->first();
    
            if (!$intrare) {
                $absentUserCount++;
            }
        }
    
        return response()->json(['AbsentiCount' => $absentUserCount]);
    }
    

}