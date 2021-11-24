<?php

namespace App\Http\Controllers\Comptabilite;

use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Comptabilite\TicketInTva;
use App\Models\Comptabilite\TvaDeclaree;

class TicketInTvaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listeTicketDeclare($declaration)
    {
        $array = TicketInTva::where('ticket_in_tvas.declaration', $declaration)->get();

        $ids = [];
        foreach ($array as $indext => $arr) {
            $ids[$indext] = $arr->ticket;
        }

        $ventes = Vente::with('depot')
                        ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->where('article_ventes.retourne', 0)
                        ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                        ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                        ->select('ventes.numero_ticket', 'param_tvas.tva', 'article_ventes.id as idArticleVente', 'article_ventes.quantite', DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'), 'article_ventes.prix as prix_vente_ttc', 'articles.libelle_article')
                        ->whereIn('article_ventes.id', $ids)
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

    public function ticketDeclarePdf($declaration)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->ticketDeclare($declaration));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('tickets_declares.pdf');
    }

    public function ticketDeclare($declaration)
    {

        $array = TicketInTva::where('ticket_in_tvas.declaration', $declaration)->get();

        $ids = [];
        foreach ($array as $indext => $arr) {
            $ids[$indext] = $arr->ticket;
        }

        $datas = Vente::with('depot')
            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->where('article_ventes.retourne', 0)
            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
            ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->select('ventes.numero_ticket', 'param_tvas.tva', 'article_ventes.id as idArticleVente', 'article_ventes.quantite', DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'), 'article_ventes.prix as prix_vente_ttc','articles.libelle_article')
            ->whereIn('article_ventes.id', $ids)
            ->get();

        //Requête pour regrouper les montants par tva
        $groupeByTvas = Vente::with('depot')
                            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->where('article_ventes.retourne', 0)
                            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                            ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                            ->select('param_tvas.tva', 'article_ventes.quantite', DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS prix_vente_ttc'))
                            ->Where([['ventes.deleted_at', null],['ventes.client_id', null]])
                            ->whereIn('article_ventes.id', $ids)
                            ->groupBy('param_tvas.id')
                            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Déclaration  TVA</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté / Btle</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix HT</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix TTC</th>
                            <th cellspacing="0" border="2" width="18%" align="center">Montant HT</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Mont. TVA</th>
                            <th cellspacing="0" border="2" width="5%" align="center">TVA</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant Net</th>
                        </tr>
                    </div>';

        $totalTTC = 0;
        $totalHT = 0;
        $montantTva = 0;
        $totalTva = 0;
        foreach ($datas as $data) {
            $montantTva = $data->prix_ht * $data->tva * $data->quantite;
            $totalHT = $totalHT + $data->prix_ht * $data->quantite;
            $totalTTC = $totalTTC + $data->prix_vente_ttc * $data->quantite;
            $totalTva = $totalTva + $montantTva;

            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->numero_ticket . '</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;' . $data->libelle_article . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_ht, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente_ttc, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_ht * $data->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente_ttc * $data->quantite, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($montantTva, 0, ',', ' ') . '&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . ($data->tva * 100) . ' %&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">' . number_format($data->prix_vente_ttc * $data->quantite, 0, ',', ' ') . '&nbsp;</td>
                        </tr>';
        }

        $outPut .= '</table>';
        $outPut .= '<br/> Total HT : <b> ' . number_format($totalHT, 0, ',', ' ') . ' F CFA</b><br/>';
        $outPut .= 'Total TVA : <b> ' . number_format($totalTva, 0, ',', ' ') . ' F CFA</b><br/>';
        $outPut .= 'Total TTC : <b> ' . number_format($totalTTC, 0, ',', ' ') . ' F CFA</b><br/><br/>';

        $outPut .= 'Répartition des monatnt par TVA<br/>';
        foreach ($groupeByTvas as $resp) {
            $outPut .= 'Chiffre d\'affaires TTC TVA ' . ($resp->montant_tva * 100) . '% : <b> ' . number_format(($resp->prix_vente_ttc - $resp->montant_remise), 0, ',', ' ') . ' F CFA</b><br/>';
        }
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
