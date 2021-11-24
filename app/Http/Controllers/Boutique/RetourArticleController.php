<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleRetourne;
use App\Models\Boutique\ArticleVente;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\MouvementStock;
use App\Models\Boutique\RetourArticle;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

include_once(app_path() . "/number-to-letters/nombre_en_lettre.php");

class RetourArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $ventes = DB::table('ventes')->Where([['deleted_at', null], ['divers', 0], ['attente', 0], ['proformat', 0]])->get();
        $retours = DB::table('ventes')
            ->join('retour_articles', 'retour_articles.vente_id', '=', 'ventes.id')
            ->select('ventes.*')
            ->Where('retour_articles.deleted_at', null)
            ->get();
        $menuPrincipal = "Boutique";
        $titleControlleur = "Retour d'articles";
        $btnModalAjout = "TRUE";
        return view('boutique.retour-article.index', compact('ventes', 'retours', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vueRetourMateriel()
    {
        $vente_materiels = DB::table('vente_materiels')->select('vente_materiels.*')->Where('deleted_at', null)->get();

        $retours = DB::table('vente_materiels')
            ->join('retour_articles', 'retour_articles.vente_materiel_id', '=', 'vente_materiels.id')
            ->select('vente_materiels.*')
            ->Where('retour_articles.deleted_at', null)
            ->get();

        $menuPrincipal = "Canal";
        $titleControlleur = "Retour de matériel";
        $btnModalAjout = "TRUE";
        return view('canal.retour-materiel.index', compact('vente_materiels', 'retours', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeRetourMateriel()
    {
        $retour_materiels = RetourArticle::where('retour_articles.deleted_at', null)
            ->join('materiel_retournes', 'materiel_retournes.id', '=', 'retour_articles.vente_materiel_id')
            ->join('vente_materiels', 'vente_materiels.id', '=', 'retour_articles.vente_materiel_id')
            ->join('agences', 'agences.id', '=', 'vente_materiels.agence_id')
            ->join('materiels', 'materiels.id', '=', 'materiel_retournes.materiel_id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_materiels->toArray();
        $jsonData["total"] = $retour_materiels->count();
        return response()->json($jsonData);
    }

    public function listeRetourArticle()
    {
        $retour_articles = RetourArticle::where('retour_articles.deleted_at', null)
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }

    public function listeRetourArticleByVente($vente)
    {
        $retour_articles = RetourArticle::where([['retour_articles.deleted_at', null], ['ventes.id', $vente]])
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }

    public function listeRetourArticleByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $retour_articles = RetourArticle::where('retour_articles.deleted_at', null)
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->whereDate('retour_articles.date_retour', $date)
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }

    public function listeRetourArticleByDepot($depot)
    {
        $retour_articles = RetourArticle::where([['retour_articles.deleted_at', null], ['ventes.depot_id', $depot]])
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }
    public function listeRetourArticleByArticle($article)
    {
        $retour_articles = RetourArticle::where([['retour_articles.deleted_at', null], ['article_retournes.article_id', $article]])
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }
    public function listeRetourArticleByPeriodeDepot($dateDebuts, $dateFins, $depot)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $dateDebuts);
        $dateFin = Carbon::createFromFormat('d-m-Y', $dateFins);
        $retour_articles = RetourArticle::where([['retour_articles.deleted_at', null], ['ventes.depot_id', $depot]])
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->whereDate('retour_articles.date_retour', '>=', $dateDebut)
            ->whereDate('retour_articles.date_retour', '<=', $dateFin)
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }

    public function listeRetourArticleByPeriodeArticle($dateDebuts, $dateFins, $article)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $dateDebuts);
        $dateFin = Carbon::createFromFormat('d-m-Y', $dateFins);
        $retour_articles = RetourArticle::where([['retour_articles.deleted_at', null], ['article_retournes.retour_article_id', $article]])
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->whereDate('retour_articles.date_retour', '>=', $dateDebut)
            ->whereDate('retour_articles.date_retour', '<=', $dateFin)
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
        return response()->json($jsonData);
    }

    public function listeRetourArticleByPeriode($dateDebuts, $dateFins)
    {
        $dateDebut = Carbon::createFromFormat('d-m-Y', $dateDebuts);
        $dateFin = Carbon::createFromFormat('d-m-Y', $dateFins);
        $retour_articles = RetourArticle::where('retour_articles.deleted_at', null)
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'retour_articles.id as id_ligne', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->whereDate('retour_articles.date_retour', '>=', $dateDebut)
            ->whereDate('retour_articles.date_retour', '<=', $dateFin)
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->get();
        $jsonData["rows"] = $retour_articles->toArray();
        $jsonData["total"] = $retour_articles->count();
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
        if ($request->isMethod('post') && $request->input('vente_id') && !empty($request->input('lotArticle'))) {
            $data = $request->all();
            try {
                $vente = Vente::find($data['vente_id']);
                $retourArticle = new RetourArticle;
                $retourArticle->vente_id = $data['vente_id'];
                $retourArticle->date_retour = Carbon::createFromFormat('d-m-Y', $data['date_retour']);
                $retourArticle->created_by = Auth::user()->id;
                $retourArticle->save();

                if ($retourArticle != null) {
                    $lotArticle = is_array($data["lotArticle"]) ? $data["lotArticle"] : array($data["lotArticle"]);
                    foreach ($lotArticle as $index => $article) {
                        //Enregistrement du destockage
                        $articleRetourne = new ArticleRetourne;
                        $articleRetourne->article_id = $data["lotArticle"][$index]["articles"];
                        $articleRetourne->unite_id = $data["lotArticle"][$index]["unites"];
                        $articleRetourne->quantite = $data["lotArticle"][$index]["quantites"];
                        $articleRetourne->quantite_vendue = $data["lotArticle"][$index]["quantite_vendues"];
                        $articleRetourne->prix_unitaire = $data["lotArticle"][$index]["prix"];
                        $articleRetourne->retour_article_id = $retourArticle->id;
                        $articleRetourne->created_by = Auth::user()->id;
                        $articleRetourne->save();

                        //Traitement sur le stock dans depot-article
                        if ($articleRetourne != null) {
                            $ArticleVente = ArticleVente::where([['vente_id', $data['vente_id']], ['article_id', $data["lotArticle"][$index]["articles"]], ['unite_id', $data["lotArticle"][$index]["unites"]]])->first();
                            $Article = Article::find($data["lotArticle"][$index]["articles"]);
                            if ($Article != null && $Article->stockable == 1) {
                                $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $data["lotArticle"][$index]["articles"]], ['unite_id', $data["lotArticle"][$index]["unites"]]])->first();
                                $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $data["lotArticle"][$index]["articles"]], ['unite_id', $data["lotArticle"][$index]["unites"]]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_retour']))->first();
                                if (!$mouvementStock) {
                                    $mouvementStock = new MouvementStock;
                                    $mouvementStock->date_mouvement = Carbon::createFromFormat('d-m-Y', $data['date_retour']);
                                    $mouvementStock->depot_id = $vente->depot_id;
                                    $mouvementStock->article_id = $data["lotArticle"][$index]["articles"];
                                    $mouvementStock->unite_id = $data["lotArticle"][$index]["unites"];
                                    $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                                    $mouvementStock->created_by = Auth::user()->id;
                                }
                                $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $data["lotArticle"][$index]["quantites"];
                                $DepotArticle->save();
                                $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee + $data["lotArticle"][$index]["quantites"];
                                $mouvementStock->save();
                            }

                            if ($ArticleVente->quantite == $data["lotArticle"][$index]["quantites"]) {
                                $ArticleVente->retourne = TRUE;
                            }
                            $ArticleVente->quantite = $ArticleVente->quantite - $data["lotArticle"][$index]["quantites"];
                            $ArticleVente->save();
                        }
                    }
                }
                $jsonData["data"] = json_decode($retourArticle);
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
     * @param  \App\RetourArticle  $retourArticle
     * @return Response
     */
    public function updateRetourArticle(Request $request)
    {
        $retourArticle = RetourArticle::find($request->input('idRetourArticleModifier'));
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        if ($retourArticle) {
            $data = $request->all();
            try {
                $old_vente = $retourArticle->vente_id;
                if ($retourArticle->vente_id != $data['vente_id']) {
                    $articleRetournes = ArticleRetourne::where('retour_article_id', $retourArticle->id)->get();
                    if ($articleRetournes->count() > 0) {
                        $vente = Vente::find($retourArticle->vente_id);
                        foreach ($articleRetournes as $articleRetourne) {
                            $Article = Article::find($articleRetourne->article_id);
                            $ArticleVente = ArticleVente::where([['vente_id', $vente->id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                            if ($Article != null && $Article->stockable == 1) {
                                $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                                $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                                $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $articleRetourne->quantite;
                                $DepotArticle->save();
                                $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee - $articleRetourne->quantite;
                                $mouvementStock->save();
                            }
                            $ArticleVente->quantite = $ArticleVente->quantite + $articleRetourne->quantite;
                            $ArticleVente->retourne = FALSE;
                            $ArticleVente->save();
                        }
                    }
                }

                $retourArticle->vente_id = $data['vente_id'];
                $retourArticle->date_retour = Carbon::createFromFormat('d-m-Y', $data['date_retour']);
                $retourArticle->updated_by = Auth::user()->id;
                $retourArticle->save();

                if ($old_vente != $data['vente_id']) {
                    $articleRetournes = ArticleRetourne::where('retour_article_id', $retourArticle->id)->get();
                    if ($articleRetournes->count() > 0) {
                        $Vente = Vente::find($data['vente_id']);
                        foreach ($articleRetournes as $articleRetourne) {
                            $ArticleVente = ArticleVente::where([['vente_id', $data['vente_id']], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                            $Article = Article::find($articleRetourne->article_id);
                            if ($Article != null && $Article->stockable == 1) {
                                $DepotArticle = DepotArticle::where([['depot_id', $Vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                                $mouvementStock = MouvementStock::where([['depot_id', $Vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                                $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $articleRetourne->quantite;
                                $DepotArticle->save();
                                $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee + $articleRetourne->quantite;
                                $mouvementStock->save();
                            }

                            if ($ArticleVente->quantite == $articleRetourne->quantite) {
                                $ArticleVente->retourne = TRUE;
                            }
                            $ArticleVente->quantite = $ArticleVente->quantite - $articleRetourne->quantite;
                            $ArticleVente->save();
                        }
                    }
                }
                $jsonData["data"] = json_decode($retourArticle);
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
     * @param  \App\RetourArticle  $retourArticle
     * @return Response
     */
    public function destroy($id)
    {
        $retourArticle = RetourArticle::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($retourArticle) {
            try {

                $articleRetournes = ArticleRetourne::where('retour_article_id', $retourArticle->id)->get();
                if ($articleRetournes->count() > 0) {
                    $vente = Vente::find($retourArticle->vente_id);
                    foreach ($articleRetournes as $articleRetourne) {
                        $ArticleVente = ArticleVente::where([['vente_id', $vente->id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                        $Article = Article::find($articleRetourne->article_id);
                        if ($Article != null && $Article->stockable == 1) {
                            $DepotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->first();
                            $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleRetourne->article_id], ['unite_id', $articleRetourne->unite_id]])->whereDate('date_mouvement', $retourArticle->date_retour)->first();
                            $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $articleRetourne->quantite;
                            $DepotArticle->save();
                            $mouvementStock->quantite_retoutnee = $mouvementStock->quantite_retoutnee - $articleRetourne->quantite;
                            $mouvementStock->save();
                        }
                        $ArticleVente->quantite = $ArticleVente->quantite + $articleRetourne->quantite;
                        $ArticleVente->retourne = FALSE;
                        $ArticleVente->save();
                    }
                }

                $retourArticle->update(['deleted_by' => Auth::user()->id]);
                $retourArticle->delete();
                $jsonData["data"] = json_decode($retourArticle);
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

    //Facture de retour d'articles
    public function recuRetourArticlePdf($retour)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->recuRetourArticle($retour));
        $retour_article = RetourArticle::find($retour);
        $vente = Vente::find($retour_article->vente_id);
        $vente->numero_ticket != null ? $numero_facture_ticket = $vente->numero_ticket : $numero_facture_ticket = 'FACT' . $vente->numero_facture;
        return $pdf->stream('reçu_retour_article_du_' . $numero_facture_ticket . '.pdf');
    }

    public function recuRetourArticle($retour)
    {
        $outPut = $this->factureHeader($retour);
        $outPut .= $this->factureContent($retour);
        $outPut .= $this->factureFooter();
        return $outPut;
    }
    public function factureContent($retour)
    {

        $articlesRetournes = ArticleRetourne::with('article', 'unite')
            ->select('article_retournes.*')
            ->Where('article_retournes.retour_article_id', $retour)
            ->get();
        $montantTT = 0;
        $content = '<div class="container-table">
                        <table border="1" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="55%" align="center">Article</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Colis</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle vendue</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle retournée</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Prix</th>
                                <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            </tr>';

        foreach ($articlesRetournes as $element) {
            $montantTT = $montantTT + $element->quantite * $element->prix_unitaire;
            $content .= '<tr>
                            <td style="font-size:13px;"  cellspacing="0" border="2" width="55%">&nbsp;&nbsp;&nbsp;' . $element->article->description_article . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center" width="10%">' . $element->unite->libelle_unite . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center" width="10%">' . $element->quantite_vendue . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center" width="10%">' . $element->quantite . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right" width="15%">' . number_format($element->prix_unitaire, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right" width="20%">' . number_format($element->quantite * $element->prix_unitaire, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }

        $content .= '<tr>
                        <td style="font-size:13px;"  cellspacing="0" colspan="5" border="2" align="left" width="70%">&nbsp;&nbsp;Montant Total</td>
                        <td style="font-size:15px;"  cellspacing="0" colspan="1" border="2" align="right" width="30%">&nbsp;&nbsp;' . number_format($montantTT, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table>
                <p style="font-style: italic;"> NET A PAYER <b>' . ucfirst(NumberToLetter($montantTT)) . ' F CFA</b></p>
         </div>';

        return $content;
    }

    //Header & footer facture
    public function factureHeader($retour)
    {
        $retour_article = RetourArticle::find($retour);
        $vente = Vente::find($retour_article->vente_id);
        $vente->numero_ticket != null ? $numero_facture_ticket = " le <b>" . $vente->numero_ticket . "</b>" : " la " . $numero_facture_ticket = ' la <b>FACT' . $vente->numero_facture . '</b>';
        $facture = RetourArticle::where([['retour_articles.deleted_at', null], ['retour_articles.id', $retour]])
            ->join('ventes', 'ventes.id', '=', 'retour_articles.vente_id')
            ->join('depots', 'depots.id', '=', 'ventes.depot_id')
            ->join('article_retournes', 'article_retournes.retour_article_id', '=', 'retour_articles.id')
            ->select('retour_articles.*', 'depots.libelle_depot', DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'), 'ventes.numero_facture', 'ventes.numero_ticket', DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
            ->groupBy('article_retournes.retour_article_id')
            ->orderBy('retour_articles.id', 'DESC')
            ->first();
        $header = "<html>
                         <head>
                            <meta charset='utf-8'>
                            <title></title>
                                    <style>
                                        .container-table{
                                            margin:200px 0;
                                            width: 100%;
                                        }
                                        .container{
                                            width: 100%;
                                            margin: 2px 5px;
                                            font-size:15px;
                                        }
                                        .fixed-header-left{
                                            width: 34%;
                                            height:4%;
                                            position: absolute;
                                            line-height:1;
                                            font-size:13px;
                                            top: 0;
                                        }

                                        .fixed-header-center{
                                            width:66%;
                                            height:7%;
                                            margin: 0 150px;
                                            top: 0;
                                            text-align:center;
                                            position: absolute;
                                        }
                                        .fixed-footer{
                                            position: fixed;
                                            bottom: -28;
                                            left: 0px;
                                            right: 0px;
                                            height: 80px;
                                            text-align:center;
                                        }
                                        .titre-style{
                                         text-align:center;
                                         text-decoration: underline;
                                        }
                                    footer{
                                    font-size:13px;
                                    position: absolute;
                                    bottom: -35px;
                                    left: 0px;
                                    right: 0px;
                                    height: 80px;
                                    text-align:center;
                                    }
                                    </style>
                        </head>
                <body style='margin-bottom:0; margin-top:0px;'>
                 <div class='fixed-header-left'>
                    <div class='container'>
                         <img src=" . $this->infosConfig()->logo . " width='200' height='160'/>
                    </div>
                </div>
                <div class='fixed-header-center'>
                    <div class='container'>
                       Retour d'article sur " . $numero_facture_ticket . "<br/>
                       Date du retour : <b>" . $facture->date_retours . "</b><br/>
                    </div>
                </div>";
        return $header;
    }
    //Footer fiche
    public function factureFooter()
    {
        $type_compagnie = '';
        $capital = '';
        $rccm = '';
        $ncc = '';
        $adresse_compagnie = '';
        $numero_compte_banque = '';
        $banque = '';
        $nc_tresor = '';
        $email_compagnie = '';
        $cellulaire = '';
        $telephone_faxe = '';
        $telephone_fixe = '';
        $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
        $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
        $nom_compagnie = str_replace($search, $replace, $this->infosConfig()->nom_compagnie);
        if ($this->infosConfig()->type_compagnie != null) {
            $type_compagnie = $this->infosConfig()->type_compagnie;
        }
        if ($this->infosConfig()->capital != null) {
            $capital = ' au capital de ' . number_format($this->infosConfig()->capital, 0, ',', ' ') . ' F CFA';
        }
        if ($this->infosConfig()->rccm != null) {
            $rccm = ' RCCM ' . $this->infosConfig()->rccm;
        }
        if ($this->infosConfig()->ncc != null) {
            $ncc = ' NCC ' . $this->infosConfig()->ncc;
        }
        if ($this->infosConfig()->adresse_compagnie != null) {
            $adresse_compagnie = ' Siège social: ' . $this->infosConfig()->adresse_compagnie;
        }
        if ($this->infosConfig()->numero_compte_banque != null) {
            $numero_compte_banque = $this->infosConfig()->numero_compte_banque;
        }
        if ($this->infosConfig()->banque != null) {
            $banque = 'N° de compte - ' . $this->infosConfig()->banque . ': ';
        }
        if ($this->infosConfig()->nc_tresor != null) {
            $nc_tresor = ' - TRESOR: ' . $this->infosConfig()->nc_tresor;
        }
        if ($this->infosConfig()->email_compagnie != null) {
            $email_compagnie = ' Email : ' . $this->infosConfig()->email_compagnie;
        }
        if ($this->infosConfig()->cellulaire != null) {
            $cellulaire = ' / ' . $this->infosConfig()->cellulaire;
        }
        if ($this->infosConfig()->telephone_faxe != null) {
            $telephone_faxe = ' Fax : ' . $this->infosConfig()->telephone_faxe;
        }
        if ($this->infosConfig()->telephone_fixe != null) {
            $telephone_fixe = ' Tel : ' . $this->infosConfig()->telephone_fixe;
        }
        $footer = "<footer>
                        <hr width='100%'>
                      <b>" . strtoupper($nom_compagnie) . "</b><br/>
                      " . strtoupper($type_compagnie) . "" . $capital . "" . $rccm . "" . $ncc . "" . $adresse_compagnie . "
                        " . $banque . "" . $numero_compte_banque . "" . $nc_tresor . "" . $email_compagnie . "
                        Cel: " . $this->infosConfig()->contact_responsable . "" . $cellulaire . "" . $telephone_fixe . "" . $telephone_faxe . "
               </footer>
            </body>
        </html>";
        return $footer;
    }
}
