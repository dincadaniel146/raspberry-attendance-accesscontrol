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

    public function delete($id) //stergere utilizator
    {
        // Cautam utilizatorul dupa ID
        $user = DB::table('utilizator')->where('id', '=', $id)->delete();

        // Inapoi la pagina cu utilizatori alaturi de un mesaj de succes
        return redirect()->route('utilizatori')->with('success', 'Utilizatorul a fost șters!');
    }
    public function usercount()
    {
        //endpoint nr. total utilizatori
        $count= DB::table('utilizator')->count();
        return view('dashboard')->with('count',$count);
    }


    public function newuser(Request $request)
    {
        //Adaugarea unui utilizator nou, campurile nume si rfid_uid sunt necesare, timp_de_lucru este prestabilit la 8 ore
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

        return redirect()->route('utilizatori')->with('success', 'Utilizator adăugat !');
    }

    public function edit($id)
    {
        //Afisarea datelor in modalul de editare
$user=DB::table('utilizator')->find($id);
return view('utilizatori', compact('user'));

    }
    public function update(Request $request, $id)
    {
        //Actualizare utilizator
        DB::table('utilizator')->where('id', $id)->update($request->except(['_token', '_method','created']));

        return redirect()->route('utilizatori')->with('success', 'Utilizator actualizat !');
    }





    
}


