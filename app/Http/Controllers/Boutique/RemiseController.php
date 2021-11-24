<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\Remise;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemiseController extends Controller
{

    public function listeRemise($vente)
    {
        $remises = DB::table('remises')->select('remises.*')->Where([['deleted_at', null], ['vente_id', $vente]])->get();

        $jsonData["rows"] = $remises->toArray();
        $jsonData["total"] = $remises->count();

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
        if ($request->isMethod('post') && $request->input('libelle_remise')) {

            $data = $request->all();

            try {

                $remise = new Remise;
                $remise->libelle_remise = $data['libelle_remise'];
                $remise->montan_remise = $data['montan_remise'];
                $remise->vente_id = $data['idVente'];
                $remise->created_by = Auth::user()->id;
                $remise->save();

                $jsonData["data"] = json_decode($remise);
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
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Remise  $remise
     * @return Response
     */
    public function updateRemise(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        $remise = Remise::find($request->get('idRemise'));

        if ($remise) {
            try {

                $remise->update([
                    'libelle_remise' => $request->get('libelle_remise'),
                    'montan_remise' => $request->get('montan_remise'),
                    'vente_id' => $request->get('idVente'),
                    'updated_by' => Auth::user()->id,
                ]);

                $jsonData["data"] = json_decode($remise);

                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Echec de modification", "data" => null]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Remise  $remise
     * @return Response
     */
    public function destroy(Remise $remise)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($remise) {
            try {

                $remise->update(['deleted_by' => Auth::user()->id]);
                $remise->delete();

                $jsonData["data"] = json_decode($remise);

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
