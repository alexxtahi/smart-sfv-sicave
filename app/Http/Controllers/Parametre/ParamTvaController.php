<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\ParamTva;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParamTvaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Gestion TVA";
        $btnModalAjout = "FALSE";
        return view('parametre.param-tva.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeParamTva()
    {
        $param_tvas = DB::table('param_tvas')
            ->select('param_tvas.*', DB::raw('(param_tvas.tva*100) as tva_convertis'))
            ->Where('deleted_at', null)
            ->get();
        $jsonData["rows"] = $param_tvas->toArray();
        $jsonData["total"] = $param_tvas->count();
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
        if ($request->isMethod('post') && $request->input('tva')) {

            $data = $request->all();

            try {

                $ParamTva = ParamTva::where([['tva', $data['tva'] / 100], ['id', '!=', $data['id']]])->first();
                if ($ParamTva != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }

                $paramTva = $data['id'] ? ParamTva::findOrFail($data['id']) : new ParamTva;

                $paramTva->tva = ($data['tva'] / 100);
                $paramTva->created_by = Auth::user()->id;
                $paramTva->save();
                $jsonData["data"] = json_decode($paramTva);
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
     * @param  \App\ParamTva  $paramTva
     * @return Response
     */
    public function destroy($id)
    {
        $paramTva = ParamTva::find($id);

        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($paramTva) {
            try {

                $paramTva->update(['deleted_by' => Auth::user()->id]);
                $paramTva->delete();
                $jsonData["data"] = json_decode($paramTva);
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
