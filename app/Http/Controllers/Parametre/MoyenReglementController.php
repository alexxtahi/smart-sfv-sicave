<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\MoyenReglement;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MoyenReglementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Moyen de payement";
        $btnModalAjout = "FALSE";
        return view('parametre.moyen-reglement.index', compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeMoyenReglement()
    {
        $moyenReglements = DB::table('moyen_reglements')->select('moyen_reglements.*')->Where('deleted_at', null)->orderBy('moyen_reglements.libelle_moyen_reglement', 'ASC')->get();

        $jsonData["rows"] = $moyenReglements->toArray();
        $jsonData["total"] = $moyenReglements->count();

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
        if ($request->isMethod('post') && $request->input('libelle_moyen_reglement')) {

            $data = $request->all();

            try {


                $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
                $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
                $libelle = str_replace($search, $replace, $data['libelle_moyen_reglement']);

                $MoyenReglement = MoyenReglement::where([['libelle_moyen_reglement', $libelle], ['id', '!=', ($data['id'])]])->first();
                if ($MoyenReglement != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }

                $moyenReglement = $data['id'] ? MoyenReglement::findOrFail($data['id']) : new MoyenReglement;

                $moyenReglement->libelle_moyen_reglement = strtoupper($libelle);
                $moyenReglement->created_by = Auth::user()->id;
                $moyenReglement->save();

                $jsonData["data"] = json_decode($moyenReglement);
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
     * @param  \App\MoyenReglement  $moyenReglement
     * @return Response
     */
    public function destroy(MoyenReglement $moyenReglement)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($moyenReglement) {
            try {

                $moyenReglement->update(['deleted_by' => Auth::user()->id]);
                $moyenReglement->delete();
                $jsonData["data"] = json_decode($moyenReglement);
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
