<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\DetailInventaire;
use App\Models\Stock\Inventaire;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ZipStream\Exception;

class DetailInventaireController extends Controller
{

    public function listeDetailsInventaire($inventaire)
    {
        $details = DetailInventaire::with('article', 'unite')
            ->select('detail_inventaires.*', DB::raw('DATE_FORMAT(detail_inventaires.date_peremption, "%d-%m-%Y") as date_peremptions'))
            ->Where([['detail_inventaires.deleted_at', null], ['detail_inventaires.inventaire_id', $inventaire]])
            ->get();
        $jsonData["rows"] = $details->toArray();
        $jsonData["total"] = $details->count();
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
        if ($request->isMethod('post') && $request->input('article_id')) {

            $data = $request->all();

            try {
                //Récuperation des dépôts
                $inventaire = Inventaire::find($data['inventaire_id']);
                $depot_id = $inventaire->depot_id;

                if (!isset($data["depot_id"]) or $inventaire->depot_id != $data['depot_id']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }

                $DetailInventaire = DetailInventaire::where([['inventaire_id', $data['inventaire_id']], ['article_id', $data['article_id']]])->first();
                if ($DetailInventaire != null) {
                    $DetailInventaire->quantite_denombree = $DetailInventaire->quantite_denombree + $data['quantite_denombree'];
                    $DetailInventaire->save();
                    $detailInventaires = $DetailInventaire;
                } else {
                    $detailInventaire = new DetailInventaire;
                    $detailInventaire->article_id = $data["article_id"];
                    $detailInventaire->inventaire_id = $inventaire->id;
                    $detailInventaire->quantite_en_stocke = $data["quantite_en_stocke"];
                    $detailInventaire->quantite_denombree = $data["quantite_denombree"];
                    $detailInventaire->date_peremption = isset($data["date_peremption"]) && !empty($data["date_peremption"]) ? Carbon::createFromFormat('d-m-Y', $data["date_peremption"]) : null;
                    $detailInventaire->created_by = Auth::user()->id;
                    $detailInventaire->save();
                    $detailInventaires = $detailInventaire;
                }

                //Ajustement du stock
                $DepotArticle = DepotArticle::where([['depot_id', $depot_id], ['article_id', $data["article_id"]]])->first();
                $DepotArticle->quantite_disponible = $data["quantite_denombree"];
                $DepotArticle->date_peremption = isset($data["date_peremption"]) && !empty($data["date_peremption"]) ? Carbon::createFromFormat('d-m-Y', $data["date_peremption"]) : null;
                $DepotArticle->save();

                $jsonData["data"] = json_decode($detailInventaires);
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
     * @param  \App\DetailInventaire  $detailInventaire
     * @return Response
     */
    public function update(Request $request, DetailInventaire $detailInventaire)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];

        if ($detailInventaire) {
            $data = $request->all();
            try {

                //Récuperation des dépôts
                $inventaire = Inventaire::find($detailInventaire->inventaire_id);
                $depot_id = $inventaire->depot_id;

                if (!isset($data["depot_id"]) or $inventaire->depot_id != $data['depot_id']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }

                if (($detailInventaire->article_id != $data["article_id"])) {
                    $DepotArticle = DepotArticle::where([['depot_id', $depot_id], ['article_id', $detailInventaire->article_id]])->first();
                    $DepotArticle->quantite_disponible = $detailInventaire->quantite_en_stocke;
                    $DepotArticle->save();
                }


                $detailInventaire->article_id = $data["article_id"];
                $detailInventaire->inventaire_id = $inventaire->id;
                $detailInventaire->quantite_en_stocke = $data["quantite_en_stocke"];
                $detailInventaire->quantite_denombree = $data["quantite_denombree"];
                $detailInventaire->date_peremption = isset($data["date_peremption"]) && !empty($data["date_peremption"]) ? Carbon::createFromFormat('d-m-Y', $data["date_peremption"]) : null;
                $detailInventaire->updated_by = Auth::user()->id;
                $detailInventaire->save();

                //Ajustement du stock
                $newDepotArticle = DepotArticle::where([['depot_id', $depot_id], ['article_id', $data["article_id"]]])->first();
                $newDepotArticle->quantite_disponible = $data["quantite_denombree"];
                $newDepotArticle->date_peremption = isset($data["date_peremption"]) && !empty($data["date_peremption"]) ? Carbon::createFromFormat('d-m-Y', $data["date_peremption"]) : null;
                $newDepotArticle->save();

                $jsonData["data"] = json_decode($detailInventaire);
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
     * @param  \App\DetailInventaire  $detailInventaire
     * @return Response
     */
    public function destroy(DetailInventaire $detailInventaire)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($detailInventaire) {
            try {
                //Récuperation des dépôts
                $inventaire = Inventaire::find($detailInventaire->inventaire_id);
                $depot_id = $inventaire->depot_id;

                $DepotArticle = DepotArticle::where([['depot_id', $depot_id], ['article_id', $detailInventaire->article_id]])->first();
                $DepotArticle->quantite_disponible = $detailInventaire->quantite_en_stocke;
                $DepotArticle->save();

                $detailInventaire->update(['deleted_by' => Auth::user()->id]);
                $detailInventaire->delete();
                $jsonData["data"] = json_decode($detailInventaire);
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
