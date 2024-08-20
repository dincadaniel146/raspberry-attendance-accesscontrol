<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function IntrariIesiri()
    {
        // Fetch the 5 most recent entries (intrari)
        $intrari = DB::table('condica')
            ->select('user_id', 'nume', 'stare', 'data_ora')
            ->whereDate('data_ora', now()->toDateString())
            ->where('stare', 'intrare')
            ->orderBy('data_ora', 'desc')
            ->limit(4)
            ->get();
    
        // Fetch the 5 most recent exits (iesiri)
        $iesiri = DB::table('condica')
            ->select('user_id', 'nume', 'stare', 'data_ora')
            ->whereDate('data_ora', now()->toDateString())
            ->where('stare', 'iesire')
            ->orderBy('data_ora', 'desc')
            ->limit(4)
            ->get();
    
        // Pass both sets of data to the 'dashboard' view
        return view('dashboard')->with('intrari', $intrari)->with('iesiri', $iesiri);
    }
    
    
    
    public function userCount()
    {
        $userCount = DB::table('utilizator')->count();
        return response()->json(['userCount' => $userCount]);
    }
    public function date()
    {
        Carbon::setLocale('ro');
        $data = Carbon::now()->timezone('Europe/Bucharest')->isoFormat('dddd, D MMMM YYYY');
        return response()->json(['data'=> $data]);
    }
    public function checkInCount()
    {
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
        $users = DB::table('utilizator')->get();
        $absentUserCount = 0;
    
        foreach ($users as $user) {
            $intrare = DB::table('condica')
                ->where('user_id', $user->id)
                ->where('stare', 'intrare') // Check for 'intrare' status
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
