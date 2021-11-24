<?php

namespace App\Http\Controllers\Vente;

use Exception;
use App\Models\Crm\Compte;
use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use App\Models\Stock\Article;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\Parametre\Caisse;
use App\Models\Stock\DepotArticle;
use App\Models\Vente\ArticleVente;
use Illuminate\Support\Facades\DB;
use App\Models\Crm\MouvementCompte;
use App\Http\Controllers\Controller;
use App\Models\Stock\MouvementStock;
use Illuminate\Support\Facades\Auth;
use App\Models\Comptabilite\CaisseOuverte;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\User;
use App\Models\Parametre\CarteFidelite;
include_once(app_path() . "/number-to-letters/nombre_en_lettre.php");

class VenteController extends Controller
{

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    /**Gestion des ventes (facturation) */

    public function index(){
        $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
        $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
        $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();

        $menuPrincipal = "Vente";
        $titleControlleur = "Facturation";
        $btnModalAjout = "TRUE";
        return view('vente.facturation.index',compact('moyenReglements','clients','categories','depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeVente(string $numero_facture = null){
        if($numero_facture){
            $ventes = Vente::with('depot','client')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                                ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null],['ventes.numero_facture', 'like', '%' . $numero_facture . '%']])
                                ->groupBy('article_ventes.vente_id')
                                ->orderBy('ventes.id','DESC')
                                ->get();
        }else{
            $ventes = Vente::with('depot','client')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                                ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null]])
                                ->groupBy('article_ventes.vente_id')
                                ->orderBy('ventes.id','DESC')
                                ->get();
        }

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVenteByClient($client){

        $ventes = Vente::with('depot','client')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                                ->groupBy('article_ventes.vente_id')
                                ->orderBy('ventes.id','DESC')
                                ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVenteByDate($date_vente){
        $date = Carbon::createFromFormat('d-m-Y', $date_vente);

        $ventes = Vente::with('depot','client')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                                ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null]])
                                ->WhereDate('ventes.date_vente',$date)
                                ->groupBy('article_ventes.vente_id')
                                ->orderBy('ventes.id','DESC')
                                ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function venteStore(Request $request){
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];

        if ($request->isMethod('post') && $request->input('monPanier')) {
            $data = $request->all();
            try {
                    if(empty($data['monPanier'])){
                        return response()->json(["code" => 0, "msg" => "Votre panier est vide", "data" => NULL]);
                    }

                    //Verification si le client et le dépôt existe
                    if((!isset($data['client_id']) && empty($data['client_id'])) or (!isset($data['depot_id']) && empty($data['depot_id']))){
                        return response()->json(["code" => 0, "msg" => "Les champs dépôt et client sont obligatoire", "data" => NULL]);
                    }

                    //formation numéro du ticket
                    $maxIdVente = DB::table('ventes')->max('id');
                    $annee = date("Y");
                    $numero_id = sprintf("%06d", ($maxIdVente + 1));

                    $vente = new Vente;
                    $vente->numero_facture = $annee.$numero_id;
                    $vente->depot_id = $data["depot_id"];
                    $vente->client_id = $data["client_id"];
                    $vente->date_vente = now();
                    $vente->created_by = Auth::user()->id;
                    $vente->save();

                    //Ajout des articles dans la vente
                    if($vente && !empty($data["monPanier"])){
                        //enregistrement des articles de la ligne de vente
                        $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        $montantTTC = 0;

                        foreach($panierContent as $index => $article) {


                            $articleVente = ArticleVente::where([['vente_id',$vente->id],['depot_id',$data["depot_id"]],['article_id',$data["monPanier"][$index]["articles"]]])->first();
                            if(!$articleVente){
                                $articleVente = new ArticleVente();
                                $articleVente->article_id = $data["monPanier"][$index]["articles"];
                                $articleVente->vente_id = $vente->id;
                                $articleVente->depot_id = $data["depot_id"];
                                $articleVente->choix_prix = $data["monPanier"][$index]["choix_prix"];
                                $articleVente->quantite = $data["monPanier"][$index]["quantites"];
                                $articleVente->prix = $data["monPanier"][$index]["prix"];
                                $articleVente->created_by = Auth::user()->id;
                                $articleVente->save();
                            }
                            $montantTTC = $montantTTC + ($data["monPanier"][$index]["prix"] * $data["monPanier"][$index]["quantites"]);
                        }

                        //Vérifions si l'article est stockable ou non
                        $article = Article::find($data["monPanier"][$index]["articles"]);
                        if($article && $article->non_stockable == 0){
                            //Dimunition stock dans depot-article
                            $depotArticle = DepotArticle::where([['depot_id', $data["depot_id"]], ['article_id', $data["monPanier"][$index]["articles"]]])->first();
                            $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["monPanier"][$index]["articles"]]])->whereDate('date_mouvement', date('Y-m-d'))->first();
                            if(!$mouvementStock) {
                                $mouvementStock = new MouvementStock;
                                $mouvementStock->date_mouvement = now();
                                $mouvementStock->depot_id = $data['depot_id'];
                                $mouvementStock->article_id = $data["monPanier"][$index]["articles"];
                                $mouvementStock->quantite_initiale = $depotArticle ? $depotArticle->quantite_disponible : 0;
                                $mouvementStock->created_by = Auth::user()->id;
                            }

                            $depotArticle->quantite_disponible = $depotArticle->quantite_disponible - $data["monPanier"][$index]["quantites"];
                            $depotArticle->save();
                            $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data["monPanier"][$index]["quantites"];
                            $mouvementStock->save();
                        }
                    }

                    $jsonData["data"] = json_decode($vente);
                    return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = NULL;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => NULL]);
    }

    public function updateVente(Request $request){
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        $data = $request->all();
        $vente = Vente::find($data['idVente']);

        if($vente) {

            try {

                    $vente->client_id = $data["client_id"];
                    $vente->created_by = Auth::user()->id;
                    $vente->save();
                    $jsonData["data"] = json_decode($vente);
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


    /** Gestion du point de caisse **/
    public function pointCaisse(Request $request){
        $caisse_ouverte = null; $auth_user = Auth::user(); $caisse_id = NULL;

        $categories = DB::table('categories')->Where([['deleted_at', NULL],['categorie_id',NULL]])->orderBy('libelle_categorie', 'asc')->get();
        $caisses = $auth_user->role == 'Caissier' ? DB::table('caisses')->Where([['deleted_at', NULL],['depot_id',$auth_user->depot_id]])->orderBy('libelle_caisse', 'asc')->get() : DB::table('caisses')->Where('deleted_at', NULL)->orderBy('libelle_caisse', 'asc')->get();
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('id', 'asc')->get();

        //Recupértion de la caisse dans la session
        if($request->session()->has('session_caisse_ouverte')){
            $caisse_ouverte_id = $request->session()->get('session_caisse_ouverte');
            $caisse_ouverte = CaisseOuverte::where([['id',$caisse_ouverte_id],['date_fermeture',null]])->first();
        }

        //Si la caisse n'est pas fermée et que l'user s'est déconnecté
        $caisse_ouverte_non_fermee = CaisseOuverte::where([['user_id',$auth_user->id],['date_fermeture',null]])->first();
        if($caisse_ouverte_non_fermee!=null){
            $request->session()->put('session_caisse_ouverte',$caisse_ouverte_non_fermee->id);
            $caisse_ouverte = CaisseOuverte::find($caisse_ouverte_non_fermee->id);
        }

        if($caisse_ouverte){
            $caisse = Caisse::find($caisse_ouverte->caisse_id);
            $caisse_id = $caisse->id;
            $titleControlleur = $caisse->libelle_caisse." ouverte";
            $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
            $articles = DepotArticle::with('article')
                                        ->join('articles','articles.id','=','depot_articles.article_id')
                                        ->where('depot_articles.depot_id',$caisse->depot_id)
                                        ->select('depot_articles.*')
                                        ->orderBy('articles.libelle_article', 'ASC')
                                        ->get();

            $menuPrincipal = "Point de caisse";
            $btnModalAjout = "FALSE";
            return view('vente.point-caisse.point-caisse',compact('articles','categories','moyenReglements','caisse_id','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
        }else{
            $menuPrincipal = "Point de caisse";
            $titleControlleur = "Caisse fermée";
            $btnModalAjout = "FALSE";
            return view('vente.point-caisse.point-caisse-ferme',compact('caisses','depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
        }
    }

    public function vueListeVenteCaisse(int $Caisse=null){
        $caisse = null;
        $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
        if(Auth::user()->role != "Caissier" && $Caisse){
            $caisse = Caisse::find($Caisse);
        }
        if(Auth::user()->role == "Caissier" && session()->has('session_caisse_ouverte')){
            $caisse_ouverte = CaisseOuverte::find(session()->get('session_caisse_ouverte'));
            $caisse = Caisse::find($caisse_ouverte->caisse_id);
        }

        $menuPrincipal = "Point de caisse";
        $titleControlleur = "Liste des ventes";
        $btnModalAjout = "FALSE";
        return view('vente.point-caisse.liste-vente-caisse',compact('caisse','moyenReglements','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeVenteCaisse(int $caisse =null){
        $totalCaisse = 0;
        if(Auth::user()->role == "Caissier"){
            $ventes = Vente::with('depot','moyen_reglement')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*','caisses.libelle_caisse','caisses.id as idCaisse','users.full_name',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.user_id',Auth::user()->id]])
                            ->groupBy('article_ventes.vente_id')
                            ->orderBy('ventes.id','DESC')
                            ->get();
        }else{
            $ventes = Vente::with('depot','moyen_reglement')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*','caisses.libelle_caisse','caisses.id as idCaisse','users.full_name',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.caisse_id',$caisse]])
                            ->groupBy('article_ventes.vente_id')
                            ->orderBy('ventes.id','DESC')
                            ->get();
        }
        foreach ($ventes as $vente){
            $totalCaisse = $totalCaisse + $vente->montantTTC + $vente->montant_carte_fidelite;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalCaisse"] = $totalCaisse;
        return response()->json($jsonData);
    }

    public function listeVenteCaisseByNumero($numero_ticket, int $caisse = null){
        $totalCaisse = 0;
        if(Auth::user()->role == "Caissier"){
            $ventes = Vente::with('depot','moyen_reglement')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*','caisses.libelle_caisse','caisses.id as idCaisse','users.full_name',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.user_id',Auth::user()->id],['ventes.numero_ticket', 'like', '%' . $numero_ticket . '%']])
                            ->groupBy('article_ventes.vente_id')
                            ->orderBy('ventes.id','DESC')
                            ->get();
        }else{
            $ventes = Vente::with('depot','moyen_reglement')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*','caisses.libelle_caisse','caisses.id as idCaisse','users.full_name',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.numero_ticket', 'like', '%' . $numero_ticket . '%'],['caisse_ouvertes.caisse_id',$caisse]])
                            ->groupBy('article_ventes.vente_id')
                            ->orderBy('ventes.id','DESC')
                            ->get();

        }
        foreach ($ventes as $vente){
            $totalCaisse = $totalCaisse + $vente->montantTTC + $vente->montant_carte_fidelite;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalCaisse"] = $totalCaisse;
        return response()->json($jsonData);
    }

    public function listeVenteCaisseByDate($date_vente, $caisse){
        $totalCaisse = 0;
        $date = Carbon::createFromFormat('d-m-Y', $date_vente);
        $ventes = Vente::with('depot','moyen_reglement')
                            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*','caisses.libelle_caisse','caisses.id as idCaisse','users.full_name',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.caisse_id',$caisse]])
                            ->whereDate('ventes.date_vente',$date)
                            ->groupBy('article_ventes.vente_id')
                            ->orderBy('ventes.id','DESC')
                            ->get();

        foreach ($ventes as $vente){
            $totalCaisse = $totalCaisse + $vente->montantTTC + $vente->montant_carte_fidelite;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalCaisse"] = $totalCaisse;
        return response()->json($jsonData);
    }

    //Enregistrement depuis le point de caisse
    public function caisseStore(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];

        if ($request->isMethod('post') && $request->input('monPanier')) {
            $data = $request->all();
            try {
                    if(empty($data['monPanier'])){
                        return response()->json(["code" => 0, "msg" => "Votre panier est vide", "data" => NULL]);
                    }

                    //Verification de la caisse (ouvert ou non)
                    $caisse_ouverte = CaisseOuverte::where([['caisse_id',$data['caisse_id']],['date_fermeture',null]])->first();
                    if(!$caisse_ouverte){
                        return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => NULL]);
                    }

                    //formation numéro du ticket
                    $maxIdVente = DB::table('ventes')->max('id');
                    $annee = date("Y");
                    $numero_id = sprintf("%06d", ($maxIdVente + 1));

                    $vente = new Vente;
                    $vente->numero_ticket = "TICKET".$annee.$numero_id;

                    //Si c'est une mise en attente
                    if(isset($data['attente'])){
                        $vente->depot_id = $data["depot_id"];
                        $vente->caisse_ouverte_id = $caisse_ouverte->id;
                        $vente->attente = TRUE;
                        $vente->date_vente = now();
                        $vente->created_by = Auth::user()->id;
                        $vente->save();

                        //Ajout des articles dans la vente
                        if($vente && !empty($data["monPanier"])){
                            //enregistrement des articles de la ligne de vente
                            $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);

                            foreach($panierContent as $index => $article) {
                                $articleVente = ArticleVente::where([['vente_id',$vente->id],['depot_id',$data["depot_id"]],['article_id',$data["monPanier"][$index]["articles"]]])->first();
                                if(!$articleVente){
                                    $articleVente = new ArticleVente();
                                    $articleVente->article_id = $data["monPanier"][$index]["articles"];
                                    $articleVente->vente_id = $vente->id;
                                    $articleVente->depot_id = $data["depot_id"];
                                    $articleVente->quantite = $data["monPanier"][$index]["quantites"];
                                    $articleVente->prix = $data["monPanier"][$index]["prix"];
                                    $articleVente->created_by = Auth::user()->id;
                                    $articleVente->save();
                                }
                            }
                        }

                        $jsonData["data"] = json_decode($vente);
                        return response()->json($jsonData);
                    }

                    //Si le montant payé est insuffusant
                    if($data['montant_a_payer'] > $data['montant_paye']){
                        return response()->json(["code" => 0, "msg" => "Le montant payé est insuffissant pour régler cette facture", "data" => NULL]);
                    }

                    //Gestion du prélevelement sur la carte de fidélité
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

                        $montantApresReduction = (intval($data['montant_carte']) - ($carte->reduction*intval($data['montant_carte'])));

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
                    }

                    $vente->depot_id = $data["depot_id"];
                    $vente->moyen_reglement_id = $data["moyen_reglement_id"];
                    $vente->caisse_ouverte_id = $caisse_ouverte->id;
                    $vente->montant_a_payer = isset($data['montant_a_payer']) && !empty($data['montant_a_payer']) ? intval($data['montant_a_payer']) : 0;
                    $vente->montant_payer = isset($data['montant_paye']) && !empty($data['montant_paye']) ? intval($data['montant_paye']) : 0;
                    $vente->date_vente = now();
                    $vente->created_by = Auth::user()->id;
                    $vente->save();

                    //Ajout des articles dans la vente
                    if($vente && !empty($data["monPanier"])){
                        //enregistrement des articles de la ligne de vente
                        $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        $montantTTC = 0;

                        foreach($panierContent as $index => $article) {
                            $articleVente = ArticleVente::where([['vente_id',$vente->id],['depot_id',$data["depot_id"]],['article_id',$data["monPanier"][$index]["articles"]]])->first();
                            if(!$articleVente){
                                $articleVente = new ArticleVente();
                                $articleVente->article_id = $data["monPanier"][$index]["articles"];
                                $articleVente->vente_id = $vente->id;
                                $articleVente->depot_id = $data["depot_id"];
                                $articleVente->quantite = $data["monPanier"][$index]["quantites"];
                                $articleVente->prix = $data["monPanier"][$index]["prix"];
                                $articleVente->created_by = Auth::user()->id;
                                $articleVente->save();
                            }
                        }

                        //Vérifions si l'article est stockable ou non
                        $article = Article::find($data["monPanier"][$index]["articles"]);
                        if($article && $article->non_stockable == 0){
                            //Dimunition stock dans depot-article
                            $depotArticle = DepotArticle::where([['depot_id', $data["depot_id"]], ['article_id', $data["monPanier"][$index]["articles"]]])->first();
                            $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["monPanier"][$index]["articles"]]])->whereDate('date_mouvement', date('Y-m-d'))->first();
                            if(!$mouvementStock) {
                                $mouvementStock = new MouvementStock;
                                $mouvementStock->date_mouvement = now();
                                $mouvementStock->depot_id = $data['depot_id'];
                                $mouvementStock->article_id = $data["monPanier"][$index]["articles"];
                                $mouvementStock->quantite_initiale = $depotArticle ? $depotArticle->quantite_disponible : 0;
                                $mouvementStock->created_by = Auth::user()->id;
                            }

                            $depotArticle->quantite_disponible = $depotArticle->quantite_disponible - $data["monPanier"][$index]["quantites"];
                            $depotArticle->save();
                            $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data["monPanier"][$index]["quantites"];
                            $mouvementStock->save();
                        }
                    }

                    $jsonData["data"] = json_decode($vente);
                    return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = NULL;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => NULL]);
    }
    //Modification depuis le point de caisse
    public function caisseStoreUpdate(Request $request){
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        $data = $request->all();
        $vente = Vente::find($data['idVente']);

        if($vente) {

            try {
                    //Verification de la caisse (ouvert ou non)
                    $caisse_ouverte = CaisseOuverte::where([['caisse_id',$data['caisse_id']],['date_fermeture',null]])->first();
                    if(!$caisse_ouverte){
                        return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => NULL]);
                    }

                    //Si le montant payé est insuffusant
                    if($data['montant_a_payer'] > $data['montant_paye']){
                        return response()->json(["code" => 0, "msg" => "Le montant payé est insuffissant pour régler cette facture", "data" => NULL]);
                    }

                    //Gestion du prélevelement sur la carte de fidélité
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
                        $montantApresReduction = (intval($data['montant_carte']) - ($carte->reduction*intval($data['montant_carte'])));

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
                    }
                    $oldAttente = $vente->attente;

                    $vente->moyen_reglement_id = $data["moyen_reglement_id"];
                    $vente->montant_a_payer = isset($data['montant_a_payer']) && !empty($data['montant_a_payer']) ? intval($data['montant_a_payer']) : 0;
                    $vente->montant_payer = isset($data['montant_paye']) && !empty($data['montant_paye']) ? intval($data['montant_paye']) : 0;
                    $vente->attente = FALSE;
                    $vente->created_by = Auth::user()->id;
                    $vente->save();

                    //Ajout des articles dans la vente
                    if($vente && $oldAttente == 1){
                        $articleVentes = ArticleVente::where('vente_id',$vente->id)->first();

                        foreach ($articleVentes as $articleVente) {
                            //Vérifions si l'article est stockable ou non
                            $article = Article::find($articleVente->article_id);
                            if($article && $article->non_stockable == 0){
                                //Dimunition stock dans depot-article
                                $depotArticle = DepotArticle::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->first();
                                $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $articleVente->article_id]])->whereDate('date_mouvement', date_format($vente->date_vente, "Y-m-d"))->first();
                                if(!$mouvementStock) {
                                    $mouvementStock = new MouvementStock;
                                    $mouvementStock->date_mouvement = now();
                                    $mouvementStock->depot_id = $vente->depot_id;
                                    $mouvementStock->article_id = $articleVente->article_id;
                                    $mouvementStock->quantite_initiale = $depotArticle ? $depotArticle->quantite_disponible : 0;
                                    $mouvementStock->created_by = Auth::user()->id;
                                }

                                $depotArticle->quantite_disponible = $depotArticle->quantite_disponible - $articleVente->quantite;
                                $depotArticle->save();
                                $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $articleVente->quantite;
                                $mouvementStock->save();
                            }
                        }
                    }

                    $jsonData["data"] = json_decode($vente);
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
     * @param  \App\Vente  $vente
     * @return Response
     */
    public function destroy(Vente $vente)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($vente){
                try {

                if($vente->client_id==null && $vente->caisse_ouverte_id){
                    $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                    if($caisse_ouverte && $caisse_ouverte->date_fermeture != null) {
                        return response()->json(["code" => 0, "msg" => "Sppression impossible car la caisse est fermée", "data" => NULL]);
                    }
                }

                if($vente->attente==0){
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleVentes = ArticleVente::where('vente_id',$vente->id)->get();
                    foreach($articleVentes as $articleVente) {
                        $Article = Article::find($articleVente->article_id);
                        if ($Article && $Article->non_stockable == 0) {
                            $articleDepot = DepotArticle::where([['article_id',$articleVente->article_id],['depot_id',$vente->depot_id]])->first();
                            $articleDepot->quantite_disponible = $articleDepot->quantite_disponible + $articleVente->quantite;
                            $articleDepot->save();

                            $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                            $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue - $articleVente->quantite;
                            $mouvementStock->save();
                        }
                    }
                }

                $vente->update(['deleted_by' => Auth::user()->id]);
                $vente->delete();
                $jsonData["data"] = json_decode($vente);
                return response()->json($jsonData);
                } catch (Exception $exc) {
                   $jsonData["code"] = -1;
                   $jsonData["data"] = NULL;
                   $jsonData["msg"] = $exc->getMessage();
                   return response()->json($jsonData);
                }
            }
        return response()->json(["code" => 0, "msg" => "Echec de suppression", "data" => NULL]);
    }

    //Ticket de caisse
    public function ticketCaissePdf($id){
        //Informations du ticket
        $infosVente = Vente::with('depot','moyen_reglement')
            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
            ->select('ventes.*','caisses.libelle_caisse','caisses.id as idCaisse','users.full_name',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
            ->Where([['ventes.deleted_at', NULL],['ventes.id',$id]])
            ->groupBy('article_ventes.vente_id')
            ->orderBy('ventes.id','DESC')
            ->first();
        //liste des articles du ticket
        $articlesVente = ArticleVente::with('article')
            ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
            ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
            ->select('article_ventes.*','param_tvas.tva')
            ->Where([['article_ventes.deleted_at', null], ['article_ventes.retourne', 0], ['article_ventes.vente_id', $id]])
            ->get();
        //$userEdit = User::where('id', $infosVente['updated_by'])->first();
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $montantTHT_add = 0;
        $montantTTTC_add = 0;
        $remise = 0;
        $tva = 0;
        // Tableau de données
        $data = [
            //'header' => $header,
            'infosVente' => $infosVente,
            'articlesVente' => $articlesVente,
            //'userEdit' => $userEdit,
            'title' => 'TICKET-' . $infosVente->numero_ticket . '-pdf',
            'barcode' => base64_encode(
                $barcodeGenerator->getBarcode(
                    $infosVente->numero_ticket,
                    $barcodeGenerator::TYPE_CODE_128
                )
            ),
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articlesVente']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['articlesVente'] as $article) {
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.ticket-caisse', $data);
    }

    public function factureVentePdf($id){
        $infosVente = Vente::with('depot','client')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                        ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.id',$id]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('ventes.id','DESC')
                        ->first();

        //liste des articles de la facture
        $articlesVente = ArticleVente::with('article')
                                    ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                    ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                    ->select('article_ventes.*','param_tvas.tva')
                                    ->Where([['article_ventes.deleted_at', null], ['article_ventes.retourne', 0], ['article_ventes.vente_id', $id]])
                                    ->get();
        //$userEdit = User::where('id', $infosVente['updated_by'])->first();
        $infosVente['montantHT'] = $infosVente['montantTTC'] / (1 + $articlesVente[0]['tva']);
        // Tableau de données
        $data = [
            //'header' => $header,
            'infosVente' => $infosVente,
            'articlesVente' => $articlesVente,
            //'userEdit' => $userEdit,
            'title' => 'FACTURE-' . $infosVente->numero_facture . '-pdf',
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articlesVente']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['articlesVente'] as $article) {
            $article['prixHT'] = $article['prix'] / (1 + $article['tva']);
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.facture-vente-a4', $data);
    }
}
