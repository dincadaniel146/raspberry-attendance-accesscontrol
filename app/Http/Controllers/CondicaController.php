<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CondicaController extends Controller
{
    public function index()
    {
        $condica = DB::table('condica')
        ->whereDate('data_ora', now()->toDateString())
        ->paginate(10);
        return view('condica')->with('condica', $condica);
    }
    public function showAttendance(Request $request, $date)
{
    $attendanceData = DB::table('condica')
        ->whereDate('data_ora', $date)
        ->paginate(10);
        

        
    return view('condica.index')->with('attendanceData',$attendanceData);
}


}
