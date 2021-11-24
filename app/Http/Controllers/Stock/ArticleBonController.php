<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\ArticleBon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleBonController extends Controller
{
    public function listeArticleBon($bon_commande)
    {
        $articlesBon = ArticleBon::with('article')
            ->join('articles', 'articles.id', '=', 'article_bons.article_id')
            ->select('article_bons.*', 'articles.id as id_article', 'articles.libelle_article', 'articles.prix_achat_ttc')
            ->Where([['article_bons.deleted_at', null], ['article_bons.bon_commande_id', $bon_commande]])
            ->get();
        $jsonData["rows"] = $articlesBon->toArray();
        $jsonData["total"] = $articlesBon->count();
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
                $articleBon = ArticleBon::where([['article_id', $data['article_id']], ['bon_commande_id', $data['bon_commande_id']]])->first();
                if ($articleBon != null) {
                    $articleBon->quantite_demande = $articleBon->quantite_demande + $data['quantite'];
                    $articleBon->save();
                } else {
                    $articleBon = new ArticleBon;
                    $articleBon->article_id = $data['article_id'];
                    $articleBon->bon_commande_id = $data['bon_commande_id'];
                    $articleBon->quantite_demande = $data['quantite'];
                    $articleBon->prix_article = $data['prix_article'];
                    $articleBon->created_by = Auth::user()->id;
                    $articleBon->save();
                }
                $jsonData["data"] = json_decode($articleBon);
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
     * @param  \App\ArticleBon  $articleBon
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        $articleBon = ArticleBon::find($id);
        if ($articleBon) {

            $data = $request->all();
            try {

                $articleBon->article_id = $data['article_id'];
                $articleBon->bon_commande_id = $data['bon_commande_id'];
                $articleBon->quantite_demande = $data['quantite'];
                $articleBon->prix_article = $data['prix_article'];
                $articleBon->updated_by = Auth::user()->id;
                $articleBon->save();
                $jsonData["data"] = json_decode($articleBon);
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
     * @param  \App\ArticleBon  $articleBon
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $articleBon = ArticleBon::find($id);
        if ($articleBon) {

            try {
                $articleBon->update(['deleted_by' => Auth::user()->id]);
                $articleBon->delete();
                $jsonData["data"] = json_decode($articleBon);
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
