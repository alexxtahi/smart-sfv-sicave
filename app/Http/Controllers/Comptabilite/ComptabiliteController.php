<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Stock\BonCommande;
use App\Models\Vente\Vente;
use App\Models\Crm\Client;
use App\Models\Crm\Fournisseur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ComptabiliteController extends Controller
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

    public function vuMargeSurVente()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Marge sur vente du jour";
        $btnModalAjout = "FALSE";
        return view('comptabilite.marge-sur-vente', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuPointCaisseCloture()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Les points de caisse cloturés du jour";
        $btnModalAjout = "FALSE";
        return view('comptabilite.point-caisse-cloture', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    /** Liste **/

    //Ventes
    public function listeMargeSurVente(){
        $totalTTC=0; $totalAchat=0;
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente',date("Y-m-d"))
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
    public function listeMargeSurVenteByDepot($depot){
        $totalTTC=0; $totalAchat=0;
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
    public function listeMargeSurVenteByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $totalTTC=0; $totalAchat=0;
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
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
    public function listeMargeSurVenteByPeriodeDepot($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $totalTTC=0; $totalAchat=0;
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
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
}
