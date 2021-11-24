<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\ArticleDestocker;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\Destockage;
use App\Models\Stock\MouvementStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleDestockerController extends Controller
{
    public function listeArticleDestockers($destockage_id)
    {
        $articleTransferts = ArticleDestocker::with('article', 'unite')
            ->select('article_destockers.*')
            ->Where([['article_destockers.deleted_at', null], ['article_destockers.destockage_id', $destockage_id]])
            ->get();
        $jsonData["rows"] = $articleTransferts->toArray();
        $jsonData["total"] = $articleTransferts->count();
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
                $destockage = Destockage::find($data['destockage_id']);
                $depot_id = $destockage->depot_id;

                if (!isset($data["depot_id"]) or $destockage->depot_id != $data['depot_id']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }

                $depot = DepotArticle::where([['depot_id', $depot_id], ['article_id', $data["article_id"]]])->first();
                //On vérifie la quantité du stock
                if ($depot != null && $data['quantite'] > $depot->quantite_disponible) {
                    return response()->json(["code" => 0, "msg" => "La quantite à déstocker est supérieure à la quantité disponible en stock qui est " . $depot->quantite_disponible, "data" => null]);
                }

                $ArticleDestocker = ArticleDestocker::where([['destockage_id', $data['destockage_id']], ['article_id', $data['article_id']]])->first();
                if ($ArticleDestocker != null) {
                    $ArticleDestocker->quantite_destocker = $ArticleDestocker->quantite_destocker + $data['quantite'];
                    $ArticleDestocker->save();
                    $articleDestockers = $ArticleDestocker;
                } else {
                    $articleDestocker = new ArticleDestocker;
                    $articleDestocker->article_id = $data["article_id"];
                    $articleDestocker->quantite_destocker = $data['quantite'];
                    $articleDestocker->destockage_id = $destockage->id;
                    $articleDestocker->created_by = Auth::user()->id;
                    $articleDestocker->save();
                    $articleDestockers = $articleDestocker;
                }
                //Traitement sur le stock dans depot-article 
                if ($articleDestockers != null) {
                    $DepotArticle = DepotArticle::where([['depot_id', $depot_id], ['article_id', $data["article_id"]]])->first();
                    $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["article_id"]]])->whereDate('date_mouvement', $destockage->date_destockage)->first();

                    if (!$mouvementStock) {
                        $mouvementStock = new MouvementStock;
                        $mouvementStock->date_mouvement = $destockage->date_destockage;
                        $mouvementStock->depot_id = $depot_id;
                        $mouvementStock->article_id = $data["article_id"];
                        $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                        $mouvementStock->created_by = Auth::user()->id;
                    }
                    $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $data['quantite'];
                    $DepotArticle->save();
                    $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker + $data['quantite'];
                    $mouvementStock->save();
                }
                $jsonData["data"] = json_decode($articleDestockers);
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
     * @param  \App\ArticleDestocker  $articleDestocker
     * @return Response
     */
    public function update(Request $request, ArticleDestocker $articleDestocker)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];

        if ($articleDestocker) {
            $data = $request->all();
            try {

                //Récuperation de l'ancien dépôt
                $destockage = Destockage::find($articleDestocker->destockage_id);
                $depot_id = $destockage->depot_id;
                if (!isset($data["depot_id"]) or $destockage->depot_id != $data['depot_id']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }

                //Ajustement des stocks dans depot-article
                $depot = DepotArticle::where([['depot_id', $depot_id], ['article_id', $articleDestocker->article_id]])->first();

                $depot->quantite_disponible = $depot->quantite_disponible + $articleDestocker->quantite_destocker;
                $depot->save();

                $mouvementStock = MouvementStock::where([['depot_id', $depot_id], ['article_id', $articleDestocker->article_id]])->whereDate('date_mouvement', $destockage->date_destockage)->first();
                $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker - $articleDestocker->quantite_destocker;
                $mouvementStock->save();

                //On vérifie la quantité du stock
                if ($data['quantite'] > $depot->quantite_disponible) {
                    $depot->quantite_disponible = $depot->quantite_disponible - $articleDestocker->quantite_destocker;
                    $depot->save();
                    return response()->json(["code" => 0, "msg" => "La quantite à déstocker est supérieure à la quantité disponible en stock qui est " . $depot->quantite_disponible, "data" => null]);
                }

                $articleDestocker->article_id = $data["article_id"];
                $articleDestocker->quantite_destocker = $data['quantite'];
                $articleDestocker->updated_by = Auth::user()->id;
                $articleDestocker->save();

                //Traitement sur les stocks dans depot-article 
                if ($articleDestocker != null) {
                    $newDepot = DepotArticle::where([['depot_id', $depot_id], ['article_id', $data["article_id"]]])->first();
                    if ($newDepot != null) {
                        $newDepot->quantite_disponible = $newDepot->quantite_disponible - $data["quantite"];
                        $newDepot->save();
                    }

                    $mouvementStock = MouvementStock::where([['depot_id', $depot_id], ['article_id', $data["article_id"]]])->whereDate('date_mouvement', $destockage->date_destockage)->first();
                    $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker + $articleDestocker->quantite_destocker;
                    $mouvementStock->save();
                }

                $jsonData["data"] = json_decode($articleDestocker);
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
     * @param  \App\ArticleDestocker  $articleDestocker
     * @return Response
     */
    public function destroy(ArticleDestocker $articleDestocker)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($articleDestocker) {
            try {
                //Récuperation de l'ancien dépôt
                $destockage = Destockage::find($articleDestocker->destockage_id);
                $depot_id = $destockage->depot_id;

                //Ajustement des stocks dans depot-article
                $depot = DepotArticle::where([['depot_id', $depot_id], ['article_id', $articleDestocker->article_id]])->first();
                $depot->quantite_disponible = $depot->quantite_disponible + $articleDestocker->quantite_destocker;
                $depot->save();
                $mouvementStock = MouvementStock::where([['depot_id', $depot_id], ['article_id', $articleDestocker->article_id]])->whereDate('date_mouvement', $destockage->date_destockage)->first();
                $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker - $articleDestocker->quantite_destocker;
                $mouvementStock->save();

                $articleDestocker->update(['deleted_by' => Auth::user()->id]);
                $articleDestocker->delete();
                $jsonData["data"] = json_decode($articleDestocker);
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
