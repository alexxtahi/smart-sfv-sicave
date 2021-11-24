<?php

namespace App\Http\Controllers\Comptabilite;

use App\Models\Vente\Vente;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Comptabilite\TimbreDeclare;
use App\Models\Comptabilite\TimbreTiketDeclare;

class TimbreTiketDeclareController extends Controller
{
    public function listeTimbreTiketDeclare($declaration)
    {
        $array = TimbreTiketDeclare::where('timbre_tiket_declares.declaration', $declaration)->get();

        $ids = [];
        foreach ($array as $indext => $arr) {
            $ids[$indext] = $arr->ticket;
        }

        $ventes = Vente::with('depot')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                            ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                            ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                            ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                            ->whereIn('ventes.id', $ids)
                            ->groupBy('article_ventes.vente_id')
                            ->orderBy('ventes.id','DESC')
                            ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    public function timbreDeclarePdf($declaration){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->timbreDeclare($declaration));
        $pdf->setPaper('A4');
        return $pdf->stream('timbres_declares.pdf');
    }

    public function timbreDeclare($declaration){

        $array = TimbreTiketDeclare::where('timbre_tiket_declares.declaration', $declaration)->get();
        $ids = [];
        foreach ($array as $indext => $arr) {
            $ids[$indext] = $arr->ticket;
        }
        $datas = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                        ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereIn('ventes.id', $ids)
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Timbre fiscal</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant HT</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Net à payer</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Timbre</th>
                        </tr>
                    </div>';


        $totalTTC=0; $totalHT=0; $totalTimbre=0; $timbre=0;
       foreach ($datas as $data){
            $totalHT = $totalHT + $data->totalHT;
            $totalTTC = $totalTTC + $data->montantTTC;
            $data->montantTTC >5000 ? $totalTimbre = $totalTimbre+100 : $totalTimbre = $totalTimbre+0;
            $data->montantTTC >5000 ? $timbre = 100 : $timbre = 0;

            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->totalHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($timbre, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }

        $outPut .='</table>';
        $outPut.='<br/> Total HT : <b> '.number_format($totalHT, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total TTC : <b> '.number_format($totalTTC, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total Timbre : <b> '.number_format($totalTimbre, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
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
