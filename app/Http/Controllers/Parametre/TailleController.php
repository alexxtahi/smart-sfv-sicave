<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Taille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class TailleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Taille";
        $btnModalAjout = "FALSE";
        return view('parametre.taille.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeTaille()
    {
        $tailles = DB::table('tailles')
            ->select('tailles.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_taille', 'ASC')
            ->get();
        $jsonData["rows"] = $tailles->toArray();
        $jsonData["total"] = $tailles->count();
        return response()->json($jsonData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('libelle_taille')) {

            $data = $request->all();

            try {

                $Taille = Taille::where([['libelle_taille', $data['libelle_taille']], ['id', '!=', $data['id']]])->first();
                if ($Taille != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }

                $taille = $data['id'] ? Taille::findOrFail($data['id']) : new Taille;

                $taille->libelle_taille = $data['libelle_taille'];
                $taille->created_by = Auth::user()->id;
                $taille->save();
                $jsonData["data"] = json_decode($taille);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => null]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Taille  $taille
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taille $taille)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($taille) {
            try {

                $taille->update(['deleted_by' => Auth::user()->id]);
                $taille->delete();
                $jsonData["data"] = json_decode($taille);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Echec de suppression", "data" => null]);
    }
}
