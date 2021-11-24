<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Casier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class CasierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Casier";
        $btnModalAjout = "FALSE";
        return view('parametre.casier.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeCasier()
    {
        $casiers = DB::table('casiers')
            ->select('casiers.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_casier', 'ASC')
            ->get();
        $jsonData["rows"] = $casiers->toArray();
        $jsonData["total"] = $casiers->count();
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
        if ($request->isMethod('post') && $request->input('libelle_casier')) {

            $data = $request->all();

            try {

                $request->validate([
                    'libelle_casier' => 'required',
                ]);
                $Casier = Casier::where([['libelle_casier', $data['libelle_casier']], ['id', '!=', $data['id']]])->first();
                if ($Casier != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }
                $casier = $data['id'] ? Casier::findOrFail($data['id']) : new Casier;

                $casier->libelle_casier = $data['libelle_casier'];
                $casier->created_by = Auth::user()->id;
                $casier->save();
                $jsonData["data"] = json_decode($casier);
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
     * @param  \App\Casier  $casier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Casier $casier)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($casier) {
            try {

                $casier->update(['deleted_by' => Auth::user()->id]);
                $casier->delete();
                $jsonData["data"] = json_decode($casier);
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
