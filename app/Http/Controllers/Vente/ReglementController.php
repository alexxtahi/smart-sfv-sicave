<?php

namespace App\Http\Controllers\Vente;

use Exception;
use App\Models\Crm\Compte;
use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\Vente\AvoirDoit;
use App\Models\Vente\Reglement;
use App\Models\Stock\BonCommande;
use Illuminate\Support\Facades\DB;
use App\Models\Crm\MouvementCompte;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Parametre\CarteFidelite;
use App\Models\Parametre\MoyenReglement;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ReglementController extends Controller
{

    public function reglementClient()
    {
        $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', null)->orderBy('libelle_moyen_reglement', 'asc')->get();
        $clients = DB::table('clients')->Where('deleted_at', null)->orderBy('full_name_client', 'asc')->get();

        $menuPrincipal = "Vente";
        $titleControlleur = "Règlement des clients";
        $btnModalAjout = "TRUE";
        return view('vente.reglement.client', compact('moyenReglements', 'clients', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function reglementFournisseur()
    {
        $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', null)->orderBy('libelle_moyen_reglement', 'asc')->get();
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();

        $menuPrincipal = "Vente";
        $titleControlleur = "Règlement des fournisseurs";
        $btnModalAjout = "TRUE";
        return view('vente.reglement.fournisseur', compact('moyenReglements', 'fournisseurs', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeReglement($type){
        if($type == 'client'){
            $reglements = Reglement::with('moyen_reglement','vente')
                                ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                ->select('reglements.*','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                ->Where([['reglements.deleted_at', null], ['reglements.vente_id', '!=', null]])
                                ->orderBy('reglements.id', 'DESC')
                                ->get();
        }else{
            $reglements = Reglement::with('moyen_reglement','bon_commande')
                                    ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                    ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                    ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                    ->Where([['reglements.deleted_at', null], ['reglements.bon_commande_id', '!=', null]])
                                    ->orderBy('reglements.id', 'DESC')
                                    ->get();
        }

        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }

    public function listeReglementByDate($type,$dates){
        $date = Carbon::createFromFormat('d-m-Y', $dates);

        if($type == 'client'){
            $reglements = Reglement::with('moyen_reglement','vente')
                                ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                ->select('reglements.*','clients.full_name_client','ventes.numero_facture', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                ->Where([['reglements.deleted_at', null], ['reglements.vente_id', '!=', null]])
                                ->whereDate('reglements.date_reglement', '<=', $date)
                                ->orderBy('reglements.id', 'DESC')
                                ->get();
        }else{
            $reglements = Reglement::with('moyen_reglement','bon_commande')
                                    ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                    ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                    ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                    ->Where([['reglements.deleted_at', null], ['reglements.bon_commande_id', '!=', null]])
                                    ->whereDate('reglements.date_reglement', '<=', $date)
                                    ->orderBy('reglements.id', 'DESC')
                                    ->get();
        }

        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }


    public function listeReglementByFacture($facture){
        $reglements = Reglement::with('moyen_reglement','vente')
                                ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                ->select('reglements.*', 'ventes.client_id as id_client','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                ->Where([['reglements.deleted_at', null], ['reglements.vente_id',$facture]])
                                ->orderBy('reglements.id', 'DESC')
                                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeReglementByClient($client){
        $reglements = Reglement::with('moyen_reglement','vente')
                                ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                ->select('reglements.*', 'ventes.client_id as id_client','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                ->Where([['reglements.deleted_at', null], ['clients.id',$client]])
                                ->orderBy('reglements.id', 'DESC')
                                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeReglementByMoyenReglementClient($moyenReglement,$client){
        if($client == "client"){
            $reglements = Reglement::with('moyen_reglement','vente')
                                    ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                    ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                    ->select('reglements.*', 'ventes.client_id as id_client','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                    ->Where([['reglements.deleted_at', null], ['reglements.vente_id','!=',null],['reglements.moyen_reglement_id',$moyenReglement]])
                                    ->orderBy('reglements.id', 'DESC')
                                    ->get();
        }else{
            $reglements = Reglement::with('moyen_reglement','vente')
                                    ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                    ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                    ->select('reglements.*', 'ventes.client_id as id_client','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                    ->Where([['reglements.deleted_at', null], ['clients.id',$client],['reglements.moyen_reglement_id',$moyenReglement]])
                                    ->orderBy('reglements.id', 'DESC')
                                    ->get();
        }

        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeReglementByBonCommande($bon_commande){
        $reglements = Reglement::with('moyen_reglement','bon_commande')
                                        ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                        ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                        ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                        ->Where([['reglements.deleted_at', null], ['reglements.bon_commande_id',$bon_commande]])
                                        ->orderBy('reglements.id', 'DESC')
                                        ->get();
            $jsonData["rows"] = $reglements->toArray();
            $jsonData["total"] = $reglements->count();
            return response()->json($jsonData);
    }
    public function listeReglementByFournisseur($fournisseur){

            $reglements = Reglement::with('moyen_reglement','bon_commande')
                                        ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                        ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                        ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                        ->Where([['reglements.deleted_at', null], ['fournisseurs.id',$fournisseur]])
                                        ->orderBy('reglements.id', 'DESC')
                                        ->get();
            $jsonData["rows"] = $reglements->toArray();
            $jsonData["total"] = $reglements->count();
            return response()->json($jsonData);
    }
    public function listeReglementByMoyenReglementFournisseur($moyenReglement,$fournisseur){
        if($fournisseur=='fournisseur'){
            $reglements = Reglement::with('moyen_reglement','bon_commande')
                                    ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                    ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                    ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                    ->Where([['reglements.deleted_at', null], ['reglements.bon_commande_id','!=',null],['reglements.moyen_reglement_id',$moyenReglement]])
                                    ->orderBy('reglements.id', 'DESC')
                                    ->get();
        }else{
            $reglements = Reglement::with('moyen_reglement','bon_commande')
                                    ->join('bon_commandes', 'bon_commandes.id', '=', 'reglements.bon_commande_id')
                                    ->join('fournisseurs', 'fournisseurs.id', '=', 'bon_commandes.fournisseur_id')
                                    ->select('reglements.*','bon_commandes.numero_bon', 'fournisseurs.full_name_fournisseur', DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                    ->Where([['reglements.deleted_at', null], ['fournisseurs.id',$fournisseur],['reglements.moyen_reglement_id',$moyenReglement]])
                                    ->orderBy('reglements.id', 'DESC')
                                    ->get();
        }

        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
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
        if ($request->isMethod('post') && $request->input('date_reglement')) {
            $data = $request->all();
            try {

                $montant_restant = 0;

                //Si le règlement concerne une facture client
                if(isset($data['vente_id']) && !empty($data['vente_id'])){
                    $vente = Vente::with('client')
                                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                    ->Where([['ventes.deleted_at', NULL],['ventes.id',$data['vente_id']]])
                                    ->groupBy('article_ventes.vente_id')
                                    ->first();

                    if(!$vente){
                        return response()->json(["code" => 0, "msg" => "Facture introuvable", "data" => null]);
                    }

                    //Si le reglement se fait par une carte de fidélité
                    if(isset($data['numero_carte_fidelite']) && !empty($data['numero_carte_fidelite'])){
                        $compteCarteFidelite = Compte::where('numero_compte',$data['numero_carte_fidelite'])->first();

                        if(!$compteCarteFidelite){
                            return response()->json(["code" => 0, "msg" => "Carte introuvable, contactez l'administrateur SVP", "data" => NULL]);
                        }

                        //Gestion de la validation du montant
                        if(empty($data['montant']) or !isset($data['montant'])){
                            return response()->json(["code" => 0, "msg" => "Le champ montant de la carte ne doit pas être vide", "data" => NULL]);
                        }

                        //Gestion du pourcentage de reduction
                        $carte = CarteFidelite::find($compteCarteFidelite->carte_id);

                        $montantApresReduction = (intval($data['montant']) - ($carte->reduction*intval($data['montant'])));

                        $soldeCarte = $compteCarteFidelite->entree - $compteCarteFidelite->sortie;

                        //Si le solde est insuffisant
                        if($soldeCarte < $montantApresReduction){
                            return response()->json(["code" => 0, "msg" => "Le solde de carte ".number_format($soldeCarte, 0, ',', ' ')." est insuffisant.", "data" => NULL]);
                        }

                        //Prélevement sur la carte
                        $compteCarteFidelite->sortie = $compteCarteFidelite->sortie + $montantApresReduction;
                        $compteCarteFidelite->save();

                        //Enregistrement du mouvement du compte de la carte
                        $mouvement = new MouvementCompte;
                        $mouvement->compte_id = $compteCarteFidelite->id;
                        $mouvement->initiale = $soldeCarte;
                        $mouvement->sortie = $montantApresReduction;
                        $mouvement->date_mouvement = now();
                        $mouvement->save();
                    }else{
                        //Montant restant
                        $montant_restant = $montant_restant + ($vente->montantTTC - ($vente->acompte_facture + $data['montant']));
                    }
                }

                //Si le règlement concerne un bon fournisseur
                if(isset($data['bon_commande_id']) && !empty($data['bon_commande_id'])){
                    $bon_commande =  BonCommande::join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
                                                    ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantBonRecu'))
                                                    ->where([['bon_commandes.deleted_at', null], ['bon_commandes.id', $data['bon_commande_id']]])
                                                    ->groupBy('bon_commandes.id')
                                                    ->first();
                    if(!$bon_commande){
                        return response()->json(["code" => 0, "msg" => "Bon de commande introuvable", "data" => null]);
                    }
                    //Montant restant
                    $montant_restant = $montant_restant + ($bon_commande->montantBonRecu - ($bon_commande->accompte + $data['montant']));
                }

                $reglement = new Reglement;
                $reglement->date_reglement = Carbon::createFromFormat('d-m-Y', $data['date_reglement']);
                $reglement->moyen_reglement_id = $data['moyen_reglement_id'];
                $reglement->montant = $data['montant'];
                $reglement->numero_cheque = isset($data['numero_cheque']) && !empty($data['numero_cheque']) ? $data['numero_cheque'] : null;
                $reglement->bon_commande_id = isset($data['bon_commande_id']) && !empty($data['bon_commande_id']) ? $data['bon_commande_id'] : null;
                $reglement->vente_id = isset($data['vente_id']) && !empty($data['vente_id']) ? $data['vente_id'] : null;
                $reglement->reste = $montant_restant;

                //Ajout du scanne du chèque s'il y a en
                if (isset($data['scan_cheque']) && !empty($data['scan_cheque'])) {
                    $scan_cheque = request()->file('scan_cheque');
                    $file_name =  $file_name = 'cheque_'.date('dmYHis');;
                    $path = public_path() . '/documents/cheque/';
                    $scan_cheque->move($path, $file_name);
                    $reglement->scan_cheque = 'documents/cheque/' . $file_name;
                }

                $reglement->created_by = Auth::user()->id;
                $reglement->save();

                //Si reglement concerne un client
                if($reglement && $reglement->vente_id) {
                    $vente->acompte_facture = $vente->acompte_facture + $reglement->montant;
                    $vente->save();

                    //Si le montant payé est supérieur au montant dû on crée un avoir pour le client
                    if($montant_restant < 0){
                        $avoir = new AvoirDoit;
                        $avoir->vente_id = $reglement->vente_id;
                        $avoir->montant = $montant_restant;
                        $avoir->date_operation = now();
                        $avoir->save();
                    }
                }

                //Si reglement concerne un fournisseur
                if ($reglement && $reglement->bon_commande_id) {
                    $bon_commande->accompte = $bon_commande->accompte + $reglement->montant;
                    $bon_commande->save();

                    //Si le montant payé est supérieur au montant dû on crée un doit pour le fournisseur
                    if($montant_restant < 0){
                        $doit = new AvoirDoit;
                        $doit->bon_commande_id =  $reglement->bon_commande_id;
                        $doit->montant = $montant_restant;
                        $doit->date_operation = now();
                        $doit->save();
                    }
                }

                $jsonData["data"] = json_decode($reglement);
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
     * @param  \App\Reglement  $reglement
     * @return Response
     */
    public function updateReglement(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $reglement = Reglement::find($request->get('idReglement'));

        if($reglement) {
            $data = $request->all();
            try {

                $montant_restant = 0;

                //Si le règlement concerne une facture client
                if($reglement->vente_id){
                    $vente = Vente::join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                    ->Where([['ventes.deleted_at', NULL],['ventes.id',$reglement->vente_id]])
                                    ->groupBy('article_ventes.vente_id')
                                    ->first();

                    $vente->acompte_facture = $vente->acompte_facture - $reglement->montant;
                    $vente->save();

                    $avoir = AvoirDoit::where('vente_id',$reglement->vente_id)->first();
                    if($avoir){
                        $avoir->montant = 0;
                        $avoir->save();
                    }

                    //S'il avait fait le reglement par une carte de fidelité
                    $moyenReglement = MoyenReglement::find($reglement->moyen_reglement_id);
                    if($moyenReglement && $moyenReglement->libelle_moyen_reglement == "CARTE DE FIDELITE"){
                        $vente = Vente::find($reglement->vente_id);

                        $compteCarteFidelite = Compte::where('client_id',$vente->client_id)->first();

                        //Gestion du pourcentage de reduction
                        $carte = CarteFidelite::find($compteCarteFidelite->carte_id);

                        $montantRestitue = $reglement->montant + ($carte->reduction*$reglement->montant);

                        //Prélevement sur la carte
                        $compteCarteFidelite->entree = $compteCarteFidelite->entree + $montantRestitue;
                        $compteCarteFidelite->save();

                        //Enregistrement du mouvement du compte de la carte
                        $mouvement = MouvementCompte::where('compte_id',$compteCarteFidelite->id)->first();
                        $mouvement->delete();

                    }else{
                         //Montant restant
                        $montant_restant = $montant_restant + ($vente->montantTTC - ($vente->acompte_facture + $data['montant']));
                    }

                    //Si le reglement se fait par une carte de fidélité
                    if(isset($data['numero_carte_fidelite']) && !empty($data['numero_carte_fidelite'])){
                        $compteCarteFidelite = Compte::where('numero_compte',$data['numero_carte_fidelite'])->first();

                        if(!$compteCarteFidelite){
                            return response()->json(["code" => 0, "msg" => "Carte introuvable, contactez l'administrateur SVP", "data" => NULL]);
                        }

                        //Gestion de la validation du montant
                        if(empty($data['montant_carte']) or !isset($data['montant_carte'])){
                            return response()->json(["code" => 0, "msg" => "Le champ montant de la carte ne doit pas être vide", "data" => NULL]);
                        }

                        //Gestion du pourcentage de reduction
                        $carte = CarteFidelite::find($compteCarteFidelite->carte_id);

                        $montantApresReduction = (intval($data['montant']) - ($carte->reduction*intval($data['montant'])));

                        $soldeCarte = $compteCarteFidelite->entree - $compteCarteFidelite->sortie;

                        //Si le solde est insuffisant
                        if($soldeCarte < $montantApresReduction){
                            return response()->json(["code" => 0, "msg" => "Le solde de carte ".number_format($soldeCarte, 0, ',', ' ')." est insuffisant.", "data" => NULL]);
                        }

                        //Prélevement sur la carte
                        $compteCarteFidelite->sortie = $compteCarteFidelite->sortie + $montantApresReduction;
                        $compteCarteFidelite->save();

                        //Enregistrement du mouvement du compte de la carte
                        $mouvement = new MouvementCompte;
                        $mouvement->compte_id = $compteCarteFidelite->id;
                        $mouvement->initiale = $soldeCarte;
                        $mouvement->sortie = $montantApresReduction;
                        $mouvement->date_mouvement = now();
                        $mouvement->save();
                    }else{
                        //Montant restant
                        $montant_restant = $montant_restant + ($vente->montantTTC - ($vente->acompte_facture + $data['montant']));
                    }
                }

                //Si le règlement concerne un bon fournisseur
                if($reglement->bon_commande_id){
                    $bon_commande =  BonCommande::join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
                                                    ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantBonRecu'))
                                                    ->where([['bon_commandes.deleted_at', null], ['bon_commandes.id',$reglement->bon_commande_id]])
                                                    ->groupBy('bon_commandes.id')
                                                    ->first();

                    $bon_commande->accompte = $bon_commande->accompte - $reglement->montant;
                    $bon_commande->save();

                    $doit = AvoirDoit::where('bon_commande_id',$reglement->bon_commande_id)->first();
                    if($doit){
                        $avoir->montant = 0;
                        $avoir->save();
                    }
                    //Montant restant
                    $montant_restant = $montant_restant + ($bon_commande->montantBonRecu - ($bon_commande->accompte + $data['montant']));
                }

                $reglement->date_reglement = Carbon::createFromFormat('d-m-Y', $data['date_reglement']);
                $reglement->moyen_reglement_id = $data['moyen_reglement_id'];
                $reglement->montant = $data['montant'];
                $reglement->numero_cheque = isset($data['numero_cheque']) && !empty($data['numero_cheque']) ? $data['numero_cheque'] : null;
                $reglement->bon_commande_id = isset($data['bon_commande_id']) && !empty($data['bon_commande_id']) ? $data['bon_commande_id'] : null;
                $reglement->vente_id = isset($data['vente_id']) && !empty($data['vente_id']) ? $data['vente_id'] : null;
                $reglement->reste = $montant_restant;

                //Ajout du scanne du chèque s'il y a en
                if (isset($data['scan_cheque']) && !empty($data['scan_cheque'])) {
                    $scan_cheque = request()->file('scan_cheque');
                    $file_name = 'cheque_'.date('dmYHis');
                    $path = public_path() . '/documents/cheque/';
                    $scan_cheque->move($path, $file_name);
                    $reglement->scan_cheque = 'documents/cheque/' . $file_name;
                }
                $reglement->updated_by = Auth::user()->id;
                $reglement->save();

                 //Si reglement concerne un client
                if($reglement && $reglement->vente_id) {
                    $vente->acompte_facture = $vente->acompte_facture + $reglement->montant;
                    $vente->save();

                    //Si le montant payé est supérieur au montant dû on crée un avoir pour le client
                    if($montant_restant < 0){
                        $avoir = AvoirDoit::where('vente_id',$reglement->vente_id)->first();

                        if($avoir){
                            $avoir->montant = $montant_restant;
                            $avoir->save();
                        }else{
                            $avoir = new AvoirDoit;
                            $avoir->vente_id = $reglement->vente_id;
                            $avoir->montant = $montant_restant;
                            $avoir->date_operation = now();
                            $avoir->save();
                        }
                    }
                }

                //Si reglement concerne un fournisseur
                if ($reglement && $reglement->bon_commande_id) {
                    $bon_commande->accompte = $bon_commande->accompte + $reglement->montant;
                    $bon_commande->save();

                    //Si le montant payé est supérieur au montant dû on crée un doit pour le fournisseur
                    if($montant_restant < 0){
                        $doit = AvoirDoit::where('bon_commande_id',$reglement->bon_commande_id)->first();

                        if($doit){
                            $doit->montant = $montant_restant;
                            $doit->save();
                        }else{
                            $doit = new AvoirDoit;
                            $doit->bon_commande_id = $reglement->bon_commande_id;
                            $doit->montant = $montant_restant;
                            $doit->date_operation = now();
                            $doit->save();
                        }
                    }
                }

                $jsonData["data"] = json_decode($reglement);
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
     * @param  \App\Reglement  $reglement
     * @return Response
     */
    public function destroy(Reglement $reglement)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($reglement) {
            try {
                //Si le règlement concerne une facture client
                if($reglement->vente_id){
                    $vente = Vente::join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'))
                                    ->Where([['ventes.deleted_at', NULL],['ventes.id',$reglement->vente_id]])
                                    ->groupBy('article_ventes.vente_id')
                                    ->first();

                    $vente->acompte_facture = $vente->acompte_facture - $reglement->montant;
                    $vente->save();

                    $avoir = AvoirDoit::where('vente_id',$reglement->vente_id)->first();
                    if($avoir){
                        $avoir->delete();
                    }

                     //S'il avait fait le reglement par une carte de fidelité
                     $moyenReglement = MoyenReglement::find($reglement->moyen_reglement_id);
                     if($moyenReglement && $moyenReglement->libelle_moyen_reglement == "CARTE DE FIDELITE"){
                         $vente = Vente::find($reglement->vente_id);

                         $compteCarteFidelite = Compte::where('client_id',$vente->client_id)->first();

                         //Gestion du pourcentage de reduction
                         $carte = CarteFidelite::find($compteCarteFidelite->carte_id);

                         $montantRestitue = $reglement->montant + ($carte->reduction*$reglement->montant);

                         //Prélevement sur la carte
                         $compteCarteFidelite->entree = $compteCarteFidelite->entree + $montantRestitue;
                         $compteCarteFidelite->save();

                         //Enregistrement du mouvement du compte de la carte
                         $mouvement = MouvementCompte::where('compte_id',$compteCarteFidelite->id)->first();
                         $mouvement->delete();

                     }
                }

                //Si le règlement concerne un bon fournisseur
                if($reglement->bon_commande_id){
                    $bon_commande =  BonCommande::join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
                                                    ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantBonRecu'))
                                                    ->where([['bon_commandes.deleted_at', null], ['bon_commandes.id',$reglement->bon_commande_id]])
                                                    ->groupBy('bon_commandes.id')
                                                    ->first();

                    $bon_commande->accompte = $vente->accompte - $reglement->montant;
                    $bon_commande->save();

                    $doit = AvoirDoit::where('bon_commande_id',$reglement->bon_commande_id)->first();
                    if($doit){
                        $avoir->delete();
                    }
                }
                $reglement->update(['deleted_by' => Auth::user()->id]);
                $reglement->delete();
                $jsonData["data"] = json_decode($reglement);
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

    //Etat reçu
    public function recuReglementPdf($reglement)
    {
        $reglement = Reglement::with('moyen_reglement','vente')
                                ->join('ventes', 'ventes.id', '=', 'reglements.vente_id')
                                ->join('clients', 'clients.id', '=', 'ventes.client_id')
                                ->select('reglements.*', 'ventes.client_id as id_client','clients.full_name_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                                ->Where([['reglements.deleted_at', null], ['reglements.id',$reglement]])
                                ->orderBy('reglements.id', 'DESC')
                                ->first();
        $barcodeGenerator = new BarcodeGeneratorPNG();
        /* Il faut faire un reçu de règlement
        * Sur le reçu doit s'afficher :
        * le montant régler et le montantn restant pour le prochain reglement.
        *Si le montant restant est negatif alors tu informes le client qu'il a un avoir corespondant aux montant
        * Sur le reçu doit s'afficher : le numero de facture, le nom du client.
        * Le reçu concerne uniquement les clients
        */
        // Tableau de données
        $data = [
            'reglement' => $reglement,
            'configs' => $this->infosConfig(),
            'barcode' => base64_encode(
                $barcodeGenerator->getBarcode(
                    $reglement->numero_facture,
                    $barcodeGenerator::TYPE_CODE_128
                )
            ),
            //'title' => 'REGLEMENT-' . $reglement->numero_ticket . '-pdf',
        ];
        // Affichage
        //return $data;
        return view('crm.etats.recu-reglement', $data);
    }

}
