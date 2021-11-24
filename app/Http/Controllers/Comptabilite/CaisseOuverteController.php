<?php

namespace App\Http\Controllers\Comptabilite;

use Exception;
use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Vente\Benefice;
use Illuminate\Support\Carbon;
use App\Models\Vente\Billetage;
use App\Models\Parametre\Caisse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Comptabilite\CaisseOuverte;


class CaisseOuverteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

    }

    public function getCaisseOuverteByCaisse($caisse){

        $caisse_ouverte =  Vente::join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                                ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                ->select('caisse_ouvertes.*','ventes.montant_carte_fidelite',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.caisse_id',$caisse]])
                                ->groupBy('caisse_ouvertes.id')
                                ->get();
       $jsonData["rows"] = $caisse_ouverte->toArray();
       $jsonData["total"] = $caisse_ouverte->count();
       return response()->json($jsonData);
    }

    public function listeCaisseCloturee(){

        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                            ->whereDate('caisse_ouvertes.date_ouverture',date("Y-m-d"))
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }
    public function listeCaisseClotureeByDepot($depot){
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }
    public function listeCaisseClotureeByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                            ->whereDate('caisse_ouvertes.date_ouverture','>=',$dateDebut)
                            ->whereDate('caisse_ouvertes.date_ouverture','<=',$dateFin)
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }
    public function listeCaisseClotureeByPeriodeDepot($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                            ->whereDate('caisse_ouvertes.date_ouverture','>=',$dateDebut)
                            ->whereDate('caisse_ouvertes.date_ouverture','<=',$dateFin)
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }


    public function ouvertureCaisse(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('caisse_id')) {
            $data = $request->all();
            try {
                //Si la caisse est déjà ouverte ou n'existe pas
                $Caisse = Caisse::find($data['caisse_id']);
                if ($Caisse->ouvert == 1 or !$Caisse) {
                    return response()->json(["code" => 0, "msg" => "Cette caisse est déjà ouverte ou n'existe pas", "data" => null]);
                }
                //Si la personne à déjà fait une ouverture de caisse sans la fermer
                $caisse_ouverte_sans_fermee = CaisseOuverte::where([['caisse_id', $data['caisse_id']], ['user_id', Auth::user()->id], ['date_fermeture', null]])->first();
                if ($caisse_ouverte_sans_fermee != null) {
                    return response()->json(["code" => 0, "msg" => "Vous avez une session ouverte sur cette caisse.", "data" => null]);
                }

                //Mise à jour
                $Caisse->ouvert = TRUE;
                $Caisse->updated_by = Auth::user()->id;
                $Caisse->save();

                $caisseOuverte = new CaisseOuverte;
                $caisseOuverte->montant_ouverture = $data['montant_ouverture'];
                $caisseOuverte->date_ouverture = now();
                $caisseOuverte->caisse_id = $data['caisse_id'];
                $caisseOuverte->user_id = Auth::user()->id;
                $caisseOuverte->created_by = Auth::user()->id;
                $caisseOuverte->save();

                //Stockage en session
                $request->session()->put('session_caisse_ouverte', $caisseOuverte->id);

                $jsonData["data"] = json_decode($caisseOuverte);
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


    public function fermetureCaisse(Request $request)
    {

        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $caisseOuverte = CaisseOuverte::where([['caisse_id', $request->caisses_fermeture], ['date_fermeture', null]])->first();
        if ($caisseOuverte) {
            $ventes = Vente::where('caisse_ouverte_id',$caisseOuverte->id)->get();
            try {
                //On récupere la caisse pour fermer
                $caisse = Caisse::find($request->caisses_fermeture);

                if ($caisse->ouvert == 0 or !$caisse) {
                    return response()->json(["code" => 0, "msg" => "Cette caisse est déjà fermée ou n'existe pas", "data" => null]);
                }
                $data = $request->all();
                //Controle du billetage
                if (empty($data["panierBillet"])) {
                    return response()->json(["code" => 0, "msg" => "Veillez remplir le billetage svp!", "data" => null]);
                }
                //Recuperation du montant total du billetage
                $billetageContent = is_array($data["panierBillet"]) ? $data["panierBillet"] : array($data["panierBillet"]);
                $montantBilletage = 0;
                foreach ($billetageContent as $index => $billetage) {
                    $montantBilletage = $montantBilletage + $data["panierBillet"][$index]["billets"] * $data["panierBillet"][$index]["quantite_billets"];
                }
                if($montantBilletage != $request->get('solde_fermeture') && empty($data["motif_non_conformite"])) {
                    return response()->json(["code" => 0, "msg" => "Le montant du billetage ne correspond pas au solde de la caisse.", "data" => null]);
                }

                //Si tout se passe bien
                foreach ($billetageContent as $index => $billetage) {
                    $Billetage = new Billetage;
                    $Billetage->billet = $data["panierBillet"][$index]["billets"];
                    $Billetage->quantite = $data["panierBillet"][$index]["quantite_billets"];
                    $Billetage->caisse_ouverte_id = $caisseOuverte->id;
                    $Billetage->created_by = Auth::user()->id;
                    $Billetage->save();
                }
                //Mise à jour
                $caisse->ouvert = FALSE;
                $caisse->updated_by = Auth::user()->id;
                $caisse->save();

                //Mise à jour caisse ouverte
                $caisseOuverte->solde_fermeture = $request->get('solde_fermeture');
                $caisseOuverte->date_fermeture = now();
                $caisseOuverte->updated_by = Auth::user()->id;
                $caisseOuverte->save();

                //Destruction de la session de caisse ouverte
                if ($request->session()->has('session_caisse_ouverte')) {
                    $request->session()->forget('session_caisse_ouverte');
                }

                //Calcule du bénéfice de la caisse
                $benefice = 0;
                $ventes = Vente::join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')
                                ->where('article_ventes.retourne', 0)
                                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                ->select('ventes.*', DB::raw('sum((articles.prix_achat_ttc*article_ventes.quantite)-(article_ventes.quantite*article_ventes.prix)) as Benefice'))
                                ->Where([['ventes.caisse_ouverte_id', $caisseOuverte->id], ['ventes.deleted_at', null]])
                                ->groupBy('article_ventes.vente_id')
                                ->get();
                foreach ($ventes as $vente) {
                    $benefice = $benefice + $vente->Benefice;
                }

                $saveBenefice = new Benefice;
                $saveBenefice->caisse_ouvert_id = $caisseOuverte->id;
                $saveBenefice->montant = $benefice;
                $saveBenefice->save();

                $jsonData["data"] = json_decode($caisseOuverte);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Echec de fermeture", "data" => null]);
    }

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    //Etat
    public function listeCaisseClotureePdf(){
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                            ->whereDate('caisse_ouvertes.date_ouverture',date("Y-m-d"))
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        return $caisses;
    }
    public function listeCaisseClotureeByDepotPdf($depot){
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        return $caisses;
    }
    public function listeCaisseClotureeByPeriodePdf($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                            ->whereDate('caisse_ouvertes.date_ouverture','>=',$dateDebut)
                            ->whereDate('caisse_ouvertes.date_ouverture','<=',$dateFin)
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        return $caisses;
    }
    public function listeCaisseClotureeByPeriodeDepotPdf($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $caisses = Vente::with('depot','caisse_ouverte')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                            ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                            ->join('depots','depots.id','=','ventes.depot_id')
                            ->join('users','users.id','=','caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                            ->whereDate('caisse_ouvertes.date_ouverture','>=',$dateDebut)
                            ->whereDate('caisse_ouvertes.date_ouverture','<=',$dateFin)
                            ->groupBy('caisse_ouvertes.id')
                            ->get();
        return $caisses;
    }
}
