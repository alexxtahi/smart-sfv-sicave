<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\ArticleDestocker;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\Destockage;
use App\Models\Stock\MouvementStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DestockageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Stock";
        $titleControlleur = "Déstockage";
        $btnModalAjout = "TRUE";
        return view('Stock.destockage.index', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeDestockage()
    {
        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where('destockages.deleted_at', null)
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        $jsonData["rows"] = $destockages->toArray();
        $jsonData["total"] = $destockages->count();
        return response()->json($jsonData);
    }

    public function listeDestockageByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where('destockages.deleted_at', null)
            ->whereDate('destockages.date_destockage', '=', $date)
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        $jsonData["rows"] = $destockages->toArray();
        $jsonData["total"] = $destockages->count();
        return response()->json($jsonData);
    }

    public function listeDestockageByDepot($depot)
    {
        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where([['destockages.deleted_at', null], ['destockages.depot_id', $depot]])
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        $jsonData["rows"] = $destockages->toArray();
        $jsonData["total"] = $destockages->count();
        return response()->json($jsonData);
    }

    public function listeDestockageByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where('destockages.deleted_at', null)
            ->whereDate('destockages.date_destockage', '>=', $date1)
            ->whereDate('destockages.date_destockage', '<=', $date2)
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        $jsonData["rows"] = $destockages->toArray();
        $jsonData["total"] = $destockages->count();
        return response()->json($jsonData);
    }

    public function listeDestockageByPeriodeDepot($debut,$fin,$depot){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $destockages = Destockage::with('depot')
                                    ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
                                    ->Where([['destockages.deleted_at', null], ['destockages.depot_id', $depot]])
                                    ->whereDate('destockages.date_destockage', '>=', $date1)
                                    ->whereDate('destockages.date_destockage', '<=', $date2)
                                    ->orderBy('destockages.date_destockage', 'DESC')
                                    ->get();
        $jsonData["rows"] = $destockages->toArray();
        $jsonData["total"] = $destockages->count();
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
        if ($request->isMethod('post') && $request->input('motif') && !empty($request->input('lotDestockage'))) {
            $data = $request->all();
            try {

                $destockage = new Destockage;
                $destockage->motif = $data['motif'];
                $destockage->depot_id = $data['depot_id'];
                $destockage->date_destockage = Carbon::createFromFormat('d-m-Y', $data['date_destockage']);
                $destockage->created_by = Auth::user()->id;
                $destockage->save();

                if ($destockage != null) {
                    $lotDestockage = is_array($data["lotDestockage"]) ? $data["lotDestockage"] : array($data["lotDestockage"]);
                    foreach ($lotDestockage as $index => $article) {
                        //Enregistrement du destockage
                        $articleDestocker = new ArticleDestocker;
                        $articleDestocker->article_id = $data["lotDestockage"][$index]["articles"];
                        $articleDestocker->quantite_destocker = $data["lotDestockage"][$index]["quantites"];
                        $articleDestocker->destockage_id = $destockage->id;
                        $articleDestocker->created_by = Auth::user()->id;
                        $articleDestocker->save();

                        //Traitement sur le stock dans depot-article
                        if ($articleDestocker != null) {
                            $DepotArticle = DepotArticle::where([['depot_id', $data["depot_id"]], ['article_id', $data["lotDestockage"][$index]["articles"]]])->first();
                            $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["lotDestockage"][$index]["articles"]]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_destockage']))->first();

                            if (!$mouvementStock) {
                                $mouvementStock = new MouvementStock;
                                $mouvementStock->date_mouvement = Carbon::createFromFormat('d-m-Y', $data['date_destockage']);
                                $mouvementStock->depot_id = $data['depot_id'];
                                $mouvementStock->article_id = $data["lotDestockage"][$index]["articles"];
                                $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                                $mouvementStock->created_by = Auth::user()->id;
                            }

                            $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $data["lotDestockage"][$index]["quantites"];
                            $DepotArticle->save();
                            $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker + $data["lotDestockage"][$index]["quantites"];
                            $mouvementStock->save();
                        }
                    }
                }
                $jsonData["data"] = json_decode($destockage);
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
     * @param  \App\Destockage  $destockage
     * @return Response
     */
    public function updateDestockage(Request $request)
    {
        $destockage = Destockage::find($request->get('idDestockageModifier'));
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($destockage) {
            $data = $request->all();
            try {
                $old_depot_id = $destockage->depot_id;
                //S'il y a changement de depot lors de la modification
                if ($destockage->depot_id != $data['depot_id']) {
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleDestockers = ArticleDestocker::where('destockage_id', $destockage->id)->get();
                    foreach ($articleDestockers as $articleDestocker) {
                        $DepotArticle = DepotArticle::where([['article_id', $articleDestocker->article_id], ['depot_id', $destockage->depot_id]])->first();
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $articleDestocker->quantite_destocker;
                        $DepotArticle->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $destockage->depot_id], ['article_id', $articleDestocker->article_id]])->whereDate('date_mouvement', $destockage->date_destockage)->first();
                        $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker - $articleDestocker->quantite_destocker;
                        $mouvementStock->save();
                    }
                }

                $destockage->motif = $data['motif'];
                $destockage->depot_id = $data['depot_id'];
                $destockage->date_destockage = Carbon::createFromFormat('d-m-Y', $data['date_destockage']);
                $destockage->updated_by = Auth::user()->id;
                $destockage->save();

                //S'il y a changement de depot lors de la modification
                if ($old_depot_id != $data['depot_id']) {
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleDestockers = ArticleDestocker::where('destockage_id', $destockage->id)->get();
                    foreach ($articleDestockers as $articleDestocker) {
                        $Depot = DepotArticle::where([['article_id', $articleDestocker->article_id], ['depot_id', $data['depot_id']]])->first();


                        $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $articleDestocker->article_id]])->whereDate('date_mouvement', $destockage->date_destockage)->first();
                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = Carbon::createFromFormat('d-m-Y', $data['date_destockage']);
                            $mouvementStock->depot_id = $data['depot_id'];
                            $mouvementStock->article_id = $articleDestocker->article_id;
                            $mouvementStock->quantite_initiale = $Depot != null ? $Depot->quantite_disponible : 0;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        $Depot->quantite_disponible = $Depot->quantite_disponible - $articleDestocker->quantite_destocker;
                        $Depot->save();
                        $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker + $articleDestocker->quantite_destocker;
                        $mouvementStock->save();
                    }
                }
                $jsonData["data"] = json_decode($destockage);
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
     * @param  \App\Destockage  $destockage
     * @return Response
     */
    public function destroy(Destockage $destockage)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($destockage) {
            try {
                //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                $articleDestockers = ArticleDestocker::where('destockage_id', $destockage->id)->get();
                foreach ($articleDestockers as $articleDestocker) {
                    $depot = DepotArticle::where([['article_id', $articleDestocker->article_id], ['depot_id', $destockage->depot_id]])->first();
                    $depot->quantite_disponible = $depot->quantite_disponible + $articleDestocker->quantite_destocker;
                    $depot->save();
                    $mouvementStock = MouvementStock::where([['depot_id', $destockage->depot_id], ['article_id', $articleDestocker->article_id]])->whereDate('date_mouvement', $destockage->date_destockage)->first();
                    $mouvementStock->quantite_destocker = $mouvementStock->quantite_destocker - $articleDestocker->quantite_destocker;
                    $mouvementStock->save();
                }
                $destockage->update(['deleted_by' => Auth::user()->id]);
                $destockage->delete();
                $jsonData["data"] = json_decode($destockage);
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
    public function destockagePdf($destockage)
    {
        $infosDestockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where([['destockages.deleted_at', null], ['destockages.id', $destockage]])
            ->orderBy('destockages.date_destockage', 'DESC')
            ->first();
        $articlesDestocker = ArticleDestocker::with('article', 'unite')
            ->join('articles', 'articles.id', '=', 'article_destockers.id')
            ->join('depot_articles', 'depot_articles.article_id', '=', 'articles.id')
            ->select('article_destockers.*', 'depot_articles.quantite_disponible as qteEnStock')
            ->Where([['article_destockers.deleted_at', null], ['article_destockers.destockage_id', $destockage]])
            ->get();
        // Chargement des données
        $data = [
            'infosDestockages' => $infosDestockages,
            'articlesDestocker' => $articlesDestocker,
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articlesDestocker']);
        $data['qteTotal'] = 0;
        $index = 1;
        // Calcul de la quantité total
        foreach ($data['articlesDestocker'] as $article) {
            $data['qteTotal'] += $article['quantite_destocker'];
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.etat-destockage', $data);
    }

    public function content($destockage)
    {
        $elements = ArticleDestocker::with('article', 'unite')
            ->select('article_destockers.*')
            ->Where([['article_destockers.deleted_at', null], ['article_destockers.destockage_id', $destockage]])
            ->get();
        $content = '<div class="container-table">
                        <table border="1" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="60%" align="center">Article</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Qté / Btle</th>
                            </tr>';

        foreach ($elements as $element) {
            $content .= '<tr>
                            <td style="font-size:13px;"  cellspacing="0" border="2">&nbsp;&nbsp;' . $element->article->libelle_article . '</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">' . $element->quantite_destocker . '</td>
                       </tr>';
        }

        return $content;
    }

    public function header($destockage)
    {
        $infosDestockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where([['destockages.deleted_at', null], ['destockages.id', $destockage]])
            ->orderBy('destockages.date_destockage', 'DESC')
            ->first();

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
                                            width: 50%;
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
                       Fiche de déstockage
                    </div>
                </div>
                <div class="fixed-header-right">
                    <div class="container">
                       Dépôt : <b>' . $infosDestockages->depot->libelle_depot . '</b><br/>
                       Motif : <b>' . $infosDestockages->motif . '</b><br/>
                       Date : <b>' . $infosDestockages->date_destockages . '</b>
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
