<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\ArticleTransfert;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\MouvementStock;
use App\Models\Stock\TransfertStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\json_decode;

class TransfertStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $unites = DB::table('unites')->Where('deleted_at', null)->orderBy('libelle_unite', 'asc')->get();
        $menuPrincipal = "Stock";
        $titleControlleur = "Transfert de stock d'article";
        $btnModalAjout = "TRUE";
        return view('stock.transfert-stock.index', compact('depots', 'unites', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeTransfertStock()
    {
        $transfert_stocks = TransfertStock::with('depot_depart', 'depot_arrivee')
            ->select('transfert_stocks.*', DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
            ->Where('transfert_stocks.deleted_at', null)
            ->orderBy('transfert_stocks.date_transfert', 'DESC')
            ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
        return response()->json($jsonData);
    }

    public function listeTransfertStockByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $transfert_stocks = TransfertStock::with('depot_depart', 'depot_arrivee')
            ->select('transfert_stocks.*', DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
            ->Where('transfert_stocks.deleted_at', null)
            ->whereDate('transfert_stocks.date_transfert', '=', $date)
            ->orderBy('transfert_stocks.date_transfert', 'DESC')
            ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
        return response()->json($jsonData);
    }

    public function listeTransfertStockByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $transfert_stocks = TransfertStock::with('depot_depart', 'depot_arrivee')
                                            ->select('transfert_stocks.*', DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                                            ->Where('transfert_stocks.deleted_at', null)
                                            ->whereDate('transfert_stocks.date_transfert', '>=', $date1)
                                            ->whereDate('transfert_stocks.date_transfert', '<=', $date2)
                                            ->orderBy('transfert_stocks.date_transfert', 'DESC')
                                            ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
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
        if ($request->isMethod('post') && $request->input('lotTransfert')) {
            $data = $request->all();
            if ($data['depot_depart_id'] == $data['depot_arrivee_id']) {
                return response()->json(["code" => 0, "msg" => "Impossible de faire du transfèrt dans le même dépôt", "data" => null]);
            }
            try {

                $transfertStock = new TransfertStock;
                $transfertStock->depot_depart_id = $data['depot_depart_id'];
                $transfertStock->depot_arrivee_id = $data['depot_arrivee_id'];
                $transfertStock->date_transfert = Carbon::createFromFormat('d-m-Y', $data['date_transfert']);
                $transfertStock->created_by = Auth::user()->id;
                $transfertStock->save();

                if ($transfertStock != null) {
                    $lotTransfert = is_array($data["lotTransfert"]) ? $data["lotTransfert"] : array($data["lotTransfert"]);
                    foreach ($lotTransfert as $index => $article) {

                        //Enregistrement du transfert
                        $articleTransfert = new ArticleTransfert();
                        $articleTransfert->article_id = $data["lotTransfert"][$index]["articles"];
                        $articleTransfert->quantite_depart = $data["lotTransfert"][$index]["quantites"];
                        $articleTransfert->quantite_reception = $data["lotTransfert"][$index]["quantite_receptions"];
                        $articleTransfert->transfert_stock_id = $transfertStock->id;
                        $articleTransfert->created_by = Auth::user()->id;
                        $articleTransfert->save();

                        //Traitement sur le stock dans depot-article
                        if ($articleTransfert != null) {
                            $depotDpart = DepotArticle::where([['depot_id', $data['depot_depart_id']], ['article_id', $data["lotTransfert"][$index]["articles"]]])->first();
                            $depotArrive = DepotArticle::where([['depot_id', $data["depot_arrivee_id"]], ['article_id', $data["lotTransfert"][$index]["articles"]]])->first();
                            $mouvementStock = MouvementStock::where([['depot_id', $data['depot_depart_id']], ['article_id', $data["lotTransfert"][$index]["articles"]]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_transfert']))->first();

                            if (!$mouvementStock) {
                                $mouvementStock = new MouvementStock;
                                $mouvementStock->date_mouvement = Carbon::createFromFormat('d-m-Y', $data['date_transfert']);
                                $mouvementStock->depot_id = $data['depot_depart_id'];
                                $mouvementStock->article_id = $data["lotTransfert"][$index]["articles"];
                                $mouvementStock->quantite_initiale = $depotDpart != null ? $depotDpart->quantite_disponible : 0;
                                $mouvementStock->created_by = Auth::user()->id;
                            }

                            if (!$depotArrive) {
                                $depotArrive = new DepotArticle;
                                $depotArrive->article_id = $data["lotTransfert"][$index]["articles"];
                                $depotArrive->depot_id = $data["depot_arrivee_id"];
                            }

                            $depotDpart->quantite_disponible = $depotDpart->quantite_disponible - $data["lotTransfert"][$index]["quantites"];
                            $depotDpart->save();
                            $depotArrive->quantite_disponible = $depotArrive->quantite_disponible + $data["lotTransfert"][$index]["quantite_receptions"];
                            $depotArrive->save();
                            $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $data["lotTransfert"][$index]["quantites"];
                            $mouvementStock->save();
                        }
                    }
                }
                $jsonData["data"] = json_decode($transfertStock);
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
     * @param  \App\TransfertStock  $transfertStock
     * @return Response
     */
    public function updateTransfertStocks(Request $request)
    {
        $transfertStock = TransfertStock::find($request->get('idTransfertStockModifier'));
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($transfertStock) {
            $data = $request->all();
            if ($data['depot_depart_id'] == $data['depot_arrivee_id']) {
                return response()->json(["code" => 0, "msg" => "Impossible de faire du transfèrt dans le même dépôt", "data" => null]);
            }
            try {
                //S'il y a changement de depot lors de la modification
                $old_depot_depart_id = $transfertStock->depot_depart_id;
                $old_depot_arrivee_id = $transfertStock->depot_arrivee_id;

                if ($transfertStock->depot_depart_id != $data['depot_depart_id']) {
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id', $transfertStock->id)->get();

                    foreach ($articleTransferts as $articleTransfert) {
                        $depotDepart = DepotArticle::where([['article_id', $articleTransfert->article_id], ['depot_id', $old_depot_depart_id]])->first();
                        $depotDepart->quantite_disponible = $depotDepart->quantite_disponible + $articleTransfert->quantite_depart;
                        $depotDepart->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $old_depot_depart_id], ['article_id', $articleTransfert->article_id]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();
                        $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee - $articleTransfert->quantite_depart;
                        $mouvementStock->save();
                    }
                }

                if ($transfertStock->depot_arrivee_id != $data['depot_arrivee_id']) {
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id', $transfertStock->id)->get();
                    foreach ($articleTransferts as $articleTransfert) {
                        $depotArrive = DepotArticle::where([['article_id', $articleTransfert->article_id], ['depot_id', $transfertStock->depot_arrivee_id]])->first();
                        $depotArrive->quantite_disponible = $depotArrive->quantite_disponible - $articleTransfert->quantite_reception;
                        $depotArrive->save();
                    }
                }

                $transfertStock->depot_depart_id = $data['depot_depart_id'];
                $transfertStock->depot_arrivee_id = $data['depot_arrivee_id'];
                $transfertStock->date_transfert = Carbon::createFromFormat('d-m-Y', $data['date_transfert']);
                $transfertStock->updated_by = Auth::user()->id;
                $transfertStock->save();

                //S'il y a changement de depot lors de la modification
                if ($old_depot_depart_id != $data['depot_depart_id']) {
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id', $transfertStock->id)->get();
                    foreach ($articleTransferts as $articleTransfert) {
                        $depotDepart = DepotArticle::where([['article_id', $articleTransfert->article_id], ['depot_id', $data['depot_depart_id']]])->first();
                        $depotDepart->quantite_disponible = $depotDepart->quantite_disponible - $articleTransfert->quantite_depart;
                        $depotDepart->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $data['depot_depart_id']], ['article_id', $articleTransfert->article_id]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_transfert']))->first();
                        $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $articleTransfert->quantite_depart;
                        $mouvementStock->save();
                    }
                }

                if ($old_depot_arrivee_id != $data['depot_arrivee_id']) {
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id', $transfertStock->id)->get();
                    foreach ($articleTransferts as $articleTransfert) {
                        $depotArrive = DepotArticle::where([['article_id', $articleTransfert->article_id], ['depot_id', $data['depot_arrivee_id']]])->first();
                        $depotArrive->quantite_disponible = $depotArrive->quantite_disponible + $articleTransfert->quantite_reception;
                        $depotArrive->save();
                    }
                }
                $jsonData["data"] = json_decode($transfertStock);
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
     * @param  \App\TransfertStock  $transfertStock
     * @return Response
     */
    public function destroy(TransfertStock $transfertStock)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($transfertStock) {
            try {
                //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                $articleTransferts = ArticleTransfert::where('transfert_stock_id', $transfertStock->id)->get();
                foreach ($articleTransferts as $articleTransfert) {
                    $depotDepart = DepotArticle::where([['article_id', $articleTransfert->article_id], ['depot_id', $transfertStock->depot_depart_id]])->first();
                    $depotDepart->quantite_disponible = $depotDepart->quantite_disponible + $articleTransfert->quantite_depart;
                    $depotDepart->save();

                    $depotArrive = DepotArticle::where([['article_id', $articleTransfert->article_id], ['depot_id', $transfertStock->depot_arrivee_id]])->first();
                    $depotArrive->quantite_disponible = $depotArrive->quantite_disponible - $articleTransfert->quantite_reception;
                    $depotArrive->save();

                    $mouvementStock = MouvementStock::where([['depot_id', $transfertStock->depot_depart_id], ['article_id', $articleTransfert->article_id]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();
                    $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee - $articleTransfert->quantite_depart;
                    $mouvementStock->save();
                }

                $transfertStock->update(['deleted_by' => Auth::user()->id]);
                $transfertStock->delete();
                $jsonData["data"] = json_decode($transfertStock);
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
    public function transfertStockPdf($transfertStock)
    {
        // Chargement des données
        $data = $this->transfertStock($transfertStock);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articles_transfert']);
        $data['qteTotalTransfert'] = 0;
        $data['qteTotalReception'] = 0;
        $index = 1;
        // Calcul du montant total
        foreach ($data['articles_transfert'] as $article) {
            $data['qteTotalTransfert'] += $article['quantite_depart'];
            $data['qteTotalReception'] += $article['quantite_reception'];
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.etat-transfert-stock', $data);
    }

    public function transfertStock($transfertStock)
    {
        $infos_transfert = TransfertStock::with('depot_depart', 'depot_arrivee')
        ->select('transfert_stocks.*', DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
        ->Where([['transfert_stocks.deleted_at', null], ['transfert_stocks.id', $transfertStock]])
            ->orderBy('transfert_stocks.date_transfert', 'DESC')
            ->first();
        $articles_transfert = ArticleTransfert::where('article_transferts.deleted_at', null)
        ->join('articles', 'articles.id', '=', 'article_transferts.article_id')
        ->select('article_transferts.*', 'articles.libelle_article', 'articles.code_barre')
        ->Where([['article_transferts.transfert_stock_id', $transfertStock]])
            ->get();
        return [
            'infos_transfert' => $infos_transfert,
            'articles_transfert' => $articles_transfert,
            'title' => 'fiche-transfert-stock-pdf',
        ];
    }

    public function header($transfertStock)
    {


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
                       Fiche de transfert de stock
                    </div>
                </div>
                <div class="fixed-header-right">
                    <div class="container">
                       Du dépôt : <b>' . $infosTransfertStocks->depot_depart->libelle_depot . '</b><br/>
                       Au dépôt : <b>' . $infosTransfertStocks->depot_arrivee->libelle_depot . '</b><br/>
                       Date : <b>' . $infosTransfertStocks->date_transferts . '</b>
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
