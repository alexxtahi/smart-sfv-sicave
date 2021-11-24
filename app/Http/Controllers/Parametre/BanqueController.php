<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Banque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class BanqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Banque";
        $btnModalAjout = "FALSE";
        return view('parametre.banque.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeBanque()
    {
        $banques = DB::table('banques')
            ->select('banques.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_banque', 'ASC')
            ->get();
        $jsonData["rows"] = $banques->toArray();
        $jsonData["total"] = $banques->count();
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
        if ($request->isMethod('post') && $request->input('libelle_banque')) {

            $data = $request->all();

            try {

                $Banque = Banque::where([['libelle_banque', $data['libelle_banque']], ['id', '!=', $data['id']]])->first();
                if ($Banque != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }
                $banque = $data['id'] ? Banque::findOrFail($data['id']) : new Banque;

                $banque->libelle_banque = $data['libelle_banque'];
                $banque->created_by = Auth::user()->id;
                $banque->save();
                $jsonData["data"] = json_decode($banque);
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
     * @param  \App\Banque  $banque
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banque $banque)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($banque) {
            try {

                $banque->update(['deleted_by' => Auth::user()->id]);
                $banque->delete();
                $jsonData["data"] = json_decode($banque);
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
