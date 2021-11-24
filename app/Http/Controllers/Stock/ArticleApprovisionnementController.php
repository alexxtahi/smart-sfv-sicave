<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\Approvisionnement;
use App\Models\Stock\ArticleApprovisionnement;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\MouvementStock;
use App\Models\Stock\Article;
use App\Models\Stock\Depot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ArticleApprovisionnementController extends Controller
{

    public function listeArticleApprovisionne($approvisionnement)
    {
        $articlesApprovisionnes = ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->select('article_approvisionnements.*', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
            ->Where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.approvisionnement_id', $approvisionnement]])
            ->get();
        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }

    public function listeArticlesRecusByQuantite()
    {
        $date_jour = date("Y-m-d");
        $articlesApprovisionnes = ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->whereDate('approvisionnements.date_approvisionnement', $date_jour)
            ->Where('article_approvisionnements.deleted_at', null)
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();
        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantitePeriode($debut, $fin)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();
        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantiteArtice($article)
    {
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->Where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.article_id', $article]])
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();
        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantiteArticlePeriode($debut, $fin, $article)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->Where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.article_id', $article]])
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();

        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantiteDepot($depot)
    {
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->Where([['approvisionnements.depot_id', $depot], ['article_approvisionnements.deleted_at', null]])
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();
        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantiteDepotArticle($depot, $article)
    {
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->Where([['approvisionnements.depot_id', $depot], ['article_approvisionnements.deleted_at', null], ['article_approvisionnements.article_id', $article]])
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();
        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantiteDepotPeriode($debut, $fin, $depot)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->Where([['article_approvisionnements.deleted_at', null], ['approvisionnements.depot_id', $depot]])
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();

        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesRecusByQuantitePeriodeDepotArticle($debut, $fin, $depot, $article)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $articlesApprovisionnes =  ArticleApprovisionnement::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('approvisionnements', 'approvisionnements.id', '=', 'article_approvisionnements.approvisionnement_id')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->Where([['article_approvisionnements.deleted_at', null], ['approvisionnements.depot_id', $depot], ['article_approvisionnements.article_id', $article]])
            ->select('article_approvisionnements.*', 'depots.libelle_depot', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->get();

        $jsonData["rows"] = $articlesApprovisionnes->toArray();
        $jsonData["total"] = $articlesApprovisionnes->count();
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
                $approvisionnement = Approvisionnement::find($data['approvisionnement_id']);
                if (!isset($data['depot']) or $approvisionnement->depot_id != $data['depot']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }

                $articleApprovisionnement = new ArticleApprovisionnement;
                $articleApprovisionnement->article_id = $data['article_id'];
                $articleApprovisionnement->approvisionnement_id = $data['approvisionnement_id'];
                $articleApprovisionnement->quantite = $data['quantite'];
                $articleApprovisionnement->date_peremption = isset($data['date_peremption']) && !empty($data['date_peremption']) ? Carbon::createFromFormat('d-m-Y', $data['date_peremption']) : null;
                $articleApprovisionnement->created_by = Auth::user()->id;
                $articleApprovisionnement->save();

                if ($articleApprovisionnement) {
                    $depotArticle = DepotArticle::where([['depot_id', $data['depot']], ['article_id', $data['article_id']]])->first();
                    $mouvementStock = MouvementStock::where([['depot_id', $data['depot']], ['article_id', $data['article_id']]])->whereDate('date_mouvement', $approvisionnement->date_approvisionnement)->first();

                    if (!$mouvementStock) {
                        $mouvementStock = new MouvementStock;
                        $mouvementStock->date_mouvement = $approvisionnement->date_approvisionnement;
                        $mouvementStock->depot_id = $data['depot'];
                        $mouvementStock->article_id = $data['article_id'];
                        $mouvementStock->quantite_initiale = $depotArticle != null ? $depotArticle->quantite_disponible : 0;
                        $mouvementStock->date_peremption = isset($data['date_peremption']) && !empty($data['date_peremption']) ? Carbon::createFromFormat('d-m-Y', $data['date_peremption']) : null;
                        $mouvementStock->created_by = Auth::user()->id;
                    }
                    if (!$depotArticle) {
                        $depotArticle = new DepotArticle;
                        $depotArticle->article_id = $data['article_id'];
                        $depotArticle->depot_id = $data['depot'];
                        $depotArticle->date_peremption = isset($data['date_peremption']) && !empty($data['date_peremption']) ? Carbon::createFromFormat('d-m-Y', $data['date_peremption']) : null;
                        $depotArticle->created_by = Auth::user()->id;
                    }
                    $depotArticle->quantite_disponible = $depotArticle->quantite_disponible + $data['quantite'];
                    $depotArticle->save();
                    $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee + $data['quantite'];
                    $mouvementStock->save();
                }
                $jsonData["data"] = json_decode($articleApprovisionnement);
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
     * @param  \App\ArticleApprovisionnement  $articleApprovisionnement
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        $articleApprovisionnement = ArticleApprovisionnement::find($id);
        if ($articleApprovisionnement) {
            $data = $request->all();
            try {

                //Récuperation de l'ancien dépôt
                $approvisionnement = Approvisionnement::find($articleApprovisionnement->approvisionnement_id);
                $depot = $approvisionnement->depot_id;
                if (!isset($data['depot']) or $depot != $data['depot']) {
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => null]);
                }
                //Ajustement stock dans depot-article
                $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $articleApprovisionnement->article_id]])->first();
                $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $articleApprovisionnement->quantite;
                $DepotArticle->save();

                $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $articleApprovisionnement->article_id]])->whereDate('date_mouvement', $approvisionnement->date_approvisionnement)->whereDate('date_peremption', $articleApprovisionnement->date_peremption)->first();
                $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee - $articleApprovisionnement->quantite;
                $mouvementStock->save();

                $articleApprovisionnement->article_id = $data['article_id'];
                $articleApprovisionnement->quantite = $data['quantite'];
                $articleApprovisionnement->date_peremption = isset($data['date_peremption']) && !empty($data['date_peremption']) ? Carbon::createFromFormat('d-m-Y', $data['date_peremption']) : null;
                $articleApprovisionnement->updated_by = Auth::user()->id;
                $articleApprovisionnement->save();

                if ($articleApprovisionnement) {
                    $depotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $data['article_id']]])->whereDate('date_peremption', Carbon::createFromFormat('d-m-Y', $data['date_peremption']))->first();
                    $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $data['article_id']]])->whereDate('date_mouvement', $approvisionnement->date_approvisionnement)->whereDate('date_peremption', Carbon::createFromFormat('d-m-Y', $data['date_peremption']))->first();

                    if (!$mouvementStock) {
                        $mouvementStock = new MouvementStock;
                        $mouvementStock->date_mouvement = $approvisionnement->date_approvisionnement;
                        $mouvementStock->depot_id = $depot;
                        $mouvementStock->article_id = $data['article_id'];
                        $mouvementStock->quantite_initiale = $depotArticle != null ? $depotArticle->quantite_disponible : 0;
                        $mouvementStock->date_peremption = isset($data['date_peremption']) && !empty($data['date_peremption']) ? Carbon::createFromFormat('d-m-Y', $data['date_peremption']) : null;
                        $mouvementStock->created_by = Auth::user()->id;
                    }
                    if (!$depotArticle) {
                        $depotArticle = new DepotArticle;
                        $depotArticle->article_id = $data['article_id'];
                        $depotArticle->depot_id = $depot;
                        $depotArticle->date_peremption = isset($data['date_peremption']) && !empty($data['date_peremption']) ? Carbon::createFromFormat('d-m-Y', $data['date_peremption']) : null;
                        $depotArticle->created_by = Auth::user()->id;
                    }
                    $depotArticle->quantite_disponible = $depotArticle->quantite_disponible + $data['quantite'];
                    $depotArticle->save();
                    $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee + $data['quantite'];
                    $mouvementStock->save();
                }

                $jsonData["data"] = json_decode($articleApprovisionnement);
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
     * @param  ArticleApprovisionnement  $articleApprovisionnement
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $articleApprovisionnement = ArticleApprovisionnement::find($id);
        if ($articleApprovisionnement) {
            try {

                //Récuperation du dépôt
                $approvisionnement = Approvisionnement::find($articleApprovisionnement->approvisionnement_id);
                $depot = $approvisionnement->depot_id;

                //Ajustement stock dans depot-article
                $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $articleApprovisionnement->article_id]])->first();
                $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $articleApprovisionnement->quantite;
                $DepotArticle->save();

                $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $articleApprovisionnement->article_id]])->whereDate('date_mouvement', $approvisionnement->date_approvisionnement)->whereDate('date_peremption', $articleApprovisionnement->date_peremption)->first();
                $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee - $articleApprovisionnement->quantite;
                $mouvementStock->save();

                $articleApprovisionnement->update(['deleted_by' => Auth::user()->id]);
                $articleApprovisionnement->delete();
                $jsonData["data"] = json_decode($articleApprovisionnement);
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

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    //Etat
    //Mouvelent article reçu
    public function articlesRecusByQuantitePdf()
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantite());
        return $pdf->stream('liste_articles_vendus.pdf');
    }
    public function articlesRecusByQuantite()
    {
        $date_jour = date("Y-m-d");
        $datas =  Approvisionnement::with('fournisseur')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->Where('approvisionnements.deleted_at', null)
            ->whereDate('approvisionnements.date_approvisionnement', $date_jour)
            ->select('approvisionnements.*', 'depots.libelle_depot', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant du jour</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="center">&nbsp; Dépôt : <b>' . $data->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles = ArticleApprovisionnement::where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.approvisionnement_id', $data->id]])
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="3" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="3" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement sur une période
    public function articlesRecusByQuantitePeriodePdf($debut, $fin)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantitePeriode($debut, $fin));
        return $pdf->stream('liste_articles_vendus_du_' . $debut . '_au_' . $fin . '_.pdf');
    }
    public function articlesRecusByQuantitePeriode($debut, $fin)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant du ' . $debut . ' au ' . $fin . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="center">&nbsp; Dépôt : <b>' . $data->depot->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles = ArticleApprovisionnement::where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.approvisionnement_id', $data->id]])
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'unites.libelle_unite', 'articles.code_barre', 'articles.description_article', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="3" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="3" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement d'un article
    public function articlesRecusByQuantiteArticlePdf($article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantiteArticle($article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_articles_' . $info_article->description_article . '_vendus.pdf');
    }
    public function articlesRecusByQuantiteArticle($article_id)
    {
        $datas = Approvisionnement::with('fournisseur', 'depot')
            ->join('article_approvisionnements', 'article_approvisionnements.approvisionnement_id', '=', 'approvisionnements.id')
            ->Where('article_approvisionnements.article_id', $article_id)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->groupBy('approvisionnements.id')
            ->get();

        $info_article = Article::find($article_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant concernant ' . $info_article->description_article . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Dépôt : <b>' . $data->depot->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles = ArticleApprovisionnement::where('article_approvisionnements.approvisionnement_id', $data->id)
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')->where('articles.id', $article_id)
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement d'un article sur une période
    public function articlesRecusByQuantitePeriodeArticlePdf($debut, $fin, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantitePeriodeArticle($debut, $fin, $article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_article_' . $info_article->description_article . '_vendus_du_' . $debut . '_au_' . $fin . '_.pdf');
    }
    public function articlesRecusByQuantitePeriodeArticle($debut, $fin, $article_id)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_article = Article::find($article_id);
        $datas = Approvisionnement::with('fournisseur', 'depot')
            ->join('article_approvisionnements', 'article_approvisionnements.approvisionnement_id', '=', 'approvisionnements.id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->Where('article_approvisionnements.article_id', $article_id)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->groupBy('approvisionnements.id')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant concernant ' . $info_article->description_article . ' du ' . $debut . ' au ' . $fin . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->depot->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles =  ArticleApprovisionnement::where('article_approvisionnements.approvisionnement_id', $data->id)
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')->where('articles.id', $article_id)
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesRecusByQuantiteDepotPdf($depot_id)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantiteDepot($depot_id));
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_recu_dans_le_depot_' . $info_depot->libelle_depot . '.pdf');
    }
    public function articlesRecusByQuantiteDepot($depot_id)
    {
        $info_depot = Depot::find($depot_id);
        $datas =  Approvisionnement::with('fournisseur')
            ->join('depots', 'depots.id', '=', 'approvisionnements.depot_id')
            ->Where([['approvisionnements.deleted_at', null], ['approvisionnements.depot_id', $depot_id]])
            ->select('approvisionnements.*', 'depots.libelle_depot', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant dans le dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="center">&nbsp; Dépôt : <b>' . $data->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles = ArticleApprovisionnement::where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.approvisionnement_id', $data->id]])
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="3" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="3" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesRecusByQuantiteDepotArticlePdf($depot, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantiteDepotArticle($depot, $article));
        $info_article = Article::find($article);
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_' . $info_article->description_article . '_recus_dans_le_depot_' . $info_depot->libelle_depot . '.pdf');
    }
    public function articlesRecusByQuantiteDepotArticle($depot, $article_id)
    {
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article_id);
        $datas = Approvisionnement::with('fournisseur', 'depot')
            ->join('article_approvisionnements', 'article_approvisionnements.approvisionnement_id', '=', 'approvisionnements.id')
            ->Where([['article_approvisionnements.article_id', $article_id], ['approvisionnements.depot_id', $depot]])
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->groupBy('approvisionnements.id')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant concernant ' . $info_article->description_article . ' dans le dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Dépôt : <b>' . $data->depot->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles = ArticleApprovisionnement::where('article_approvisionnements.approvisionnement_id', $data->id)
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')->where('articles.id', $article_id)
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesRecusByQuantiteDepotPeriodePdf($depot, $debut, $fin)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantiteDepotPeriode($depot, $debut, $fin));
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_articles_recus_dans_le_depot_' . $info_depot->libelle_depot . '_du_' . $debut . '_au_' . $fin . '.pdf');
    }
    public function articlesRecusByQuantiteDepotPeriode($depot, $debut, $fin)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot);
        $datas = Approvisionnement::with('fournisseur', 'depot')
            ->join('article_approvisionnements', 'article_approvisionnements.approvisionnement_id', '=', 'approvisionnements.id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->Where('approvisionnements.depot_id', $depot)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->groupBy('approvisionnements.id')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant dans le dépôt ' . $info_depot->libelle_depot . ' du ' . $debut . ' au ' . $fin . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->depot->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles =  ArticleApprovisionnement::where('article_approvisionnements.approvisionnement_id', $data->id)
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesRecusByQuantitePeriodeDepotArticlePdf($debut, $fin, $depot, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesRecusByQuantitePeriodeDepotArticle($debut, $fin, $depot, $article));
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article);
        return $pdf->stream('liste_' . $info_article->description_article . '_recus_dans_le_depot_' . $info_depot->libelle_depot . '_du_' . $debut . '_au_' . $fin . '.pdf');
    }
    public function articlesRecusByQuantitePeriodeDepotArticle($debut, $fin, $depot, $article_id)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article_id);
        $datas = Approvisionnement::with('fournisseur', 'depot')
            ->join('article_approvisionnements', 'article_approvisionnements.approvisionnement_id', '=', 'approvisionnements.id')
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $dateDebut)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $dateFin)
            ->Where([['article_approvisionnements.article_id', $article_id], ['approvisionnements.depot_id', $depot]])
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->groupBy('approvisionnements.id')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock entrant concernant ' . $info_article->description_article . ' dans le dépôt ' . $info_depot->libelle_depot . ' du ' . $debut . ' au ' . $fin . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotalAchat = 0;
        $grandTotalVente = 0;
        $grandTotalQteArticle = 0;
        foreach ($datas as $data) {
            $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->date_approvisionnements . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="center">&nbsp; Approvisionnement du : <b>' . $data->depot->libelle_depot . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant achat TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix vente TTC</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant vente TTC</th>
                        </tr>';
            $articles =  ArticleApprovisionnement::where('article_approvisionnements.approvisionnement_id', $data->id)
                ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')->where('articles.id', $article_id)
                ->join('unites', 'unites.id', '=', 'article_approvisionnements.unite_id')
                ->select('article_approvisionnements.*', 'unites.libelle_unite', 'articles.prix_achat_ttc', 'articles.prix_vente_ttc_base', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
            $totalAchat = 0;
            $totalVente = 0;
            $totalQteArticle = 0;
            $total = 0;
            foreach ($articles as $article) {
                $totalAchat = $totalAchat + $article->quantite * $article->prix_achat_ttc;
                $totalVente = $totalVente + $article->quantite * $article->prix_vente_ttc_base;
                $totalQteArticle = $totalQteArticle + $article->quantite;
                $total = $total + 1;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_achat_ttc * $article->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix_vente_ttc_base, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $totalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($totalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
            $grandTotalAchat = $grandTotalAchat + $totalAchat;
            $grandTotalVente = $grandTotalVente + $totalVente;
            $grandTotalQteArticle = $grandTotalQteArticle + $totalQteArticle;
        }
        $outPut .= '<tr>
                       <td colspan="1" cellspacing="0" border="2" align="left">&nbsp;<b>Total Général</b></td>
                       <td colspan="1" cellspacing="0" border="2" align="center">' . $grandTotalQteArticle . '</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalAchat, 0, ',', ' ') . '</b>&nbsp;</td>
                       <td colspan="2" cellspacing="0" border="2" align="right"><b>' . number_format($grandTotalVente, 0, ',', ' ') . '</b>&nbsp;</td>
                     </tr>';
        $outPut .= '</table></div>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Header and footer des pdf
    public function header()
    {
        $header = '<html>
                    <head>
                        <style>
                            @page{
                                margin: 100px 25px;
                                }
                            header{
                                    position: absolute;
                                    top: -60px;
                                    left: 0px;
                                    right: 0px;
                                    height:20px;
                                }
                            .container-table{
                                            margin:80px 0;
                                            width: 100%;
                                        }
                            .fixed-footer{.
                                width : 100%;
                                position: fixed;
                                bottom: -28;
                                left: 0px;
                                right: 0px;
                                height: 50px;
                                text-align:center;
                            }
                            .fixed-footer-right{
                                position: absolute;
                                bottom: -150;
                                height: 0;
                                font-size:13px;
                                float : right;
                            }
                            .page-number:before {

                            }
                        </style>
                    </head>
    /
    <script type="text/php">
        if (isset($pdf)){
            $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
        <body>
        <header>
        <p style="margin:0; position:left;">
            <img src=' . $this->infosConfig()->logo . ' width="200" height="160"/>
        </p>
        </header>';
        return $header;
    }
    public function footer()
    {
        $footer = "<div class='fixed-footer'>
                        <div class='page-number'></div>
                    </div>
                    <div class='fixed-footer-right'>
                     <i> Editer le " . date('d-m-Y') . "</i>
                    </div>
            </body>
        </html>";
        return $footer;
    }
}
