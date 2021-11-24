<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UniteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Unité";
        $btnModalAjout = "FALSE";
        return view('parametre.unite.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeUnite()
    {
        $unites = DB::table('unites')
            ->select('unites.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_unite', 'ASC')
            ->get();
        $jsonData["rows"] = $unites->toArray();
        $jsonData["total"] = $unites->count();
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
        if ($request->isMethod('post') && $request->input('libelle_unite')) {

            $data = $request->all();

            try {
                $Unite = Unite::where([['libelle_unite', $data['libelle_unite']], ['id', '!=', $data['id']]])->first();
                if ($Unite != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }

                $unite = $data['id'] ? Unite::findOrFail($data['id']) : new Unite;

                $unite->libelle_unite = $data['libelle_unite'];
                $unite->quantite_unite = isset($data['quantite_unite']) && !empty($data['quantite_unite']) ? $data['quantite_unite'] : null;
                $unite->created_by = Auth::user()->id;
                $unite->save();
                $jsonData["data"] = json_decode($unite);
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
     * @param  \App\Unite  $unite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unite $unite)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($unite) {
            try {

                $unite->update(['deleted_by' => Auth::user()->id]);
                $unite->delete();
                $jsonData["data"] = json_decode($unite);
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
