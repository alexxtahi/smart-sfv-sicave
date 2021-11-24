<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleRetourne;
use App\Models\Boutique\ArticleVente;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\MouvementStock;
use App\Models\Boutique\RetourArticle;
use App\Models\Boutique\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use ZipStream\Exception;

class ArticleRetourneController extends Controller
{
    public function listeArticleRetourne($retourArticle)
    {
        $details = ArticleRetourne::with('article', 'unite')
            ->select('article_retournes.*')
            ->Where('article_retournes.retour_article_id', $retourArticle)
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
                $retourArticle = RetourArticle::find($data['retour_article_id']);
                $vente = Vente::find($data["vente_id"]);

                if (!isset($data["vente_id"]) or $retourArticle->vente_id != $data['vente_id']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé le numero de facture ou de ticket, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }

                $ArticleRetourne = ArticleRetourne::where([['retour_article_id', $data['retour_article_id']], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->first();
                if ($ArticleRetourne != null) {
                    $ArticleRetourne->quantite = $ArticleRetourne->quantite + $data['quantite'];
                    $ArticleRetourne->prix_unitaire = $data['prix'];
                    $ArticleRetourne->save();
                    $articleRetournes = $ArticleRetourne;
                } else {
                    $articleRetourne = new ArticleRetourne;
                    $articleRetourne->article_id = $data["article_id"];
                    $articleRetourne->unite_id = $data["unite_id"];
                    $articleRetourne->quantite = $data['quantite'];
                    $articleRetourne->quantite_vendue = $data['quantite_vendue'];
                    $articleRetourne->prix_unitaire = $data['prix'];
                    $articleRetourne->retour_article_id = $retourArticle->id;
                    $articleRetourne->created_by = Auth::user()->id;
                    $articleRetourne->save();
                    $articleRetournes = $articleRetourne;
                }
                //Traitement sur le stock dans depot-article 
                if ($articleRetournes != null) {


                    $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();
                    $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                    $ArticleVente = ArticleVente::where([['vente_id', $vente->id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();

                    $Article = Article::find($data["article_id"]);
                    if ($Article != null && $Article->stockable == 1) {
                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = $retourArticle->date_retour;
                            $mouvementStock->depot_id = $vente->depot_id;
                            $mouvementStock->article_id = $data["article_id"];
                            $mouvementStock->unite_id = $data["unite_id"];
                            $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $data['quantite'];
                        $DepotArticle->save();
                        $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee + $data['quantite'];
                        $mouvementStock->save();
                    }

                    if ($ArticleVente->quantite == $data['quantite']) {
                        $ArticleVente->retourne = TRUE;
                    }
                    $ArticleVente->quantite = $ArticleVente->quantite - $data['quantite'];
                    $ArticleVente->save();
                }
                $jsonData["data"] = json_decode($articleRetournes);
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
     * @param  \App\ArticleRetourne  $articleRetourne
     * @return Response
     */
    public function update(Request $request, ArticleRetourne $articleRetourne)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];

        if ($articleRetourne) {
            $data = $request->all();
            try {

                //Récuperation des dépôts
                $retourArticle = RetourArticle::find($articleRetourne->retour_article_id);
                $vente = Vente::find($retourArticle->vente_id);

                if (!isset($data["vente_id"]) or $retourArticle->vente_id != $data['vente_id']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé le numero de facture ou de ticket, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }
                $ArticleVente = ArticleVente::where([['vente_id', $vente->id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                $Article = Article::find($articleRetourne->article_id);
                if ($Article != null && $Article->stockable == 1) {
                    //Ajustement des stocks dans depot-article
                    $depot = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                    $depot->quantite_disponible = $depot->quantite_disponible - $articleRetourne->quantite;
                    $depot->save();
                    $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                    $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee - $articleRetourne->quantite;
                    $mouvementStock->save();
                }
                $ArticleVente->quantite = $ArticleVente->quantite + $articleRetourne->quantite;
                $ArticleVente->retourne = FALSE;
                $ArticleVente->save();

                $articleRetourne->article_id = $data["article_id"];
                $articleRetourne->unite_id = $data["unite_id"];
                $articleRetourne->quantite = $data['quantite'];
                $articleRetourne->quantite_vendue = $data['quantite_vendue'];
                $articleRetourne->prix_unitaire = $data['prix'];
                $articleRetourne->updated_by = Auth::user()->id;
                $articleRetourne->save();

                //Traitement sur le stock dans depot-article 
                if ($articleRetourne != null) {
                    $ArticleVente = ArticleVente::where([['vente_id', $vente->id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();
                    $Article = Article::find($data["article_id"]);
                    if ($Article != null && $Article->stockable == 1) {
                        $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                        $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();
                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = $retourArticle->date_retour;
                            $mouvementStock->depot_id = $vente->depot_id;
                            $mouvementStock->article_id = $data["article_id"];
                            $mouvementStock->unite_id = $data["unite_id"];
                            $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $data['quantite'];
                        $DepotArticle->save();
                        $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee + $data['quantite'];
                        $mouvementStock->save();
                    }

                    if ($ArticleVente->quantite == $data['quantite']) {
                        $ArticleVente->retourne = TRUE;
                    }
                    $ArticleVente->quantite = $ArticleVente->quantite - $data['quantite'];
                    $ArticleVente->save();
                }

                $jsonData["data"] = json_decode($articleRetourne);
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
     * @param  \App\ArticleRetourne  $articleRetourne
     * @return Response
     */
    public function destroy(ArticleRetourne $articleRetourne)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($articleRetourne) {
            try {
                //Récuperation des dépôts
                $retourArticle = RetourArticle::find($articleRetourne->retour_article_id);
                $vente = Vente::find($retourArticle->vente_id);

                //Ajustement des stocks dans depot-article
                $Article = Article::find($articleRetourne->article_id);
                $ArticleVente = ArticleVente::where([['vente_id', $vente->id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                if ($Article != null && $Article->stockable == 1) {
                    $depot = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                    $depot->quantite_disponible = $depot->quantite_disponible - $articleRetourne->quantite;
                    $depot->save();

                    $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                    $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee - $articleRetourne->quantite;
                    $mouvementStock->save();
                }
                $ArticleVente->quantite = $ArticleVente->quantite + $articleRetourne->quantite;
                $ArticleVente->retourne = FALSE;
                $ArticleVente->save();

                $articleRetourne->update(['deleted_by' => Auth::user()->id]);
                $articleRetourne->delete();
                $jsonData["data"] = json_decode($articleRetourne);
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
