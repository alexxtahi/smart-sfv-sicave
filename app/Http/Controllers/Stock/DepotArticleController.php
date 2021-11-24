<?php

namespace App\Http\Controllers\Stock;

use Exception;
use function response;
use App\Models\Stock\Depot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Stock\DepotArticle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArticleByDepotExport;

class DepotArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = Depot::where('depots.deleted_at', null)
            ->select('depots.*')
            ->orderBy('libelle_depot')
            ->get();

        $menuPrincipal = "Boutique";
        $titleControlleur = "Les des dépôts avec leurs articles";
        $btnModalAjout = "FALSE";
        return view('boutique.depot-articles.index', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeDepotByArticle($article)
    {
        $articles = DepotArticle::with('depot')
            ->where('depot_articles.article_id', $article)
            ->select('depot_articles.*')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByDepot($depot)
    {
        $articles = DepotArticle::with('depot', 'article')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->where('depot_articles.depot_id', $depot)
            ->select('depot_articles.*')
            ->orderBy('articles.libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByDepotCodeBarre($depot, $codeBarre)
    {
        $articles = DepotArticle::with('depot', 'article')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->where([['depot_articles.depot_id', $depot], ['articles.code_barre', $codeBarre]])
            ->select('depot_articles.*', 'param_tvas.tva')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByArticleDepot($article, $depot)
    {
        $articles = DepotArticle::with('depot', 'article')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->where([['depot_articles.depot_id', $depot], ['depot_articles.article_id', $article]])
            ->orderBy('articles.libelle_article', 'ASC')
            ->select('depot_articles.*')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByCategorieDepot($categorie, $depot)
    {
        $articles = DepotArticle::with('depot', 'article')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->where([['depot_articles.depot_id', $depot], ['articles.categorie_id', $categorie]])
            ->select('depot_articles.*')
            ->orderBy('articles.libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
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
                $DepotArticle = DepotArticle::where([['depot_id', $data['depot_id']], ['article_id', $data['article_id']]])->first();
                if ($DepotArticle != null) {
                    return response()->json(["code" => 0, "msg" => "Ce enregistrement existe déjà sur cet article, vérifier la liste", "data" => null]);
                }
                $depotArticle = new DepotArticle;
                $depotArticle->article_id = $data['article_id'];
                $depotArticle->depot_id = $data['depot_id'];
                $depotArticle->prix_vente_detail = $data['prix_vente_detail'];
                $depotArticle->prix_vente_gros = $data['prix_vente_gros'];
                $depotArticle->prix_vente_demi_gros = $data['prix_vente_demi_gros'];
                $depotArticle->created_by = Auth::user()->id;
                $depotArticle->save();
                $jsonData["data"] = json_decode($depotArticle);
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
     * @param  \App\DepotArticle  $depotArticle
     * @return Response
     */
    public function update(Request $request, DepotArticle $depotArticle)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        if ($depotArticle) {
            $data = $request->all();
            try {

                $depotArticle->depot_id = $data['depot_id'];
                $depotArticle->prix_vente_detail = $data['prix_vente_detail'];
                $depotArticle->prix_vente_gros = $data['prix_vente_gros'];
                $depotArticle->prix_vente_demi_gros = $data['prix_vente_demi_gros'];
                $depotArticle->updated_by = Auth::user()->id;
                $depotArticle->save();
                $jsonData["data"] = json_decode($depotArticle);
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
     * @param  \App\DepotArticle  $depotArticle
     * @return Response
     */
    public function destroy(DepotArticle $depotArticle)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($depotArticle) {
            try {
                $depotArticle->update(['deleted_by' => Auth::user()->id]);
                $depotArticle->delete();
                $jsonData["data"] = json_decode($depotArticle);
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

    //Export excel
    public function exportExcelArticlByDepot($depot_id)
    {
        return Excel::download(new ArticleByDepotExport($depot_id), 'articles_par_depot.xlsx');
    }


    //Article par dépôt PDF
    public function listeArticleByDepotPdf($depot_id)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepot($depot_id));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_' . $info_depot->libelle_depot . '_.pdf');
    }
    public function articleByDepot($depot_id)
    {
        $info_depot = Depot::find($depot_id);

        $datas = DepotArticle::where('depot_articles.depot_id', $depot_id)
            ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles du dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
        $total = 0;
        $totalArticle = 0;
        $totalVente = 0;
        foreach ($datas as $data) {
            $totalArticle = $totalArticle + $data->quantite_disponible;
            $totalVente = $totalVente + ($data->prix_vente * $data->quantite_disponible);
            $data->param_tva_id != null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente;
            $prix_achat_ttc = $data->prix_achat_ttc;

            $prix_vente_ht = ($prix_vente_ttc / ($tva + 1));
            $prixVHT = round($prix_vente_ht, 0);

            $prix_achat_ht = ($prix_achat_ttc / ($tva + 1));
            $prixAHT = round($prix_achat_ht, 0);

            $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->code_barre . '</td>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->description_article . '</td>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_disponible . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($prixAHT, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($prixVHT, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente * $data->quantite_disponible, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
        }

        $outPut .= '</table>';
        $outPut .= '<br/> Nombre totale:<b> ' . number_format($totalArticle, 0, ',', ' ') . ' article(s)</b> <i>pour un montant de vente TTC global de </i><b>' . number_format($totalVente, 0, ',', ' ') . '</b> F CFA';
        $outPut .= $this->footer();
        return $outPut;
    }

    //Article par dépôt by categorie PDF
    public function listeArticleByDepotByCategoriePdf($depot_id, $categorie)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepotByCategorie($depot_id, $categorie));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_' . $info_depot->libelle_depot . '_.pdf');
    }

    public function articleByDepotByCategorie($depot_id, $categorie)
    {

        $info_depot = Depot::find($depot_id);
        $info_categorie = \App\Models\Parametre\Categorie::find($categorie);
        $datas = DepotArticle::where([['depot_articles.depot_id', $depot_id], ['articles.categorie_id', $categorie]])
            ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->join('categories', 'categories.id', '=', 'articles.categorie_id')
            ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles de catégorie ' . $info_categorie->libelle_categorie . ' du dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
        $total = 0;
        $totalArticle = 0;
        $totalVente = 0;
        foreach ($datas as $data) {
            $totalArticle = $totalArticle + $data->quantite_disponible;
            $totalVente = $totalVente + ($data->prix_vente * $data->quantite_disponible);
            $data->param_tva_id != null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente;
            $prix_achat_ttc = $data->prix_achat_ttc;

            $prix_vente_ht = ($prix_vente_ttc / ($tva + 1));
            $prixVHT = round($prix_vente_ht, 0);

            $prix_achat_ht = ($prix_achat_ttc / ($tva + 1));
            $prixAHT = round($prix_achat_ht, 0);

            $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->code_barre . '</td>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->description_article . '</td>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_disponible . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($prixAHT, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($prixVHT, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente * $data->quantite_disponible, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
        }

        $outPut .= '</table>';
        $outPut .= '<br/> Nombre totale:<b> ' . number_format($totalArticle, 0, ',', ' ') . ' article(s)</b> <i>pour un montant de vente TTC global de </i><b>' . number_format($totalVente, 0, ',', ' ') . '</b> F CFA';
        $outPut .= $this->footer();
        return $outPut;
    }


    //Article par dépôt by quantité PDF
    public function listeArticleByDepotByQuantitePdf($depot_id, $quantite)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepotByQuantite($depot_id, $quantite));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_' . $info_depot->libelle_depot . '_.pdf');
    }

    public function articleByDepotByQuantite($depot_id, $quantite)
    {

        $info_depot = Depot::find($depot_id);

        if ($quantite == 1) {
            $datas = DepotArticle::where([['depot_articles.depot_id', $depot_id], ['depot_articles.quantite_disponible', '>', 0]])
                ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
                ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.categorie_id')
                ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
                ->get();
            $outPut = $this->header();
            $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles avec quantité disponible du dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        }

        if ($quantite == 2) {
            $datas = DepotArticle::where([['depot_articles.depot_id', $depot_id], ['depot_articles.quantite_disponible', '=', 0]])
                ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
                ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.categorie_id')
                ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
                ->get();
            $outPut = $this->header();
            $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles dont la quantité est 0 du dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">';
        }


        $outPut .= '<tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
        $total = 0;
        $totalArticle = 0;
        $totalVente = 0;
        foreach ($datas as $data) {
            $totalArticle = $totalArticle + $data->quantite_disponible;
            $totalVente = $totalVente + ($data->prix_vente * $data->quantite_disponible);
            $data->param_tva_id != null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente;
            $prix_achat_ttc = $data->prix_achat_ttc;

            $prix_vente_ht = ($prix_vente_ttc / ($tva + 1));
            $prixVHT = round($prix_vente_ht, 0);

            $prix_achat_ht = ($prix_achat_ttc / ($tva + 1));
            $prixAHT = round($prix_achat_ht, 0);

            $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->code_barre . '</td>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->description_article . '</td>
                            <td  cellspacing="0" border="2">&nbsp;' . $data->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_disponible . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($prixAHT, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_achat_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($prixVHT, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente * $data->quantite_disponible, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
        }

        $outPut .= '</table>';
        $outPut .= '<br/> Nombre totale:<b> ' . number_format($totalArticle, 0, ',', ' ') . ' article(s)</b> <i>pour un montant de vente TTC global de </i><b>' . number_format($totalVente, 0, ',', ' ') . '</b> F CFA';
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
