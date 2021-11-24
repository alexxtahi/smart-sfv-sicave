<?php

namespace App\Http\Controllers\Vente;

use Exception;
use App\Models\Vente\Devis;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Vente\ArticleDevis;
use App\Http\Controllers\Controller;

class ArticleDevisController extends Controller
{
    public function listeArticlesDevis($devis)
    {
        $montantTTC = 0;
        $articlesDevis =  ArticleDevis::with('article')
                                        ->join('articles', 'articles.id', '=', 'article_devis.article_id')
                                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                        ->select('article_devis.*','param_tvas.tva')
                                        ->Where('article_devis.devis_id', $devis)
                                        ->get();

        foreach ($articlesDevis as $article){
            $montantTTC = $montantTTC + $article->prix*$article->quantite;
        }

        $jsonData["rows"] = $articlesDevis->toArray();
        $jsonData["total"] = $articlesDevis->count();
        $jsonData["montantTTC"] = $montantTTC;
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

                //Récuperation du dépôt
                $devis = Devis::find($data['vente_id']);

                $articleDevis = ArticleDevis::where([['devis_id', $data['vente_id']], ['article_id', $data['article_id']],['choix_prix', $data['choix_prix']]])->first();
                if ($articleDevis) {
                    $articleDevis->quantite = $articleDevis->quantite + $data['quantite'];
                    $articleDevis->save();
                } else {
                    $articleDevis = new ArticleDevis;
                    $articleDevis->article_id = $data['article_id'];
                    $articleDevis->devis_id = $data['vente_id'];
                    $articleDevis->quantite = $data['quantite'];
                    $articleDevis->choix_prix = $data['choix_prix'];
                    $articleDevis->depot_id = $devis->depot_id;
                    $articleDevis->prix = $data['prix'];
                    $articleDevis->save();
                }
                $jsonData["data"] = json_decode($articleDevis);
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
     * @param  \App\ArticleVente  $articleVente
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        $articleDevis = ArticleDevis::find($id);
        if ($articleDevis) {
            $data = $request->all();
            try {

                $articleDevis->article_id = $data['article_id'];
                $articleDevis->quantite = $data['quantite'];
                $articleDevis->prix = $data['prix'];
                $articleDevis->choix_prix = $data['choix_prix'];
                $articleDevis->save();

                $jsonData["data"] = json_decode($articleDevis);
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
     * @param  \App\ArticleVente  $articleVente
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $articleDevis = ArticleDevis::find($id);
        if ($articleDevis) {
            try {

                $articleDevis->delete();

                $jsonData["data"] = json_decode($articleDevis);
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
