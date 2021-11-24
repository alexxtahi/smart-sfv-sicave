<?php

namespace App\Http\Controllers\Vente;

use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Vente\Billetage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BilletageController extends Controller
{

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    //Eatat
    public function billetagePdf($caisse_ouverte)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->billetageContent($caisse_ouverte));
        return $pdf->stream('billetage.pdf');
    }

    public function billetageContent($caisse_ouverte)
    {
        $info_caisse_ouverte = DB::table('caisse_ouvertes')
                                    ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                                    ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                                    ->select('caisse_ouvertes.*', 'caisses.libelle_caisse', 'users.full_name')
                                    ->Where([['caisse_ouvertes.deleted_at', null], ['caisse_ouvertes.id', $caisse_ouverte]])
                                    ->first();
        $totalCaisse = 0; $totalCarte= 0;
        $ventes_caisse = Vente::join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')
                                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', null)
                                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                ->Where([['ventes.deleted_at', null], ['ventes.client_id', null], ['ventes.caisse_ouverte_id', $caisse_ouverte]])
                                ->groupBy('article_ventes.vente_id')
                                ->get();

        foreach ($ventes_caisse as $vente) {
            $totalCaisse = $totalCaisse + $vente->montantTTC;
            $totalCarte = $totalCarte + $vente->numero_carte_fidelite;
        }
        $datas = Billetage::where('caisse_ouverte_id', $caisse_ouverte)->orderBy('billet', 'desc')->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Resumé de caisse après vente</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <td cellspacing="0" border="2" width="40%" align="letf">
                                Date : <b>' . date("d-m-Y") . '</b><br/>
                                Caisse : <b>' . $info_caisse_ouverte->libelle_caisse . '</b><br/>
                                Caissier(e) <b>: <b>' . $info_caisse_ouverte->full_name . '</b>
                            </td>
                            <td cellspacing="0" border="2" width="35%" align="letf">
                                Ouverture : <b>' . date('d-m-Y H:i', strtotime($info_caisse_ouverte->date_ouverture)) . '</b><br/>
                                Fermeture : <b>' . date('d-m-Y H:i', strtotime($info_caisse_ouverte->date_fermeture)) . '</b><br/>
                                Solde <b>: <b>' . number_format($info_caisse_ouverte->solde_fermeture, 0, ',', ' ') . '</b>
                            </td>
                            <td cellspacing="0" border="2" width="25%" align="left">
                                Ouverture : <b>' . number_format($info_caisse_ouverte->montant_ouverture, 0, ',', ' ') . '</b><br/>
                                Entrée : <b>' . number_format($totalCaisse + $info_caisse_ouverte->entree, 0, ',', ' ') . '</b><br/>
                                Sortie : <b>' . number_format($info_caisse_ouverte->sortie, 0, ',', ' ') . '</b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center"> <b>Billetage</b> </td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Nombre</th>
                            <th cellspacing="0" border="2" width="50%" align="center">Billet</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Montant</th>
                        </tr>
                    </div>';
        $montantTotal = 0;
        foreach ($datas as $data) {
            $montantTotal = $montantTotal + $data->billet * $data->quantite;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">' . $data->quantite . '</td>
                            <td  cellspacing="0" border="2" align="center">' . $data->billet . '</td>
                            <td  cellspacing="0" border="2" align="center">' . number_format($data->billet * $data->quantite, 0, ',', ' ') . '</td>
                        </tr>';
        }
        $outPut .= '</table>';

        $info_caisse_ouverte->motif_non_conformite != null ? $motif_non_conformite = 'Motif : ' . $info_caisse_ouverte->motif_non_conformite : $motif_non_conformite = null;

        $ventes_by_categories = Vente::join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                        ->join('articles','article_ventes.article_id','=','articles.id')
                                        ->join('categories','categories.id','=','articles.categorie_id')
                                        ->select('ventes.*','categories.libelle_categorie',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.caisse_ouverte_id',$caisse_ouverte]])
                                        ->groupBy('categories.id')
                                        ->get();

        $ventes_by_moyen_reglements = Vente::join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')
                                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                            ->join('moyen_reglements','moyen_reglements.id','=','ventes.moyen_reglement_id')
                                            ->select('ventes.*','moyen_reglements.libelle_moyen_reglement',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.caisse_ouverte_id',$caisse_ouverte]])
                                            ->groupBy('moyen_reglements.id')
                                            ->get();

        $outPut .='<br/>Montant par catégories<br/>';
        foreach($ventes_by_categories as $categorie){
            $outPut.= $categorie->libelle_categorie.' : &nbsp;'.number_format($categorie->montantTTC, 0, ',', ' ').'<br/>';
        }

        $outPut .='<br/>Montant par moyen de règlement<br/>';
        foreach($ventes_by_moyen_reglements as $moyen_reglement){
            $outPut.= $moyen_reglement->libelle_moyen_reglement.' : &nbsp;'.number_format($moyen_reglement->montantTTC, 0, ',', ' ').'<br/>';
        }

        $outPut .= '<br/> Montant total : <b> ' .number_format($montantTotal, 0, ',', ' '). ' F CFA</b><br/>' . $motif_non_conformite;

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
