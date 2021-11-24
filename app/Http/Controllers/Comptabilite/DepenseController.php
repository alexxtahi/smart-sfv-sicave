<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\Depense;
use App\Models\Parametre\CategorieDepense;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $categorie_depenses = DB::table('categorie_depenses')->Where('deleted_at', null)->orderBy('libelle_categorie_depense', 'asc')->get();
        $menuPrincipal = "Comptabilité";
        $titleControlleur = "Dépenses";
        $btnModalAjout = "TRUE";
        return view('comptabilite.depenses', compact('categorie_depenses', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeDepense(int $categorie = null)
    {
        if($categorie){
            $depenses = Depense::with('categorie_depense')
                            ->select('depenses.*', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
                            ->Where([['depenses.deleted_at', null], ['depenses.categorie_depense_id', $categorie]])
                            ->orderBy('depenses.id', 'DESC')
                            ->get();
        }else{
            $depenses = Depense::with('categorie_depense')
                            ->select('depenses.*', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
                            ->Where('depenses.deleted_at', null)
                            ->whereMonth('depenses.date_operation', '=', date("m"))
                            ->orderBy('depenses.id', 'DESC')
                            ->get();
        }

        $jsonData["rows"] = $depenses->toArray();
        $jsonData["total"] = $depenses->count();
        return response()->json($jsonData);
    }

    public function listeDepenseByPeriode($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $depenses = Depense::with('categorie_depense')
                                ->select('depenses.*', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
                                ->Where('depenses.deleted_at', null)
                                ->whereDate('depenses.date_operation', '>=', $date1)
                                ->whereDate('depenses.date_operation', '<=', $date2)
                                ->orderBy('depenses.id', 'DESC')
                                ->get();
        $jsonData["rows"] = $depenses->toArray();
        $jsonData["total"] = $depenses->count();
        return response()->json($jsonData);
    }

    public function listeDepenseByCategoriePeriode($categorie,$debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $depenses = Depense::with('categorie_depense')
                                ->select('depenses.*', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
                                ->Where([['depenses.deleted_at', null], ['depenses.categorie_depense_id', $categorie]])
                                ->whereDate('depenses.date_operation', '>=', $date1)
                                ->whereDate('depenses.date_operation', '<=', $date2)
                                ->orderBy('depenses.id', 'DESC')
                                ->get();
        $jsonData["rows"] = $depenses->toArray();
        $jsonData["total"] = $depenses->count();
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
        if ($request->isMethod('post') && $request->input('date_operation')) {

            $data = $request->all();

            try {

                $depense = $data['id'] ? Depense::findOrFail($data['id']) : new Depense;

                $depense->categorie_depense_id = $data['categorie_depense_id'];
                $depense->date_operation = Carbon::createFromFormat('d-m-Y', $data['date_operation']);
                $depense->description = isset($data['description']) && !empty($data['description']) ? $data['description'] : null;
                $depense->montant_depense =  $data['montant_depense'];
                $depense->created_by = Auth::user()->id;
                $depense->save();

                $jsonData["data"] = json_decode($depense);
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
     * @param  \App\Depense  $depense
     * @return Response
     */
    public function destroy(Depense $depense)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($depense) {
            try {

                $depense->update(['deleted_by' => Auth::user()->id]);
                $depense->delete();
                $jsonData["data"] = json_decode($depense);
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

    //Liste des dépenses
    public function listeDepensePdf()
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeDepenses());
        return $pdf->stream('liste_depenses.pdf');
    }
    public function listeDepenses()
    {
        $datas = Depense::where('depenses.deleted_at', null)
            ->join('categorie_depenses', 'categorie_depenses.id', '=', 'depenses.categorie_depense_id')
            ->select('depenses.*', 'categorie_depenses.libelle_categorie_depense', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
            ->whereMonth('depenses.date_operation', '=', date("m"))
            ->orderBy('depenses.date_operation', 'DESC')
            ->get();

        $month = ['01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril', '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Decembre'];

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des dépenses ' . $month[date("m")] . ' ' . date("Y") . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Libellé</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Description</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>';
        $montantTotal = 0;
        foreach ($datas as $data) {
            $montantTotal = $montantTotal + $data->montant_depense;
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->date_operations . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->libelle_categorie_depense . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->description . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->montant_depense, 0, ',', ' ') . '&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .= '</table><br/><br/>Montant total : <b>' . $montantTotal . ' F CFA</b></div>';
        $outPut .= $this->footer();
        return $outPut;
    }


    //Liste des dépenses par catégorie
    public function listeDepenseByCategoriePdf($categorie)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeDepenseByCategories($categorie));
        $info_categorie_dep = CategorieDepense::find($categorie);
        return $pdf->stream('liste_depenses_dans_' . $info_categorie_dep->libelle_categorie_depense . '.pdf');
    }
    public function listeDepenseByCategories($categorie)
    {

        $info_categorie_dep = CategorieDepense::find($categorie);
        $datas = Depense::where([['depenses.deleted_at', null], ['depenses.categorie_depense_id', $categorie]])
            ->select('depenses.*', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
            ->orderBy('depenses.date_operation', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des dépenses dans ' . $info_categorie_dep->libelle_categorie_depense . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="45%" align="center">Description</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Montant</th>
                        </tr>';
        $montantTotal = 0;
        foreach ($datas as $data) {
            $montantTotal = $montantTotal + $data->montant_depense;
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->date_operations . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->description . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->montant_depense, 0, ',', ' ') . '&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .= '</table><br/><br/>Montant total : <b>' . number_format($montantTotal, 0, ',', ' ') . ' F CFA</b></div>';
        $outPut .= $this->footer();
        return $outPut;
    }


    //Liste des dépenses par période
    public function listeDepenseByPeriodePdf($debut, $fin)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeDepenseByPeriodes($debut, $fin));
        return $pdf->stream('liste_depenses_du_' . $debut . '_au_' . $fin . '.pdf');
    }
    public function listeDepenseByPeriodes($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Depense::where('depenses.deleted_at', null)
            ->join('categorie_depenses', 'categorie_depenses.id', '=', 'depenses.categorie_depense_id')
            ->select('depenses.*', 'categorie_depenses.libelle_categorie_depense', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
            ->whereDate('depenses.date_operation', '>=', $date1)
            ->whereDate('depenses.date_operation', '<=', $date2)
            ->orderBy('depenses.date_operation', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des dépenses du ' . $debut . ' au ' . $fin . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Libellé</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Description</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>';
        $montantTotal = 0;
        foreach ($datas as $data) {
            $montantTotal = $montantTotal + $data->montant_depense;
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->date_operations . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->libelle_categorie_depense . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->description . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->montant_depense, 0, ',', ' ') . '&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .= '</table><br/><br/>Montant total : <b>' . number_format($montantTotal, 0, ',', ' ') . ' F CFA</b></div>';
        $outPut .= $this->footer();
        return $outPut;
    }


    //Liste des dépenses par période pour une catégorie
    public function listeDepenseByPeriodeCategoriePdf($debut, $fin, $categorie)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeDepenseByPeriodeCategories($debut, $fin, $categorie));
        $info_categorie_dep = CategorieDepense::find($categorie);
        return $pdf->stream('liste_depenses_du_' . $debut . '_au_' . $fin . '_dans_' . $info_categorie_dep->libelle_categorie_depense . '.pdf');
    }
    public function listeDepenseByPeriodeCategories($debut, $fin, $categorie)
    {
        $info_categorie_dep = CategorieDepense::find($categorie);
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Depense::where('depenses.deleted_at', null)
            ->select('depenses.*', DB::raw('DATE_FORMAT(depenses.date_operation, "%d-%m-%Y") as date_operations'))
            ->whereDate('depenses.date_operation', '>=', $date1)
            ->whereDate('depenses.date_operation', '<=', $date2)
            ->orderBy('depenses.date_operation', 'DESC')
            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des dépenses du ' . $debut . ' au ' . $fin . ' dans ' . $info_categorie_dep->libelle_categorie_depense . '</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Description</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>';
        $montantTotal = 0;
        foreach ($datas as $data) {
            $montantTotal = $montantTotal + $data->montant_depense;
            $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->date_operations . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;' . $data->description . '</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->montant_depense, 0, ',', ' ') . '&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .= '</table><br/><br/>Montant total : <b>' . $montantTotal . ' F CFA</b></div>';
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
