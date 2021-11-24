<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\DepotArticle;
use App\Models\Stock\DetailInventaire;
use App\Models\Stock\Inventaire;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $categories = DB::table('categories')->Where([['deleted_at', null], ['categorie_id', null]])->orderBy('libelle_categorie', 'asc')->get();
        $menuPrincipal = "Stock";
        $titleControlleur = "Inventaire";
        $btnModalAjout = "TRUE";
        return view('stock.inventaire.index', compact('depots', 'categories', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeInventaire()
    {
        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where('inventaires.deleted_at', null)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        $jsonData["rows"] = $inventaires->toArray();
        $jsonData["total"] = $inventaires->count();
        return response()->json($jsonData);
    }

    public function listeInventaireByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where('inventaires.deleted_at', null)
            ->whereDate('inventaires.date_inventaire', '=', $date)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        $jsonData["rows"] = $inventaires->toArray();
        $jsonData["total"] = $inventaires->count();
        return response()->json($jsonData);
    }

    public function listeInventaireByDepot($depot)
    {
        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where([['inventaires.deleted_at', null], ['inventaires.depot_id', $depot]])
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        $jsonData["rows"] = $inventaires->toArray();
        $jsonData["total"] = $inventaires->count();
        return response()->json($jsonData);
    }

    public function listeInventaireByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where('inventaires.deleted_at', null)
            ->whereDate('inventaires.date_inventaire', '>=', $date1)
            ->whereDate('inventaires.date_inventaire', '<=', $date2)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        $jsonData["rows"] = $inventaires->toArray();
        $jsonData["total"] = $inventaires->count();
        return response()->json($jsonData);
    }

    public function listeInventaireByPeriodeDepot($debut,$fin,$depot){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where([['inventaires.deleted_at', null], ['inventaires.depot_id', $depot]])
            ->whereDate('inventaires.date_inventaire', '>=', $date1)
            ->whereDate('inventaires.date_inventaire', '<=', $date2)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        $jsonData["rows"] = $inventaires->toArray();
        $jsonData["total"] = $inventaires->count();
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
        if ($request->isMethod('post') && $request->input('date_inventaire') && !empty($request->input('lotArticle'))) {

            $data = $request->all();

            try {
                if (empty($data['lotArticle'])) {
                    return response()->json(["code" => 0, "msg" => "Vous n'avez pas ajouté d'articles à cet inventaire", "data" => null]);
                }

                $inventaire = new Inventaire;
                $inventaire->libelle_inventaire = $data['libelle_inventaire'];
                $inventaire->date_inventaire = Carbon::createFromFormat('d-m-Y', $data['date_inventaire']);
                $inventaire->depot_id = $data['depot_id'];
                $inventaire->created_by = Auth::user()->id;
                $inventaire->save();

                if ($inventaire) {
                    $lotArticle = is_array($data["lotArticle"]) ? $data["lotArticle"] : array($data["lotArticle"]);

                    foreach ($lotArticle as $index => $article) {

                        //Enregistrement de detail-inventaire
                        $detailInventaire = new DetailInventaire;
                        $detailInventaire->article_id = $data["lotArticle"][$index]["articles"];
                        $detailInventaire->inventaire_id = $inventaire->id;
                        $detailInventaire->quantite_en_stocke = $data["lotArticle"][$index]["quantite_en_stocks"];
                        $detailInventaire->quantite_denombree = $data["lotArticle"][$index]["quantite_denombrees"];
                        $detailInventaire->date_peremption = isset($data["lotArticle"][$index]["date_peremptions"]) && !empty($data["lotArticle"][$index]["date_peremptions"]) ? Carbon::createFromFormat('d-m-Y', $data["lotArticle"][$index]["date_peremptions"]) : null;
                        $detailInventaire->created_by = Auth::user()->id;
                        $detailInventaire->save();

                        //Ajustement du stock
                        $DepotArticle = DepotArticle::where([['depot_id', $data['depot_id']], ['article_id', $data["lotArticle"][$index]["articles"]]])->first();
                        $DepotArticle->quantite_disponible = $data["lotArticle"][$index]["quantite_denombrees"];
                        $DepotArticle->date_peremption = isset($data["lotArticle"][$index]["date_peremptions"]) && !empty($data["lotArticle"][$index]["date_peremptions"]) ? Carbon::createFromFormat('d-m-Y', $data["lotArticle"][$index]["date_peremptions"]) : null;
                        $DepotArticle->save();
                    }
                }

                $jsonData["data"] = json_decode($inventaire);
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
     * @param  \App\Inventaire  $inventaire
     * @return Response
     */
    public function updateInventaire(Request $request)
    {
        $inventaire = Inventaire::find($request->input('idInventaireModifier'));
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        if ($inventaire) {
            $data = $request->all();
            try {

                $inventaire->libelle_inventaire = $data['libelle_inventaire'];
                $inventaire->date_inventaire = Carbon::createFromFormat('d-m-Y', $data['date_inventaire']);
                $inventaire->updated_by = Auth::user()->id;
                $inventaire->save();

                $jsonData["data"] = json_decode($inventaire);
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
     * @param  \App\Inventaire  $inventaire
     * @return Response
     */
    public function destroy($id)
    {
        $inventaire = Inventaire::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($inventaire) {
            try {

                //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                $detailInventaires = DetailInventaire::where('inventaire_id', $inventaire->id)->get();

                foreach ($detailInventaires as $detailInventaire) {
                    $depot = DepotArticle::where([['article_id', $detailInventaire->article_id], ['depot_id', $inventaire->depot_id]])->first();
                    $depot->quantite_disponible = $detailInventaire->quantite_en_stocke;
                    $depot->save();
                }

                $inventaire->update(['deleted_by' => Auth::user()->id]);
                $inventaire->delete();
                $jsonData["data"] = json_decode($inventaire);
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
    public function ficheInventairePdf($inventaire)
    {
        // Chargement des données
        $data = $this->ficheInventaire($inventaire);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['details_inventaire']);
        $data['qteTotalStock'] = 0;
        $data['qteTotalDenombree'] = 0;
        $index = 1;
        // Calcul du montant total
        foreach ($data['details_inventaire'] as $article) {
            $data['qteTotalStock'] += $article['quantite_en_stocke'];
            $data['qteTotalDenombree'] += $article['quantite_denombree'];
            $article['index'] = $index++;
        }
        $data['ecartTotal'] = $data['qteTotalStock'] - $data['qteTotalDenombree'];
        // Affichage
        //return $data;
        return view('crm.etats.etat-inventaire', $data);
    }


    public function ficheInventaire($inventaire)
    {
        $infos_inventaire = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where([['inventaires.deleted_at', null], ['inventaires.id', $inventaire]])
            ->orderBy('inventaires.id', 'DESC')
            ->first();
        $details_inventaire = DetailInventaire::with('article', 'unite')
            ->select('detail_inventaires.*')
            ->Where([['detail_inventaires.deleted_at', null], ['detail_inventaires.inventaire_id', $inventaire]])
            ->get();
        return [
            'infos_inventaire' => $infos_inventaire,
            'details_inventaire' => $details_inventaire,
            'title' => $infos_inventaire->libelle_inventaire . '-pdf',
        ];
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
