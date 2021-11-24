<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\BonCommande;
use App\Models\Boutique\CaisseOuverte;
use App\Models\Boutique\Operation;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Caisse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    }

    public function vueOperationCaisseAdmin()
    {
        if (Auth::user()->role == "Administrateur" or Auth::user()->role == "Concepteur") {
            $caisses = Caisse::with('depot')->Where('deleted_at', null)->get();
            $titleControlleur = "Liste des caisses par dépôt";
        }
        if (Auth::user()->role == "Gerant") {
            $caisses = Caisse::with('depot')->Where([['deleted_at', null], ['caisses.depot_id', Auth::user()->depot_id]])->get();
            $titleControlleur = "Liste des caisses de votre dépôt";
        }
        $menuPrincipal = "Boutique";
        $btnModalAjout = "FALSE";
        return view('boutique.operation.operation-caisse-admin', compact('caisses', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function operationCaisseAdmin(Request $request)
    {
        $clients = DB::table('clients')->Where('deleted_at', null)->orderBy('full_name_client', 'asc')->get();
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();
        $caisse_ouverte = CaisseOuverte::where([['caisse_id', $request->caisse_id], ['date_fermeture', null]])->first();
        $caisse = Caisse::find($request->caisse_id);
        $menuPrincipal = "Boutique";
        $titleControlleur = "Opération de caisse";
        $btnModalAjout = $caisse_ouverte != null ? "TRUE" : "FALSE";
        return view('boutique.operation.operation-caisse', compact('fournisseurs', 'clients', 'caisse_ouverte', 'caisse', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vueOperationCaisse(Request $request)
    {
        $caisse_ouverte = null;
        $auth_user = Auth::user();
        $caisse = null;
        //Recupértion de la caisse dans la session
        if ($request->session()->has('session_caisse_ouverte')) {
            $caisse_ouverte_id = $request->session()->get('session_caisse_ouverte');
            $caisse_ouverte = CaisseOuverte::where([['id', $caisse_ouverte_id], ['date_fermeture', null]])->first();
            if ($caisse_ouverte) {
                $caisse = Caisse::find($caisse_ouverte->caisse_id);
            }
        }
        //Si la caisse n'est pas fermée et que l'user s'est déconnecté
        $caisse_ouverte_non_fermee = CaisseOuverte::where([['user_id', $auth_user->id], ['date_fermeture', null]])->first();
        if ($caisse_ouverte_non_fermee != null) {
            $request->session()->put('session_caisse_ouverte', $caisse_ouverte_non_fermee->id);
            $caisse_ouverte = CaisseOuverte::find($caisse_ouverte_non_fermee->id);
            if ($caisse_ouverte) {
                $caisse = Caisse::find($caisse_ouverte->caisse_id);
            }
        }
        $clients = DB::table('clients')->Where('deleted_at', null)->orderBy('full_name_client', 'asc')->get();
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();
        $menuPrincipal = "Boutique";
        $titleControlleur = "Opération de caisse";
        $btnModalAjout = $caisse_ouverte != null ? "TRUE" : "FALSE";
        return view('boutique.operation.operation-caisse', compact('fournisseurs', 'clients', 'caisse_ouverte', 'caisse', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeOperationsByCaisse($caisse)
    {
        $operations = [];
        if (Auth::user()->role != 'Caissier') {
            $operations = Operation::with('vente', 'bon_commande')
                ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'operations.caisse_ouverte_id')
                ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                ->select('operations.*', DB::raw('DATE_FORMAT(operations.date_operation, "%d-%m-%Y") as date_operations'))
                ->Where([['operations.deleted_at', null], ['caisse_ouvertes.caisse_id', $caisse]])
                ->where('caisse_ouvertes.date_fermeture', null)
                ->orderBy('operations.id', 'DESC')
                ->get();
        } else {

            $operations = Operation::with('vente', 'bon_commande')
                ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'operations.caisse_ouverte_id')
                ->select('operations.*', DB::raw('DATE_FORMAT(operations.date_operation, "%d-%m-%Y") as date_operations'))
                ->Where([['operations.deleted_at', null], ['caisse_ouvertes.user_id', Auth::user()->id], ['caisse_ouvertes.date_fermeture', null], ['caisse_ouvertes.caisse_id', $caisse]])
                ->where('caisse_ouvertes.date_fermeture', null)
                ->orderBy('operations.id', 'DESC')
                ->get();
        }

        $jsonData["rows"] = $operations->toArray();
        $jsonData["total"] = $operations->count();
        return response()->json($jsonData);
    }

    public function  listeOperationsByCaisseDate($caisse, $dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $operations = Operation::with('vente', 'bon_commande')
            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'operations.caisse_ouverte_id')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->select('operations.*', DB::raw('DATE_FORMAT(operations.date_operation, "%d-%m-%Y") as date_operations'))
            ->Where([['operations.deleted_at', null], ['caisse_ouvertes.caisse_id', $caisse]])
            ->whereDate('operations.date_operation', $date)
            ->orderBy('operations.id', 'DESC')
            ->get();
        $jsonData["rows"] = $operations->toArray();
        $jsonData["total"] = $operations->count();
        return response()->json($jsonData);
    }
    public function  listeOperationsByCaisseFournisseur($caisse, $fournisseur)
    {
        $operations = Operation::with('bon_commande')
            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'operations.caisse_ouverte_id')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('bon_commandes', 'bon_commandes.id', '=', 'operations.bon_commande_id')
            ->select('operations.*', DB::raw('DATE_FORMAT(operations.date_operation, "%d-%m-%Y") as date_operations'))
            ->Where([['operations.deleted_at', null], ['caisse_ouvertes.caisse_id', $caisse], ['bon_commandes.fournisseur_id', $fournisseur]])
            ->orderBy('operations.id', 'DESC')
            ->get();
        $jsonData["rows"] = $operations->toArray();
        $jsonData["total"] = $operations->count();
        return response()->json($jsonData);
    }
    public function  listeOperationsByCaisseClient($caisse, $client)
    {
        $operations = Operation::with('vente')
            ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'operations.caisse_ouverte_id')
            ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
            ->join('ventes', 'ventes.id', '=', 'operations.vente_id')
            ->select('operations.*', DB::raw('DATE_FORMAT(operations.date_operation, "%d-%m-%Y") as date_operations'))
            ->Where([['operations.deleted_at', null], ['caisse_ouvertes.caisse_id', $caisse], ['ventes.client_id', $client]])
            ->orderBy('operations.id', 'DESC')
            ->get();
        $jsonData["rows"] = $operations->toArray();
        $jsonData["total"] = $operations->count();
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
        if ($request->isMethod('post') && $request->input('montant_operation')) {
            $data = $request->all();
            try {

                //Recupértion de la caisse dans la session
                if (Auth::user()->role == "Caissier") {
                    if ($request->session()->has('session_caisse_ouverte')) {
                        $caisse_ouverte_id = $request->session()->get('session_caisse_ouverte');
                        $caisse_ouverte = CaisseOuverte::find($caisse_ouverte_id);
                    }
                    if (!$caisse_ouverte or $caisse_ouverte->date_fermeture != null) {
                        return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => null]);
                    }
                }
                if (Auth::user()->role != "Caissier") {
                    $caisse_ouverte = CaisseOuverte::where([['caisse_id', $data['caisse_id']], ['date_fermeture', null]])->first();
                    if (!$caisse_ouverte) {
                        return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => null]);
                    }
                    $caisse_ouverte_id = $caisse_ouverte->id;
                }

                //Si c'est une sortie d'argent on verifie si la caisse à suifissament d'argent
                $ventes_caisse = Vente::with('depot', 'caisse_ouverte')
                    ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture', null)
                    ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                    ->join('depots', 'depots.id', '=', 'ventes.depot_id')
                    ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                    ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', null)
                    ->select('caisse_ouvertes.*', 'users.full_name', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                    ->Where([['ventes.deleted_at', null], ['caisse_ouvertes.id', $caisse_ouverte_id]])
                    ->groupBy('caisse_ouvertes.id')
                    ->orderBy('ventes.id', 'DESC')
                    ->first();

                if ($data['type_operation'] == 'sortie') {
                    $soldeCaisse = ($ventes_caisse->sommeTotale + $caisse_ouverte->montant_ouverture + $caisse_ouverte->entree) - $caisse_ouverte->sortie;
                    if ($data['montant_operation'] > $soldeCaisse) {
                        return response()->json(["code" => 0, "msg" => "Le montant de la caisse est insuffisant pour cette opération ", "data" => null]);
                    }
                }

                $operation = new Operation;
                $operation->date_operation = now();
                $operation->objet_operation = $data['objet_operation'];
                $operation->montant_operation = $data['montant_operation'];
                $operation->type_operation = $data['type_operation'];
                $operation->caisse_ouverte_id = $caisse_ouverte_id;
                $operation->created_by = Auth::user()->id;
                $operation->save();


                //Enregistrement du montant dans la caisse
                if ($operation != null) {
                    $caisseOuverte = CaisseOuverte::find($caisse_ouverte_id);
                    if ($data['type_operation'] == 'entree') {
                        $caisseOuverte->entree = $caisseOuverte->entree + $data['montant_operation'];
                    } else {
                        $caisseOuverte->sortie = $caisseOuverte->sortie + $data['montant_operation'];
                    }
                    $caisseOuverte->updated_by = Auth::user()->id;
                    $caisseOuverte->save();
                }

                $jsonData["data"] = json_decode($operation);
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
     * @param  \App\Operation  $operation
     * @return Response
     */
    public function update(Request $request, Operation $operation)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($operation) {
            $data = $request->all();
            try {

                //Recupértion de la caisse 
                $caisse_ouverte = CaisseOuverte::find($operation->caisse_ouverte_id);
                if ($caisse_ouverte->date_fermeture != null) {
                    return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => null]);
                }

                if ($operation->type_operation == 'entree') {
                    $caisse_ouverte->entree = $caisse_ouverte->entree - $operation->montant_operation;
                    $caisse_ouverte->updated_by = Auth::user()->id;
                    $caisse_ouverte->save();
                }
                if ($operation->type_operation == 'sortie') {
                    $caisse_ouverte->sortie = $caisse_ouverte->sortie - $operation->montant_operation;
                    $caisse_ouverte->updated_by = Auth::user()->id;
                    $caisse_ouverte->save();
                }

                //Si c'est une sortie d'argent on verifie si la caisse à suifissament d'argent
                $ventes_caisse = Vente::with('depot', 'caisse_ouverte')
                    ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('date_fermeture', null)
                    ->join('caisses', 'caisses.id', '=', 'caisse_ouvertes.caisse_id')
                    ->join('depots', 'depots.id', '=', 'ventes.depot_id')
                    ->join('users', 'users.id', '=', 'caisse_ouvertes.user_id')
                    ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', null)
                    ->select('caisse_ouvertes.*', 'users.full_name', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                    ->Where([['ventes.deleted_at', null], ['caisse_ouvertes.id', $operation->caisse_ouverte_id]])
                    ->groupBy('caisse_ouvertes.id')
                    ->orderBy('ventes.id', 'DESC')
                    ->first();

                if ($data['type_operation'] == 'sortie') {
                    $soldeCaisse = ($ventes_caisse->sommeTotale + $caisse_ouverte->montant_ouverture + $caisse_ouverte->entree) - $caisse_ouverte->sortie;
                    if ($data['montant_operation'] > $soldeCaisse) {
                        if ($operation->type_operation == 'entree') {
                            $caisse_ouverte->entree = $caisse_ouverte->entree + $operation->montant_operation;
                            $caisse_ouverte->updated_by = Auth::user()->id;
                            $caisse_ouverte->save();
                        }
                        if ($operation->type_operation == 'sortie') {
                            $caisse_ouverte->sortie = $caisse_ouverte->sortie + $operation->montant_operation;
                            $caisse_ouverte->updated_by = Auth::user()->id;
                            $caisse_ouverte->save();
                        }
                        return response()->json(["code" => 0, "msg" => "Le montant de la caisse est insuffisant pour cette opération ", "data" => null]);
                    }
                }


                $operation->objet_operation = $data['objet_operation'];
                $operation->montant_operation = $data['montant_operation'];
                $operation->type_operation = $data['type_operation'];
                $operation->updated_by = Auth::user()->id;
                $operation->save();


                //Enregistrement du montant dans la caisse
                if ($operation != null) {
                    $caisseOuverte = CaisseOuverte::find($operation->caisse_ouverte_id);
                    if ($data['type_operation'] == 'entree') {
                        $caisseOuverte->entree = $caisseOuverte->entree + $data['montant_operation'];
                    } else {
                        $caisseOuverte->sortie = $caisseOuverte->sortie + $data['montant_operation'];
                    }
                    $caisseOuverte->updated_by = Auth::user()->id;
                    $caisseOuverte->save();
                }

                $jsonData["data"] = json_decode($operation);
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
     * @param  \App\Operation  $operation
     * @return Response
     */
    public function destroy(Operation $operation)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($operation) {
            try {
                $caisse_ouverte = CaisseOuverte::find($operation->caisse_ouverte_id);
                if ($caisse_ouverte->date_fermeture != null) {
                    return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => null]);
                }
                if ($operation->type_operation == 'entree') {
                    $caisse_ouverte->entree = $caisse_ouverte->entree - $operation->montant_operation;
                    $caisse_ouverte->updated_by = Auth::user()->id;
                    $caisse_ouverte->save();
                }
                if ($operation->type_operation == 'sortie') {
                    $caisse_ouverte->sortie = $caisse_ouverte->sortie - $operation->montant_operation;
                    $caisse_ouverte->updated_by = Auth::user()->id;
                    $caisse_ouverte->save();
                }
                $operation->update(['deleted_by' => Auth::user()->id]);
                $operation->delete();
                $jsonData["data"] = json_decode($operation);
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
}
