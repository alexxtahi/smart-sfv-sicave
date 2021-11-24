<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Caisse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaisseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Caisse";
        $btnModalAjout = "FALSE";
        return view('parametre.caisse.index', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeCaisse()
    {
        $caisses = Caisse::with('depot')
            ->select('caisses.*')
            ->Where('caisses.deleted_at', null)
            ->orderBy('caisses.libelle_caisse', 'ASC')
            ->get();

        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }

    public function listeCaisseById($id){
        $caisses = Caisse::select('caisses.*')
                            ->Where([['caisses.deleted_at', null],['caisses.id',$id]])
                            ->get();

        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('libelle_caisse')) {

            $data = $request->all();

            try {

                $Caisse = Caisse::where([['libelle_caisse', $data['libelle_caisse']], ['depot_id', $data['depot_id']], ['id', '!=', $data['id']]])->first();
                if ($Caisse != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }

                $caisse = $data['id'] ? Caisse::findOrFail($data['id']) : new Caisse;

                $caisse->libelle_caisse = $data['libelle_caisse'];
                $caisse->depot_id = $data['depot_id'];
                $caisse->created_by = Auth::user()->id;
                $caisse->save();

                $jsonData["data"] = json_decode($caisse);
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
     * @param  \App\Caisse  $caisse
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $caisse = Caisse::find($id);
        if ($caisse) {
            try {

                $caisse->update(['deleted_by' => Auth::user()->id]);
                $caisse->delete();

                $jsonData["data"] = json_decode($caisse);

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
