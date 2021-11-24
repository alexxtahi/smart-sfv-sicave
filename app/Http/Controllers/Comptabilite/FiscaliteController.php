<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Stock\BonCommande;
use App\Models\Vente\Vente;
use App\Models\Crm\Client;
use App\Models\Crm\Fournisseur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FiscaliteController extends Controller
{

    public function vuDeclarationFiscal()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Comptabilité";
        $titleControlleur = "Déclaration fiscale sur les ventes du jour";
        $btnModalAjout = "FALSE";
        return view('comptabilite.declaration-fiscal', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuTimbreFiscal()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Comptabilité";
        $titleControlleur = "Timbre fiscal sur les ventes du jour";
        $btnModalAjout = "FALSE";
        return view('comptabilite.timbre-fiscal', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuTicketDeclareTva(){
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();

        $menuPrincipal = "Comptabilité";
        $titleControlleur = "Liste des déclarations TVA";
        $btnModalAjout = "FALSE";
        return view('comptabilite.ticket-declare-tva',compact('depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuTicketDeclareTimbre(){
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();

        $menuPrincipal = "Comptabilité";
        $titleControlleur = "Liste des déclarations de timbre";
        $btnModalAjout = "FALSE";
        return view('comptabilite.ticket-declare-timbre',compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    /** Liste **/

    //Déclaration fiscale
    public function listeDeclarationFiscale(){

        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.numero_ticket','param_tvas.tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','articles.libelle_article')
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente',date("Y-m-d"))
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function listeDeclarationFiscaleByDepot($depot){
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.numero_ticket','param_tvas.tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','articles.libelle_article')
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function listeDeclarationFiscaleByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.numero_ticket','param_tvas.tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','articles.libelle_article')
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente','>=',$dateDebut)
                        ->whereDate('ventes.date_vente','<=', $dateFin)
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function listeDeclarationFiscaleByDepotPeriode($depot,$debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.numero_ticket','param_tvas.tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','articles.libelle_article')
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->whereDate('ventes.date_vente','>=',$dateDebut)
                        ->whereDate('ventes.date_vente','<=', $dateFin)
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    //Timbre fiscal
    public function listeDeclarationTimbre(){
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente',date("Y-m-d"))
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();

        return response()->json($jsonData);
    }

    public function listeDeclarationTimbreByDepot($depot){
        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();

        return response()->json($jsonData);
    }

    public function listeDeclarationTimbreByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);

        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                        ->whereDate('ventes.date_vente','>=',$dateDebut)
                        ->whereDate('ventes.date_vente','<=', $dateFin)
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();

        return response()->json($jsonData);
    }

    public function listeDeclarationTimbreByDepotPeriode($depot,$debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);

        $ventes = Vente::with('depot')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                        ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                        ->whereDate('ventes.date_vente','>=',$dateDebut)
                        ->whereDate('ventes.date_vente','<=', $dateFin)
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();

        return response()->json($jsonData);
    }
}
