<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Stock\Approvisionnement;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\MouvementStock;
use App\Models\Stock\ArticleApprovisionnement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function view;

class ApprovisionnementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $articles = DB::table('articles')->Where('deleted_at', null)->get();
        $unites = DB::table('unites')->Where('deleted_at', null)->get();
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();

        $menuPrincipal = "Stock";
        $titleControlleur = "Approvisionnement d'articles";
        $btnModalAjout = "TRUE";
        return view('stock.approvisionnement.index', compact('depots', 'articles', 'unites', 'fournisseurs', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeApprovisionnement()
    {
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where('approvisionnements.deleted_at', null)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $jsonData["rows"] = $approvisionnements->toArray();
        $jsonData["total"] = $approvisionnements->count();
        return response()->json($jsonData);
    }

    public function listeApprovisionnementByFournisseur($fournisseur)
    {
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['approvisionnements.deleted_at', null], ['fournisseur_id', $fournisseur]])
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $jsonData["rows"] = $approvisionnements->toArray();
        $jsonData["total"] = $approvisionnements->count();
        return response()->json($jsonData);
    }

    public function listeApprovisionnementsByDepot($depot)
    {
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['approvisionnements.deleted_at', null], ['depot_id', $depot]])
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $jsonData["rows"] = $approvisionnements->toArray();
        $jsonData["total"] = $approvisionnements->count();
        return response()->json($jsonData);
    }
    public function listeApprovisionnementsByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where('approvisionnements.deleted_at', null)
            ->whereDate('approvisionnements.date_approvisionnement', '=', $date)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $jsonData["rows"] = $approvisionnements->toArray();
        $jsonData["total"] = $approvisionnements->count();
        return response()->json($jsonData);
    }

    public function listeApprovisionnementByPeriode($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where('approvisionnements.deleted_at', null)
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $date1)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $date2)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $jsonData["rows"] = $approvisionnements->toArray();
        $jsonData["total"] = $approvisionnements->count();
        return response()->json($jsonData);
    }

    public function listeApprovisionnementByPeriodeFournisseur($debut, $fin, $fournisseur)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['approvisionnements.deleted_at', null], ['fournisseur_id', $fournisseur]])
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $date1)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $date2)
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->get();
        $jsonData["rows"] = $approvisionnements->toArray();
        $jsonData["total"] = $approvisionnements->count();
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
        if ($request->isMethod('post') && $request->input('date_approvisionnement') && !empty($request->input('lotApprovisionnement'))) {

            $data = $request->all();

            try {
                if (empty($data['lotApprovisionnement'])) {
                    return response()->json(["code" => 0, "msg" => "Vous n'avez pas ajouté d'articles à cet approvisionnement", "data" => null]);
                }

                $approvisionnement = new Approvisionnement;
                $approvisionnement->depot_id = $data['depot_id'];
                $approvisionnement->date_approvisionnement = Carbon::createFromFormat('d-m-Y', $data['date_approvisionnement']);
                $approvisionnement->fournisseur_id = isset($data['fournisseur_id']) && !empty($data['fournisseur_id']) ? $data['fournisseur_id'] : null;
                $approvisionnement->created_by = Auth::user()->id;
                $approvisionnement->save();

                if ($approvisionnement) {
                    //enregistrement des articles de l'approvisionnement
                    $lotApprovisionnement = is_array($data["lotApprovisionnement"]) ? $data["lotApprovisionnement"] : array($data["lotApprovisionnement"]);

                    foreach ($lotApprovisionnement as $index => $article) {
                        $depotArticle = DepotArticle::where([['depot_id', $data['depot_id']], ['article_id', $data["lotApprovisionnement"][$index]["articles"]]])->first();
                        $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["lotApprovisionnement"][$index]["articles"]]])->whereDate('date_mouvement', $approvisionnement->date_approvisionnement)->first();

                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = $approvisionnement->date_approvisionnement;
                            $mouvementStock->depot_id = $data['depot_id'];
                            $mouvementStock->article_id = $data["lotApprovisionnement"][$index]["articles"];
                            $mouvementStock->quantite_initiale = $depotArticle ? $depotArticle->quantite_disponible : 0;
                            $mouvementStock->date_peremption = isset($data["lotApprovisionnement"][$index]["date_peremptions"]) && !empty($data["lotApprovisionnement"][$index]["date_peremptions"]) ? Carbon::createFromFormat('d-m-Y', $data["lotApprovisionnement"][$index]["date_peremptions"]) : null;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        if (!$depotArticle) {
                            $depotArticle = new DepotArticle;
                            $depotArticle->article_id = $data["lotApprovisionnement"][$index]["articles"];
                            $depotArticle->depot_id = $data['depot_id'];
                            $depotArticle->date_peremption = isset($data["lotApprovisionnement"][$index]["date_peremptions"]) && !empty($data["lotApprovisionnement"][$index]["date_peremptions"]) ? Carbon::createFromFormat('d-m-Y', $data["lotApprovisionnement"][$index]["date_peremptions"]) : null;
                            $depotArticle->created_by = Auth::user()->id;
                        }

                        $depotArticle->quantite_disponible = $depotArticle->quantite_disponible + $data["lotApprovisionnement"][$index]["quantites"];
                        $depotArticle->save();
                        $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee + $data["lotApprovisionnement"][$index]["quantites"];
                        $mouvementStock->save();

                        $articleApprovisionnement = new ArticleApprovisionnement();
                        $articleApprovisionnement->article_id = $data["lotApprovisionnement"][$index]["articles"];
                        $articleApprovisionnement->approvisionnement_id = $approvisionnement->id;
                        $articleApprovisionnement->quantite = $data["lotApprovisionnement"][$index]["quantites"];
                        $articleApprovisionnement->date_peremption = isset($data["lotApprovisionnement"][$index]["date_peremptions"]) && !empty($data["lotApprovisionnement"][$index]["date_peremptions"]) ? Carbon::createFromFormat('d-m-Y', $data["lotApprovisionnement"][$index]["date_peremptions"]) : null;
                        $articleApprovisionnement->created_by = Auth::user()->id;
                        $articleApprovisionnement->save();
                    }
                }
                $jsonData["data"] = json_decode($approvisionnement);
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
     * @param  \App\Approvisionnement  $approvisionnement
     * @return Response
     */
    public function updateApprovisionnement(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $approvisionnement = Approvisionnement::find($request->get('idApprovisionnement'));
        if ($approvisionnement) {
            $data = $request->all();
            try {
                $oldDepot = $approvisionnement->depot_id;
                if (isset($data['depot_id']) && $oldDepot != $data['depot_id']) {
                    //Récuperation des anciens articles
                    $articleApprovisionnements = ArticleApprovisionnement::where('approvisionnement_id', $approvisionnement->id)->get();
                    foreach ($articleApprovisionnements as $articleApprovisionnement) {
                        $articleDepot = DepotArticle::where([['article_id', $articleApprovisionnement->article_id], ['depot_id', $approvisionnement->depot_id]])->first();
                        $articleDepot->quantite_disponible = $articleDepot->quantite_disponible - $articleApprovisionnement->quantite;
                        $articleDepot->save();

                        //Gestion des mouvements d'article
                        $mouvementStock = MouvementStock::where([['date_mouvement', $approvisionnement->date_approvisionnement], ['depot_id', $approvisionnement->depot_id], ['article_id', $articleApprovisionnement->article_id]])->whereDate('date_peremption', $articleApprovisionnement->date_peremption)->first();
                        $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee - $articleApprovisionnement->quantite;
                        $mouvementStock->updated_by = Auth::user()->id;
                        $mouvementStock->save();
                    }
                }

                $approvisionnement->depot_id = $data['depot_id'];
                $approvisionnement->date_approvisionnement = Carbon::createFromFormat('d-m-Y', $data['date_approvisionnement']);
                $approvisionnement->fournisseur_id = isset($data['fournisseur_id']) && !empty($data['fournisseur_id']) ? $data['fournisseur_id'] : null;
                $approvisionnement->updated_by = Auth::user()->id;
                $approvisionnement->save();

                if (isset($data['depot_id']) && $oldDepot != $data['depot_id']) {
                    foreach ($articleApprovisionnements as $articleApprovisionnement) {
                        $depotArticle = DepotArticle::where([['depot_id', $data['depot_id']], ['article_id', $articleApprovisionnement->article_id]])->first();
                        $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $articleApprovisionnement->article_id]])->whereDate('date_mouvement', $data['date_approvisionnement'])->whereDate('date_peremption', $articleApprovisionnement->date_peremption)->first();

                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = $approvisionnement->date_approvisionnement;
                            $mouvementStock->depot_id = $data['depot_id'];
                            $mouvementStock->article_id = $articleApprovisionnement->article_id;
                            $mouvementStock->quantite_initiale = $depotArticle != null ? $depotArticle->quantite_disponible : 0;
                            $mouvementStock->date_peremption = $articleApprovisionnement->date_peremption != null ? $articleApprovisionnement->date_peremption : null;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        if (!$depotArticle) {
                            $depotArticle = new DepotArticle;
                            $depotArticle->article_id = $articleApprovisionnement->article_id;
                            $depotArticle->depot_id = $data['depot_id'];
                            $depotArticle->date_peremption = $articleApprovisionnement->date_peremption;
                            $depotArticle->created_by = Auth::user()->id;
                        }
                        $depotArticle->quantite_disponible = $depotArticle->quantite_disponible + $articleApprovisionnement->quantite;
                        $depotArticle->save();
                        $mouvementStock->quantite_approvisionnee = $mouvementStock->quantite_approvisionnee + $articleApprovisionnement->quantite;
                        $mouvementStock->save();
                    }
                }

                $jsonData["data"] = json_decode($approvisionnement);
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
     * @param  \App\Approvisionnement  $approvisionnement
     * @return Response
     */
    public function destroy(Approvisionnement $approvisionnement)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($approvisionnement) {
            try {

                $articleApprovisionnements = ArticleApprovisionnement::where('approvisionnement_id', $approvisionnement->id)->get();
                foreach ($articleApprovisionnements as $articleApprovisionnement) {
                    $articleApprovisionnement->delete();
                }
                $approvisionnement->update(['deleted_by' => Auth::user()->id]);
                $approvisionnement->delete();
                $jsonData["data"] = json_decode($approvisionnement);
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

    public function ficheApprovisionnementPdf($approvisionnement)
    {
        $info_approvisionnement = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['approvisionnements.deleted_at', null], ['approvisionnements.id', $approvisionnement]])
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->first();
        $info_approvisionnement->fournisseur_id != null ? $name_fournisseur = $info_approvisionnement->fournisseur->full_name_fournisseur : $name_fournisseur = "";
        $article_approvisionnements = ArticleApprovisionnement::with('article')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->join('depot_articles', 'depot_articles.id', '=', 'articles.id')
            ->select('article_approvisionnements.*', 'articles.prix_achat_ttc', 'depot_articles.quantite_disponible', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
            ->Where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.approvisionnement_id', $approvisionnement]])
            ->get();
        // Chargement des données
        $data = [
            'info_approvisionnement' => $info_approvisionnement,
            'article_approvisionnements' => $article_approvisionnements,
            'title' => 'fiche-approvisionnement-pdf',
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['article_approvisionnements']);
        $data['qteTotal'] = 0;
        $data['montantTotal'] = 0;
        $index = 1;
        // Calcul de la quantité total
        foreach ($data['article_approvisionnements'] as $article) {
            $data['qteTotal'] += $article['quantite'];
            $data['montantTotal'] += $article['prix_achat_ttc'] * $article['quantite'];
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.etat-approvisionnement', $data);
    }

    public function content($approvisionnement)
    {
        $elements = ArticleApprovisionnement::with('article')
            ->join('articles', 'articles.id', '=', 'article_approvisionnements.article_id')
            ->select('article_approvisionnements.*', 'articles.prix_achat_ttc', DB::raw('DATE_FORMAT(article_approvisionnements.date_peremption, "%d-%m-%Y") as date_peremptions'))
            ->Where([['article_approvisionnements.deleted_at', null], ['article_approvisionnements.approvisionnement_id', $approvisionnement]])
            ->get();

        $content = '<div class="container-table">
                        <table border="1" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Date de péremption</th>
                                <th cellspacing="0" border="2" width="12%" align="center">Qté / Btle</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Prix achat TTC</th>
                                <th cellspacing="0" border="2" width="20%" align="center">Prix total achat</th>
                            </tr>';

        $prixTotalAchat = 0;
        $qteTotale = 0;
        $totalPrixAchat = 0;
        foreach ($elements as $element) {
            $prixTotalAchat = $prixTotalAchat + $element->prix_achat_ttc * $element->quantite;
            $qteTotale = $qteTotale + $element->quantite;
            $totalPrixAchat = $totalPrixAchat + $element->prix_achat_ttc;

            $content .= '<tr>
                            <td style="font-size:13px;"  cellspacing="0" border="2">&nbsp;&nbsp;&nbsp;' . $element->article->libelle_article . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">' . $element->date_peremptions . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">' . $element->quantite . '&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right">' . number_format($element->prix_achat_ttc, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right">' . number_format($element->prix_achat_ttc * $element->quantite, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }
        $content .= '<tr>
                        <td style="font-size:13px;"  cellspacing="0"  colspan="2" border="2" align="left" width="70%"><b>&nbsp;&nbsp;Total général</b></td>
                        <td style="font-size:15px;"  cellspacing="0"  border="2" align="center"><b>' . number_format($qteTotale, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</b></td>
                        <td style="font-size:15px;"  cellspacing="0"  border="2" align="right">&nbsp;&nbsp;<b>' . number_format($totalPrixAchat, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</b></td>
                        <td style="font-size:15px;"  cellspacing="0"  border="2" align="right">&nbsp;&nbsp;<b>' . number_format($prixTotalAchat, 0, ',', ' ') . '&nbsp;&nbsp;&nbsp;</b></td>
                    </tr>
                </table>
         </div>';

        return $content;
    }

    public function header($approvisionnement)
    {
        $info_approvisionnement = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['approvisionnements.deleted_at', null], ['approvisionnements.id', $approvisionnement]])
            ->orderBy('approvisionnements.date_approvisionnement', 'DESC')
            ->first();
        $info_approvisionnement->fournisseur_id != null ? $name_fournisseur = $info_approvisionnement->fournisseur->full_name_fournisseur : $name_fournisseur = "";
        $header = '<html>
                         <head>
                            <meta charset="utf-8">
                            <title></title>
                                    <style>
                                        .container-table{
                                            margin:130px 0;
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
                                        .fixed-header-right{
                                            width: 40%;
                                            height:6%;
                                            float: right;
                                            position: absolute;
                                            top: 0;
                                            background: #fff;
                                            padding: 10px 0;
                                            color: #333;
                                            border: 1px #333 solid;
                                            border-radius: 3px;
                                        }
                                        .fixed-header-center{
                                            width:35%;
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
                <body style="margin-bottom:0; margin-top:0px;">
                <div class="fixed-header-left">
                    <div class="container">
                         <img src=' . $this->infosConfig()->logo . ' width="200" height="160"/>
                    </div>
                </div>
                <div class="fixed-header-center">
                    <div class="container">
                       Fiche Approvisionnement
                    </div>
                </div>
                <div class="fixed-header-right">
                    <div class="container">
                       Dépôt : <b>' . $info_approvisionnement->depot->libelle_depot . '</b><br/>
                       Date : <b>' . $info_approvisionnement->date_approvisionnements . '</b><br/>
                       Fournisseur : <b>' . $name_fournisseur . '</b>
                    </div>
                </div>';
        return $header;
    }
    //Footer fiche
    public function footer()
    {
        $footer = "<div class='fixed-footer'>
                        <div class='page-number'></div>
                    </div>
            </body>
        </html>";
        return $footer;
    }
}
