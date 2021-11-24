<?php

namespace App\Http\Controllers\Vente;

use Exception;
use function response;
use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use App\Models\Stock\Article;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\Stock\DepotArticle;
use App\Models\Vente\ArticleVente;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Stock\MouvementStock;
use Illuminate\Support\Facades\Auth;
use App\Models\Comptabilite\CaisseOuverte;

class ArticleVenteController extends Controller
{
    public function listeArticlesVente($vente)
    {
        $montantTTC = 0;
        $articlesVentes =  ArticleVente::with('article')
                                    ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                    ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                    ->select('article_ventes.*','param_tvas.tva')
                                    ->Where([['article_ventes.deleted_at', null], ['article_ventes.retourne', 0], ['article_ventes.vente_id', $vente]])
                                    ->get();

        foreach ($articlesVentes as $article){
            $montantTTC = $montantTTC + $article->prix*$article->quantite;
        }

        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
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
                $vente = Vente::find($data['vente_id']);

                $article = Article::find($data['article_id']);
                if ($article && $article->non_stockable == 0) {
                    //Vérifions la quantité en stock
                    $depotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $data['article_id']]])->first();
                    if ($depotArticle->quantite_disponible < $data['quantite']) {
                        return response()->json(["code" => 0, "msg" => "La quantité demandée est supérieure à la quantité dans ce dépôt qui est " . $depotArticle->quantite_disponible, "data" => null]);
                    }
                }

                if(!$vente->client_id == null) {
                    if ($vente->caisse_ouverte_id) {
                        $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                        if (!$caisse_ouverte or $caisse_ouverte->date_fermeture != null) {
                            return response()->json(["code" => 0, "msg" => "Ajout impossible car la caisse est fermée", "data" => null]);
                        }
                    }
                }

                $articleVente = ArticleVente::where([['vente_id', $data['vente_id']], ['article_id', $data['article_id']],['choix_prix', $data['choix_prix']]])->first();
                if ($articleVente) {
                    $articleVente->quantite = $articleVente->quantite + $data['quantite'];
                    $articleVente->save();
                } else {
                    $articleVente = new ArticleVente;
                    $articleVente->article_id = $data['article_id'];
                    $articleVente->vente_id = $data['vente_id'];
                    $articleVente->quantite = $data['quantite'];
                    $articleVente->choix_prix = isset($data['choix_prix']) ? $data['choix_prix'] : null;
                    $articleVente->depot_id = $vente->depot_id;
                    $articleVente->prix = $data['prix'];
                    $articleVente->created_by = Auth::user()->id;
                    $articleVente->save();
                }

                if ($article && $article->non_stockable == 0 && $vente->proformat == 0) {
                    //Dimunition stock depot-article
                    $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $data['article_id']]])->whereDate('date_mouvement', date_format($vente->date_vente, "Y-m-d"))->first();
                    if (!$mouvementStock) {
                        $mouvementStock = new MouvementStock;
                        $mouvementStock->date_mouvement = date_format($vente->date_vente, "Y-m-d");
                        $mouvementStock->depot_id = $vente->depot_id;
                        $mouvementStock->article_id = $data['article_id'];
                        $mouvementStock->quantite_initiale = $depotArticle != null ? $depotArticle->quantite_disponible : 0;
                        $mouvementStock->created_by = Auth::user()->id;
                    }
                    $depotArticle->quantite_disponible = $depotArticle->quantite_disponible - $data['quantite'];
                    $depotArticle->save();
                    $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data['quantite'];
                    $mouvementStock->save();
                }

                $jsonData["data"] = json_decode($articleVente);
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
        $articleVente = ArticleVente::find($id);
        if ($articleVente) {
            $data = $request->all();
            try {


                $vente = Vente::find($articleVente->vente_id);

                $article = Article::find($articleVente->article_id);

                if ($article && $article->non_stockable == 0) {
                    $oldQuantite = $articleVente->quantite;
                    //Ajustement stock dans depot-article
                    $oldDepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->first();
                    $oldDepotArticle->quantite_disponible = $oldDepotArticle->quantite_disponible + $articleVente->quantite;
                    $oldDepotArticle->save();

                    $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->whereDate('date_mouvement', date_format($vente->date_vente, "Y-m-d"))->first();
                    $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue - $articleVente->quantite;
                    $mouvementStock->save();

                    //Vérification du stock disponible
                    $depot_article = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $data['article_id']]])->first();
                    if ($depot_article->quantite_disponible < $data['quantite']) {
                        //Ajustement stock dans depot-article
                        $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->first();
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $oldQuantite;
                        $DepotArticle->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->whereDate('date_mouvement', date_format($vente->date_vente, "Y-m-d"))->first();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $articleVente->quantite;
                        $mouvementStock->save();
                        return response()->json(["code" => 0, "msg" => "La quantité demandée est supérieure à la quantité dans ce dépôt qui est " . $depot_article->quantite_disponible, "data" => null]);
                    }
                }
                if ($vente->client_id == null) {
                    if ($vente->caisse_ouverte_id != null) {
                        $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                        if (!$caisse_ouverte or $caisse_ouverte->date_fermeture != null) {
                            return response()->json(["code" => 0, "msg" => "Modification impossible car la caisse est fermée", "data" => null]);
                        }
                    }
                }

                $articleVente->article_id = $data['article_id'];
                $articleVente->vente_id = $data['vente_id'];
                $articleVente->quantite = $data['quantite'];
                $articleVente->prix = $data['prix'];
                $articleVente->choix_prix = isset($data['choix_prix']) ? $data['choix_prix'] : null;
                $articleVente->updated_by = Auth::user()->id;
                $articleVente->save();

                //Augmentation stock dans depot-article
                $Articles = Article::find($data['article_id']);
                if ($Articles && $Articles->non_stockable == 0 && $vente->proformat == 0) {
                        $NewDepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $data['article_id']]])->first();
                        $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $data['article_id']]])->whereDate('date_mouvement', date_format($vente->date_vente, "Y-m-d"))->first();
                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = date_format($vente->date_vente, "Y-m-d");
                            $mouvementStock->depot_id = $vente->depot_id;
                            $mouvementStock->article_id = $data['article_id'];
                            $mouvementStock->quantite_initiale = $NewDepotArticle != null ? $NewDepotArticle->quantite_disponible : 0;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        $NewDepotArticle->quantite_disponible = $NewDepotArticle->quantite_disponible - $data['quantite'];
                        $NewDepotArticle->save();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data['quantite'];
                        $mouvementStock->save();
                }

                $jsonData["data"] = json_decode($articleVente);
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
        $articleVente = ArticleVente::find($id);
        if ($articleVente) {
            try {
                //Récuperation de l'ancien dépôt
                $vente = Vente::find($articleVente->vente_id);
                if ($vente->caisse_ouverte_id != null) {
                    $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                    if (!$caisse_ouverte or $caisse_ouverte->date_fermeture != null) {
                        return response()->json(["code" => 0, "msg" => "Supression impossible car la caisse est fermée", "data" => null]);
                    }
                }
                //Ajustement stock dans depot-article
                $Article = Article::find($articleVente->article_id);
                if ($Article && $Article->non_stockable == 0 && $vente->proformat == 0 && $vente->attente == 0) {
                        $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->first();
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $articleVente->quantite;
                        $DepotArticle->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->whereDate('date_mouvement', date_format($vente->date_vente, "Y-m-d"))->first();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue - $articleVente->quantite;
                        $mouvementStock->save();
                }

                $articleVente->update(['deleted_by' => Auth::user()->id]);
                $articleVente->delete();

                $jsonData["data"] = json_decode($articleVente);
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
    public function articlesVendusByQuantitePdf()
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantite());
        return $pdf->stream('liste_articles_vendus.pdf');
    }
    public function articlesVendusByQuantite()
    {
        $date_jour = date("Y-m-d");

        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->whereDate('caisse_ouvertes.date_ouverture', $date_jour)
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du jour</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {

            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['ventes.client_id', null]])
                ->join('articles', 'articles.id', 'article_ventes.article_id')
                ->join('unites', 'unites.id', 'article_ventes.unite_id')
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;
            if (count($articles) > 0) {

                $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="right">' . $article->prix . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }

                $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
                $grandTotal = $grandTotal + $totalCiasse;
            }
        }

        $articlesHorsC =  ArticleVente::where('ventes.client_id', '!=', null)
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('unites', 'unites.id', 'article_ventes.unite_id')
            ->join('articles', 'articles.id', 'article_ventes.article_id')
            ->whereDate('ventes.date_vente', $date_jour)
            ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;
        if (count($articles) > 0) {

            $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="right">' . $article->prix . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }

        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement d'un article sur une période
    public function articlesVendusByQuantitePeriodeArticlePdf($debut, $fin, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantitePeriodeArticle($debut, $fin, $article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_article_' . $info_article->description_article . '_vendus_du_' . $debut . '_au_' . $fin . '_.pdf');
    }
    public function articlesVendusByQuantitePeriodeArticle($debut, $fin, $article_id)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_article = Article::find($article_id);
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock concernant ' . $info_article->description_article . ' du ' . $debut . ' au ' . $fin . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {

            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['article_ventes.article_id', $article_id]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('unites', 'unites.id', 'article_ventes.unite_id')
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->select('article_ventes.*', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;
            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="right">' . $article->prix . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
                $grandTotal = $grandTotal + $totalCiasse;
            }
        }
        $totalHorsC = 0;
        if (count($articles) > 0) {
            $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
            $articlesHorsC =  ArticleVente::where([['ventes.client_id', '!=', null], ['article_ventes.article_id', $article_id]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('unites', 'unites.id', 'article_ventes.unite_id')
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->select('article_ventes.*', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();

            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }

        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement sur une période
    public function articlesVendusByQuantitePeriodePdf($debut, $fin)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantitePeriode($debut, $fin));
        return $pdf->stream('liste_articles_vendus_du_' . $debut . '_au_' . $fin . '_.pdf');
    }
    public function articlesVendusByQuantitePeriode($debut, $fin)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du ' . $debut . ' au ' . $fin . ' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {
            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['ventes.client_id', null]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;
            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
                $grandTotal = $grandTotal + $totalCiasse;
            }
        }

        $articlesHorsC =  ArticleVente::where('ventes.client_id', '!=', null)
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
            ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
            ->whereDate('ventes.date_vente', '>=', $dateDebut)
            ->whereDate('ventes.date_vente', '<=', $dateFin)
            ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;
        if (count($articlesHorsC) > 0) {
            $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';

            $totalHorsC = 0;
            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
        }

        $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement d'un article
    public function articlesVendusByQuantiteArticlePdf($article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantiteArticle($article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_articles_vendus_dans_le_depot_' . $info_article->description_article . '.pdf');
    }
    public function articlesVendusByQuantiteArticle($article_id)
    {
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $info_article = Article::find($article_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock concernant ' . $info_article->description_article . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {

            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['article_ventes.article_id', $article_id]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('unites', 'unites.id', 'article_ventes.unite_id')
                ->select('article_ventes.*', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;
            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
            }

            $grandTotal = $grandTotal + $totalCiasse;
        }

        $articlesHorsC =  ArticleVente::where([['ventes.client_id', '!=', null], ['article_ventes.article_id', $article_id]])
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('unites', 'unites.id', 'article_ventes.unite_id')
            ->select('article_ventes.*', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;
        if (count($articlesHorsC) > 0) {
            $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                    <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';



            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }

        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesVendusByDepotPdf($depot)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepot($depot));
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_articles_' . $info_depot->libelle_depot . '_vendus.pdf');
    }
    public function articlesVendusByDepot($depot_id)
    {
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $info_depot = Depot::find($depot_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {

            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['ventes.depot_id', $depot_id]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('unites', 'unites.id', 'article_ventes.unite_id')
                ->join('articles', 'articles.id', 'article_ventes.article_id')
                ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;
            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
            }

            $grandTotal = $grandTotal + $totalCiasse;
        }

        $articlesHorsC =  ArticleVente::where([['ventes.client_id', '!=', null], ['ventes.depot_id', $depot_id]])
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('unites', 'unites.id', 'article_ventes.unite_id')
            ->join('articles', 'articles.id', 'article_ventes.article_id')
            ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;
        if (count($articlesHorsC) > 0) {

            $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                    <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }


        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesVendusByDepotArticlePdf($depot, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepotArticle($depot, $article));
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article);
        return $pdf->stream('liste_' . $info_article->description_article . '_vendus_dans_le_depot_' . $info_depot->libelle_depot . '_pdf');
    }
    public function articlesVendusByDepotArticle($depot_id, $article_id)
    {
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $info_depot = Depot::find($depot_id);
        $info_article = Article::find($article_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du dépôt ' . $info_depot->libelle_depot . ' concernant ' . $info_article->description_article . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;

        foreach ($datas as $data) {
            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['ventes.depot_id', $depot_id], ['article_ventes.article_id', $article_id]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('unites', 'unites.id', 'article_ventes.unite_id')
                ->select('article_ventes.*', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;
            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
            }

            $grandTotal = $grandTotal + $totalCiasse;
        }

        $articlesHorsC =  ArticleVente::where([['ventes.client_id', '!=', null], ['ventes.depot_id', $depot_id], ['article_ventes.article_id', $article_id]])
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('unites', 'unites.id', 'article_ventes.unite_id')
            ->select('article_ventes.*', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;

        if (count($articlesHorsC) > 0) {
            $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                    <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';



            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }

        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesVendusByDepotPeriodePdf($depot, $debut, $fin)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepotPeriode($depot, $debut, $fin));
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_articles_vendus_du_' . $debut . '_au_' . $fin . '_dans_le_depo_' . $info_depot->libelle_depot . '.pdf');
    }
    public function articlesVendusByDepotPeriode($depot, $debut, $fin)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot);
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du ' . $debut . ' au ' . $fin . ' dans le dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {

            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['ventes.client_id', null], ['ventes.depot_id', $depot]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();
            $totalCiasse = 0;

            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
            }

            $grandTotal = $grandTotal + $totalCiasse;
        }

        $articlesHorsC =  ArticleVente::where([['ventes.client_id', '!=', null], ['ventes.depot_id', $depot]])
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
            ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
            ->whereDate('ventes.date_vente', '>=', $dateDebut)
            ->whereDate('ventes.date_vente', '<=', $dateFin)
            ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;
        if (count($articlesHorsC) > 0) {
            $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Pirx U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }

        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
        $outPut .= $this->footer();
        return $outPut;
    }

    public function articlesVendusByDepotArticlePeriodePdf($debut, $fin, $depot, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepotArticlePeriode($debut, $fin, $depot, $article));
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article);
        return $pdf->stream('liste_' . $info_article->description_article . '_vendus_du_' . $debut . '_au_' . $fin . '_dans_le_depo_' . $info_depot->libelle_depot . '.pdf');
    }
    public function articlesVendusByDepotArticlePeriode($debut, $fin, $depot_id, $article_id)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot_id);
        $info_article = Article::find($article_id);
        $datas = DB::table('caisse_ouvertes')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock concernant ' . $info_article->description_article . ' du ' . $debut . ' au ' . $fin . ' dans le dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal = 0;
        foreach ($datas as $data) {

            $articles =  ArticleVente::where([['ventes.caisse_ouverte_id', $data->id], ['ventes.client_id', null], ['ventes.depot_id', $depot_id], ['article_ventes.article_id', $article_id]])
                ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
                ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
                ->get();

            $totalCiasse = 0;

            if (count($articles) > 0) {
                $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>' . $data->libelle_caisse . '</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>' . $data->full_name . '</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


                foreach ($articles as $article) {
                    $totalCiasse = $totalCiasse + $article->quantite * $article->prix - $article->remise_sur_ligne;
                    $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
                }
                $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>' . number_format($totalCiasse, 0, ',', ' ') . '</b></td></tr>';
            }

            $grandTotal = $grandTotal + $totalCiasse;
        }

        $articlesHorsC =  ArticleVente::where([['ventes.client_id', '!=', null], ['ventes.depot_id', $depot_id], ['article_ventes.article_id', $article_id]])
            ->join('ventes', 'ventes.id', 'article_ventes.vente_id')
            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
            ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
            ->whereDate('ventes.date_vente', '>=', $dateDebut)
            ->whereDate('ventes.date_vente', '<=', $dateFin)
            ->select('article_ventes.*', 'articles.code_barre', 'articles.description_article', 'unites.libelle_unite', DB::raw('sum(article_ventes.quantite) as quantite'))
            ->groupBy('article_ventes.article_id', 'article_ventes.unite_id')
            ->get();
        $totalHorsC = 0;

        if (count($articlesHorsC) > 0) {
            $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';


            foreach ($articlesHorsC as $article) {
                $totalHorsC = $totalHorsC + $article->quantite * $article->prix - $article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $article->prix . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $article->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($article->quantite * $article->prix - $article->remise_sur_ligne, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
            }
            $outPut .= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>' . number_format($totalHorsC, 0, ',', ' ') . '</b></td></tr>';
        }

        $outPut .= '</table></div>';
        $outPut .= '<br/> Somme totale : <b> ' . number_format($grandTotal + $totalHorsC, 0, ',', ' ') . ' F CFA</b>';
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
