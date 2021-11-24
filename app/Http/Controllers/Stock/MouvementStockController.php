<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\MouvementStock;
use App\Models\Parametre\Article;
use App\Models\Parametre\Depot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use function view;

class MouvementStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $articles = DB::table('articles')->Where('deleted_at', null)->orderBy('description_article', 'asc')->get();
        $menuPrincipal = "Stock";
        $titleControlleur = "Mouvement des stocks du jour";
        $btnModalAjout = "FALSE";
        return view('boutique.mouvement-stock.index', compact('depots', 'articles', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vueMouvementStockGrouper()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $articles = DB::table('articles')->Where('deleted_at', null)->orderBy('description_article', 'asc')->get();
        $menuPrincipal = "Stock";
        $titleControlleur = "Mouvement des stocks du jour";
        $btnModalAjout = "FALSE";
        return view('boutique.mouvement-stock.mouvement-stock-articles', compact('depots', 'articles', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeMouvementStock()
    {
        $date_jour = date("Y-m-d");
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where('mouvement_stocks.deleted_at', null)
            ->whereDate('mouvement_stocks.date_mouvement', $date_jour)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }

    public function listeMouvementStockArticleByDepotOnPeriode($debut, $fin, $article, $depot)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article], ['depot_id', $depot]])
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockByPeriode($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where('mouvement_stocks.deleted_at', null)
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockByArticleOnPeriode($debut, $fin, $article)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article]])
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockByArticle($article)
    {
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article]])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockByArticleDepot($article, $depot)
    {
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article], ['depot_id', $depot]])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockByDepot($depot)
    {
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['depot_id', $depot]])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockByDepotOnPeriode($debut, $fin, $depot)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $mouvementStocks = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['depot_id', $depot]])
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }


    public function listeMouvementStockGrouper()
    {
        $date_jour = date("Y-m-d");
        $mouvementStocks = MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy('mouvement_stocks.article_id')
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->whereDate('mouvement_stocks.date_mouvement', $date_jour)
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }

    public function listeMouvementStockGrouperByArticle($article)
    {
        $mouvementStocks = MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy(['mouvement_stocks.depot_id', 'mouvement_stocks.article_id'])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->where('mouvement_stocks.article_id', $article)
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockGrouperByDepot($depot)
    {
        $mouvementStocks = MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy(['mouvement_stocks.depot_id', 'mouvement_stocks.article_id'])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->where('mouvement_stocks.depot_id', $depot)
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
        return response()->json($jsonData);
    }
    public function listeMouvementStockGrouperByDepotArticle($depot, $article)
    {
        $mouvementStocks = MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy(['mouvement_stocks.depot_id', 'mouvement_stocks.article_id'])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->where([['mouvement_stocks.depot_id', $depot], ['mouvement_stocks.article_id', $article]])
            ->get();
        $jsonData["rows"] = $mouvementStocks->toArray();
        $jsonData["total"] = $mouvementStocks->count();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MouvementStock  $mouvementStock
     * @return Response
     */
    public function show(MouvementStock $mouvementStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MouvementStock  $mouvementStock
     * @return Response
     */
    public function edit(MouvementStock $mouvementStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\MouvementStock  $mouvementStock
     * @return Response
     */
    public function update(Request $request, MouvementStock $mouvementStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MouvementStock  $mouvementStock
     * @return Response
     */
    public function destroy(MouvementStock $mouvementStock)
    {
        //
    }
    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    //Etat
    //Mouvement de stock grouper par article et dépôt
    public function mouvementStockGrouperPdf()
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockGrouper());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock.pdf');
    }
    public function mouvementStockGrouper()
    {
        $date_jour = date("Y-m-d");
        $datas =   MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy('mouvement_stocks.article_id')
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->whereDate('mouvement_stocks.date_mouvement', $date_jour)
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock du jour ' . date("d-m-Y") . ' par article</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->sommeQuantiteInitiale + $data->sommeQuantiteApprovisionnee) - ($data->sommeQuantiteDestocker + $data->sommeQuantiteTransferee + $data->sommeQuantiteVendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->depot->libelle_depot . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteInitiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteApprovisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteDestocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteTransferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteVendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockGrouperByArticlePdf($article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockGrouperByArticle($article));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock.pdf');
    }
    public function mouvementStockGrouperByArticle($article)
    {
        $info_article = Article::find($article);
        $datas =   MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy(['mouvement_stocks.depot_id', 'mouvement_stocks.article_id'])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->where('mouvement_stocks.article_id', $article)
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock de ' . $info_article->description_article . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->sommeQuantiteInitiale + $data->sommeQuantiteApprovisionnee) - ($data->sommeQuantiteDestocker + $data->sommeQuantiteTransferee + $data->sommeQuantiteVendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->depot->libelle_depot . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteInitiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteApprovisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteDestocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteTransferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteVendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockGrouperByDepotPdf($depot)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockGrouperByDepot($depot));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock.pdf');
    }
    public function mouvementStockGrouperByDepot($depot)
    {
        $info_depot = Depot::find($depot);
        $datas =  MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy(['mouvement_stocks.depot_id', 'mouvement_stocks.article_id'])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->where('mouvement_stocks.depot_id', $depot)
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock dans le dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->sommeQuantiteInitiale + $data->sommeQuantiteApprovisionnee) - ($data->sommeQuantiteDestocker + $data->sommeQuantiteTransferee + $data->sommeQuantiteVendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteInitiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteApprovisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteDestocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteTransferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteVendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockGrouperByDepotArticlePdf($depot, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockGrouperByDepotArticle($depot, $article));
        //        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock.pdf');
    }
    public function mouvementStockGrouperByDepotArticle($depot, $article)
    {
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article);
        $datas =  MouvementStock::with('depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('sum(mouvement_stocks.quantite_initiale) as sommeQuantiteInitiale'), DB::raw('sum(mouvement_stocks.quantite_vendue) as sommeQuantiteVendue'), DB::raw('sum(mouvement_stocks.quantite_transferee) as sommeQuantiteTransferee'), DB::raw('sum(mouvement_stocks.quantite_destocker) as sommeQuantiteDestocker'), DB::raw('sum(mouvement_stocks.quantite_approvisionnee) as sommeQuantiteApprovisionnee'), DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->groupBy(['mouvement_stocks.depot_id', 'mouvement_stocks.article_id'])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->where([['mouvement_stocks.depot_id', $depot], ['mouvement_stocks.article_id', $article]])
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock ' . $info_article->description_article . ' dans le dépôt ' . $info_depot->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->sommeQuantiteInitiale + $data->sommeQuantiteApprovisionnee) - ($data->sommeQuantiteDestocker + $data->sommeQuantiteTransferee + $data->sommeQuantiteVendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteInitiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteApprovisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteDestocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteTransferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->sommeQuantiteVendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    //Mouvement de stock normal
    public function mouvementStockArticleByDepotPdf($article, $depot)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockArticleByDepot($article, $depot));
        return $pdf->stream('liste_mouvements_stock_periode_article_depot.pdf');
    }
    public function mouvementStockArticleByDepot($article, $depot)
    {
        $article_infos = Article::find($article);
        $depot_infos = Depot::find($depot);
        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article], ['depot_id', $depot]])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock concernant ' . $article_infos->description_article . ' dans le dépôt ' . $depot_infos->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockArticleByDepotOnPeriodePdf($debut, $fin, $article, $depot)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockArticleByDepotOnPeriode($debut, $fin, $article, $depot));
        return $pdf->stream('liste_mouvements_stock_periode_article_depot.pdf');
    }
    public function mouvementStockArticleByDepotOnPeriode($debut, $fin, $article, $depot)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $article_infos = Article::find($article);
        $depot_infos = Depot::find($depot);
        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article], ['depot_id', $depot]])
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock du ' . $debut . ' au ' . $fin . ' concernant ' . $article_infos->description_article . ' dans le dépôt ' . $depot_infos->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvemenStockByPeriodePdf($debut, $fin)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvemenStockByPeriode($debut, $fin));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock_sur_une_periode.pdf');
    }
    public function mouvemenStockByPeriode($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where('mouvement_stocks.deleted_at', null)
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock du ' . $debut . ' au ' . $fin . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->depot->libelle_depot . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockByArticleOnPeriodePdf($debut, $fin, $article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockByArticleOnPeriode($debut, $fin, $article));
        return $pdf->stream('liste_mouvements_stock_periode_article.pdf');
    }
    public function mouvementStockByArticleOnPeriode($debut, $fin, $article)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $article_infos = Article::find($article);
        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article]])
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock du ' . $debut . ' au ' . $fin . ' concernant ' . $article_infos->description_article . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                             <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                         <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->depot->libelle_depot . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockByDepotOnPeriodePdf($debut, $fin, $depot)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockByDepotOnPeriode($debut, $fin, $depot));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock_periode_depot.pdf');
    }
    public function mouvementStockByDepotOnPeriode($debut, $fin, $depot)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $depot_infos = Depot::find($depot);
        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['depot_id', $depot]])
            ->whereDate('mouvement_stocks.date_mouvement', '>=', $date1)
            ->whereDate('mouvement_stocks.date_mouvement', '<=', $date2)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock du ' . $debut . ' au ' . $fin . ' concernant le dépôt ' . $depot_infos->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                         <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                         <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockArticlePdf($article)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockArticle($article));
        return $pdf->stream('liste_mouvements_stock_article.pdf');
    }
    public function mouvementStockArticle($article)
    {
        $article_infos = Article::find($article);
        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['article_id', $article]])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock concernant ' . $article_infos->description_article . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->depot->libelle_depot . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockDepotPdf($depot)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStockDepot($depot));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock_depot.pdf');
    }
    public function mouvementStockDepot($depot)
    {
        $depot_infos = Depot::find($depot);
        $datas = MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where([['mouvement_stocks.deleted_at', null], ['depot_id', $depot]])
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock concernant le dépôt ' . $depot_infos->libelle_depot . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                         <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                         <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    public function mouvementStockPdf()
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->mouvementStock());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_mouvements_stock.pdf');
    }
    public function mouvementStock()
    {
        $date_jour = date("Y-m-d");
        $datas =  MouvementStock::with('unite', 'depot', 'article')
            ->select('mouvement_stocks.*', DB::raw('DATE_FORMAT(mouvement_stocks.date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->Where('mouvement_stocks.deleted_at', null)
            ->whereDate('mouvement_stocks.date_mouvement', $date_jour)
            ->orderBy('mouvement_stocks.date_mouvement', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvements de stock du jour</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle initiale</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle appro.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle déstockée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle transf.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle vendue</th>
                            <th cellspacing="0" border="2" width="15%" align="center">En stock</th>
                        </tr>
                    </div>';
        $totalStockFormatter = 0;
        foreach ($datas as $data) {
            $totalStockFormatter = (($data->quantite_approvisionnee + $data->quantite_initiale) - ($data->quantite_destocker + $data->quantite_transferee + $data->quantite_vendue));
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->date_mouvements . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->code_barre . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->article->description_article . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->unite->libelle_unite . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->depot->libelle_depot . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_initiale . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_approvisionnee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_destocker . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_transferee . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite_vendue . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $totalStockFormatter . '</td>
                        </tr>
                       ';
        }

        $outPut .= '</table>';

        $outPut .= $this->footer();
        return $outPut;
    }

    //Header and footer for pdf
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
