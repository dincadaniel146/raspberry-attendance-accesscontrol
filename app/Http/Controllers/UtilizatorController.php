<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilizatorController extends Controller
{
    public function index(Request $request)
{
    

    $users = DB::table('utilizator')->paginate(10);
    return view('utilizatori')->with('users', $users);
}

    public function destroy($id)
    {
        // Find the user by ID
        $user = DB::table('utilizator')->where('id', '=', $id)->delete();

        // Redirect back to the page where the users are listed
        return redirect()->route('utilizatori')->with('success', 'Utilizatorul a fost sters.');
    }
    public function usercount()
    {
        $count= DB::table('utilizator')->count();
        return view('dashboard')->with('count',$count);
    }


    public function newuser(Request $request)
    {
        $request->validate([
            'nume' => 'required|string',
            'rfid_uid' => 'required|string',
            'departament' => 'nullable|string',
            'timp_de_lucru' => 'nullable|integer|min:0',
            'email' => 'nullable|string'
        ]);

        DB::table('utilizator')->insert([
            'nume' => $request->input('nume'),
            'rfid_uid' => $request->input('rfid_uid'),
            'departament' => $request->input('departament'),
            'timp_de_lucru' => $request->input('working_hours_expected', 8),
            'email' => $request->input('email')
        ]);

        return redirect()->route('utilizatori')->with('success', 'Utilizator adaugat !');
    }

    public function edit($id)
    {
$user=DB::table('utilizator')->find($id);
return view('utilizatori', compact('user'));

    }
    public function update(Request $request, $id)
    {
        DB::table('utilizator')->where('id', $id)->update($request->except(['_token', '_method','created']));

        return redirect()->route('utilizatori')->with('success', 'Utilizator actualizat !');
    }
    public function search(Request $request)
{
    $search = $request->input('search');
    // Perform your search query here
    $results = DB::table('utilizator')
                    ->where('nume', 'LIKE', "%$search%")
                    ->get();
    return view('utilizatori.index', ['results' => $results]);

}
public function autosuggest(Request $request)
{
    $query = $request->input('query');

    // Perform a query to retrieve autosuggestions based on the search query
    $suggestions = DB::table('utilizator')->where('nume', 'like', "%$query%")->pluck('nume');

    return response()->json($suggestions);
}



    
}



