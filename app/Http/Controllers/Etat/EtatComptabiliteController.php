<?php

namespace App\Http\Controllers\Etat;

use App\Models\Vente\Vente;
use Illuminate\Support\Carbon;
use App\Models\Vente\Reglement;
use App\Models\Stock\ArticleBon;
use App\Models\Stock\BonCommande;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
include_once(app_path() . "/number-to-letters/nombre_en_lettre.php");

class EtatComptabiliteController extends Controller
{
    public function vuSoldeClient()
    {
        $clients = DB::table('clients')->Where('deleted_at', null)->orderBy('full_name_client', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Solde des clients";
        $btnModalAjout = "FALSE";
        return view('comptabilite.solde-client', compact('clients', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuSoldeFournisseur()
    {
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Solde des fournisseurs";
        $btnModalAjout = "FALSE";
        return view('comptabilite.solde-fournisseur', compact('fournisseurs', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuMargeVente()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Marge sur vente du jour";
        $btnModalAjout = "FALSE";
        return view('comptabilite.marge-vente', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }


    //PDF

    //Client
    public function listeSoldeClientPdf(){
        $ventes = Vente::with('client')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                        ->where([['ventes.deleted_at', NULL],['ventes.client_id','!=', NULL],['ventes.proformat',0]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('article_ventes.vente_id','DESC')
                        ->get();
        //return $ventes;
        // Tableau de données
        $data = [
            'ventes' => $ventes,
            //'userEdit' => $userEdit,
            'title' => 'solde-client-' . $ventes[0]->numero_facture . '-pdf',
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['ventes']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['ventes'] as $vente) {
            $vente['index'] = $index++;
        }
        //Si c'est une facture proforma
        // Affichage
        return $data;
        return view('crm.etats.liste-soldes-clients', $data);
        /*tu peux trouver la requette dans ClientController pour plus de détails*/
    }

    public function listeSoldeByClientPdf($client){
        $ventes = Vente::with('client')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                        ->where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.proformat',0]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('article_ventes.vente_id','DESC')
                        ->get();
        return $ventes ." N° ".$client;
         /*tu peux trouver la requette dans ClientController pour plus de détails*/
    }

    public function listeFactureClientPdf($client){
        $factures = Vente::with('client','depot')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                            ->groupBy('article_ventes.vente_id')
                            ->get();

        return $factures.'N° '.$client;
         /*tu peux trouver la requette dans ClientController pour plus de détails*/
    }

    public function listeRglementClientPdf($client){
        $reglements = Reglement::with('moyen_reglement','vente')
                                ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                ->select('reglements.*', 'ventes.client_id as id_client','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                ->Where([['reglements.deleted_at', null], ['clients.id',$client]])
                                ->orderBy('reglements.id', 'DESC')
                                ->get();
        return $reglements.' N° '.$client;
                /*tu peux trouver la requette dans ReglementController pour plus de détails*/

    }

    public function listeArticleAcheteByClientPdf($client){
        $achats = Vente::where([['ventes.deleted_at',null],['ventes.client_id',$client],['ventes.proformat',0]])
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('articles.libelle_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('sum(article_ventes.quantite) as qteTotale'))
                        ->groupBy('article_ventes.article_id')
                        ->orderBy('qteTotale','DESC')
                        ->get();

        return $achats.'N° '.$client;
    }

    //Fournisseur
    public function listeSoldeFournisseurPdf(){
        $bon_commandes = BonCommande::with('fournisseur')
                         ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                         ->select('bon_commandes.*','bon_commandes.accompte',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                         ->Where([['bon_commandes.deleted_at', NULL],['etat',5]])
                         ->groupBy('bon_commandes.id')
                         ->get();
        return  $bon_commandes;
        /*tu peux trouver la requette dans FournisseurController pour plus de détails*/
    }

    public function listeSoldeByFournisseurPdf($fournisseur){
        $bon_commandes = BonCommande::with('fournisseur')
                         ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                         ->select('bon_commandes.*','bon_commandes.accompte',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                         ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.fournisseur_id',$fournisseur],['etat',5]])
                         ->groupBy('bon_commandes.id')
                         ->get();
        return  $bon_commandes.' N° '.$fournisseur;
        /*tu peux trouver la requette dans FournisseurController pour plus de détails*/
    }

    public function listeBonFournisseurPdf($fournisseur){
        $bon_commandes =  BonCommande::with('fournisseur')
                                        ->join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
                                        ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantBonRecu'),DB::raw('sum(article_bons.quantite_demande*article_bons.prix_article) as montantBonDemande'),DB::raw('DATE_FORMAT(bon_commandes.date_bon, "%d-%m-%Y") as date_bons'))
                                        ->where([['bon_commandes.deleted_at', null],['bon_commandes.fournisseur_id', $fournisseur]])
                                        ->orderBy('bon_commandes.date_bon', 'DESC')
                                        ->groupBy('bon_commandes.id')
                                        ->get();
        return  $bon_commandes.' N° '.$fournisseur;
        /*tu peux trouver la requette dans FournisseurController pour plus de détails*/
    }

    public function listeRglementFournisseurPdf($fournisseur){
        $reglements = Reglement::with('moyen_reglement','bon_commande')
                                        ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                        ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                        ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                        ->Where([['reglements.deleted_at', null], ['fournisseurs.id',$fournisseur]])
                                        ->orderBy('reglements.id', 'DESC')
                                        ->get();
        /*tu peux trouver la requette dans ReglementController pour plus de détails*/
        return $reglements.'N° '.$fournisseur;
    }

    public function listeArticleCommandeByFournisseurPdf($fournisseur){
        $articles = ArticleBon::with('article')
                                ->join('articles','articles.id','=','article_bons.article_id')
                                ->join('bon_commandes','bon_commandes.id','=','article_bons.bon_commande_id')
                                ->select('articles.libelle_article','article_bons.prix_article',DB::raw('sum(article_bons.quantite_demande) as qteTotaleDem'),DB::raw('sum(article_bons.quantite_recu) as qteTotaleRec'))
                                ->Where([['article_bons.deleted_at', NULL],['bon_commandes.fournisseur_id',$fournisseur]])
                                ->groupBy('articles.id')
                                ->orderBy('qteTotaleDem','DESC')
                                ->get();
        return $articles.'N° '.$fournisseur;
    }

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    //Marge sur vente
    public function listeMargeSurVentePdf(){
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente',date("Y-m-d"))
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        return $ventes;
    }
    public function listeMargeSurVenteByDepotPdf($depot){
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        return $ventes;
    }
    public function listeMargeSurVenteByPeriodePdf($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);

        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente','>=',$dateDebut)
                        ->whereDate('ventes.date_vente','<=', $dateFin)
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        return $ventes;
    }
    public function listeMargeSurVenteByPeriodeDepotPdf($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->whereDate('ventes.date_vente','>=',$dateDebut)
                        ->whereDate('ventes.date_vente','<=', $dateFin)
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        return $ventes;
    }


    //Fiscalite TVA
    public function declarationTvaPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->declarationTva());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('declaration_tva_caisse.pdf');
    }

    public function declarationTva(){
        $array = json_decode($_GET["array"]);
        $ids = [];

        foreach ($array as $indext => $arr){
           $ids[$indext] = $arr->id;
        }
        $datas = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                    ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                    ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.id as idVente','ventes.numero_ticket','param_tvas.tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','articles.libelle_article')
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                    ->whereIn('article_ventes.id', $ids)
                    ->orderBy('ventes.id','DESC')
                    ->get();

        //Requête pour regrouper les montants par tva
        $groupeByTvas = Vente::with('depot')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                                ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                                ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                ->select('param_tvas.tva','article_ventes.quantite',DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS prix_vente_ttc'))
                                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
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

        //Enregistrement des declarations dans la base
        $declaration = new \App\Models\Comptabilite\TvaDeclaree;
        $declaration->date_declaration = now();
        $declaration->save();

        $totalTTC=0; $totalHT=0; $montantTva=0;$totalTva=0;
       foreach ($datas as $data){
            $montantTva = $data->prix_ht*$data->tva*$data->quantite;
            $totalHT = $totalHT + $data->prix_ht*$data->quantite;
            $totalTTC = $totalTTC + $data->prix_vente_ttc*$data->quantite;
            $totalTva = $totalTva + $montantTva;

            $ticket_in_tva = new \App\Models\Comptabilite\TicketInTva;
            $ticket_in_tva->ticket = $data->idArticleVente;
            $ticket_in_tva->declaration = $declaration->id;
            $ticket_in_tva->save();

            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_ht, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente_ttc, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_ht*$data->quantite, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente_ttc*$data->quantite, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($montantTva, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.($data->tva*100).' %&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente_ttc*$data->quantite, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }

        $outPut .='</table>';
        $outPut.='<br/> Total HT : <b> '.number_format($totalHT, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total TVA : <b> '.number_format($totalTva, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total TTC : <b> '.number_format($totalTTC, 0, ',', ' ').' F CFA</b><br/><br/>';

        $outPut.='Répartition des monatnt par TVA<br/>';
        foreach ($groupeByTvas as $resp){
             $outPut.='Chiffre d\'affaires TTC TVA '.($resp->tva*100).'% : <b> '.number_format(($resp->prix_vente_ttc), 0, ',', ' ').' F CFA</b><br/>';
        }
        $outPut.= $this->footer();
        return $outPut;
    }

    //Timbre fiscal
    public function timbreFiscalPdf(){
        $array = json_decode($_GET["array"]);
        $ids = [];
        foreach ($array as $indext => $arr) {
            $ids[$indext] = $arr->id;
        }
        $ventes = Vente::with('depot')
            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')
            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')->Where('article_ventes.retourne', 0)
            ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->select('ventes.*', DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'), 'ventes.id as idArticleVente', DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
            ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', null]])
            ->whereIn('ventes.id', $ids)
            ->groupBy('article_ventes.vente_id')
            ->orderBy('ventes.id', 'DESC')
            ->get();
        //Enregistrement des declarations du timbre dans la base
        $declaration = new \App\Models\Comptabilite\TimbreDeclare();
        $declaration->date_declaration = now();
        $declaration->save();
        // Calcul des montants
        $totalTTC = 0;
        $totalHT = 0;
        $totalTimbre = 0;
        $timbre = 0;
        foreach ($ventes as $vente) {
            $totalHT = $totalHT + $vente->totalHT;
            $totalTTC = $totalTTC + $vente->montantTTC;
            $vente->montantTTC > 5000 ? $totalTimbre = $totalTimbre + 100 : $totalTimbre = $totalTimbre + 0;
            $vente->montantTTC > 5000 ? $timbre = 100 : $timbre = 0;
            // Sauvegarde du ticket
            $ticket_in_tva = new \App\Models\Comptabilite\TimbreTiketDeclare();
            $ticket_in_tva->ticket = $vente->idArticleVente;
            $ticket_in_tva->declaration = $declaration->id;
            $ticket_in_tva->save();

        }

        // Tableau de données
        $data = [
            'ventes' => $ventes,
            'totalHT' => $totalHT,
            'totalTTC' => $totalTTC,
            'totalTimbre' => $totalTimbre,
            'timbre' => $timbre,
            //'userEdit' => $userEdit,
            //'title' => $infosDevis->numero_devis . '-pdf',
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['ventes']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['ventes'] as $ventes) {
            $ventes['index'] = $index++;
        }
        //Si c'est une facture proforma
        // Affichage
        //return $data;
        return view('crm.etats.timbre-fiscal', $data);
    }

    //Header and footer des pdf
    public function header(){
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
                <img src='.$this->infosConfig()->logo.' width="200" height="160"/>
            </p>
            </header>';
            return $header;
    }
    public function footer(){
        $footer ="<div class='fixed-footer'>
                            <div class='page-number'></div>
                        </div>
                        <div class='fixed-footer-right'>
                         <i> Editer le ".date('d-m-Y')."</i>
                        </div>
                </body>
            </html>";
        return $footer;
    }
}
