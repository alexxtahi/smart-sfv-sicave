<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Nation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class NationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Pays";
        $btnModalAjout = "FALSE";
        return view('parametre.nation.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeNation()
    {
        $nations = DB::table('nations')
            ->select('nations.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_nation', 'ASC')
            ->get();
        $jsonData["rows"] = $nations->toArray();
        $jsonData["total"] = $nations->count();
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
        if ($request->isMethod('post') && $request->input('libelle_nation')) {

            $data = $request->all();

            try {

                $Nation = Nation::where([['libelle_nation', $data['libelle_nation']], ['id', '!=', $data['id']]])->first();
                if ($Nation != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }
                $nation = $data['id'] ? Nation::findOrFail($data['id']) : new Nation;

                $nation->libelle_nation = $data['libelle_nation'];
                $nation->created_by = Auth::user()->id;
                $nation->save();
                $jsonData["data"] = json_decode($nation);
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
     * @param  \App\Nation  $nation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nation $nation)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($nation) {
            try {

                $nation->update(['deleted_by' => Auth::user()->id]);
                $nation->delete();
                $jsonData["data"] = json_decode($nation);
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
