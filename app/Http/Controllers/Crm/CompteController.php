<?php

namespace App\Http\Controllers\Crm;

use Exception;
use Illuminate\Http\Request;
use App\Models\Crm\MouvementCompte;
use App\Models\Crm\Compte;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorPNG;

class CompteController extends Controller
{

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $menuPrincipal = "CRM";
        $titleControlleur = "Tous les comptes";
        $btnModalAjout = "FALSE";
        return view('crm.compte.index', compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function compteCarteFidelite()
    {
        $clients = DB::table('clients')->Where('deleted_at', null)->orderBy('full_name_client', 'asc')->get();
        $cartes = DB::table('carte_fidelites')->get();

        $menuPrincipal = "CRM";
        $titleControlleur = "Compte carte de fidélité";
        $btnModalAjout = "TRUE";
        return view('crm.compte.compte-carte-fidelite', compact('clients', 'cartes', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeCompte()
    {
        $comptes = Compte::with('client', 'carte', 'fournisseur')
            ->select('comptes.*')
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
        return response()->json($jsonData);
    }

    public function listeCompteClient()
    {
        $comptes = Compte::with('client')
            ->select('comptes.*')
            ->Where([['client_id', '!=', null], ['carte_id', null]])
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
        return response()->json($jsonData);
    }

    public function listeCompteCarte()
    {
        $comptes = Compte::with('carte', 'client')
            ->select('comptes.*')
            ->Where('carte_id', '!=', null)
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
        return response()->json($jsonData);
    }

    public function listeCompteFournisseur()
    {
        $comptes = Compte::with('fournisseur')
            ->select('comptes.*')
            ->Where('fournisseur_id', '!=', null)
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
        return response()->json($jsonData);
    }

    public function listeCompteCarteByClient($client)
    {
        $comptes = Compte::with('carte', 'client')
            ->select('comptes.*')
            ->Where([['carte_id', '!=', null], ['client_id', $client]])
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
        return response()->json($jsonData);
    }

    public function listeCompteCarteByCarte($carte)
    {
        $comptes = Compte::with('carte', 'client')
            ->select('comptes.*')
            ->Where('carte_id', $carte)
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
        return response()->json($jsonData);
    }

    public function listeCompteClientByClient($client)
    {
        $comptes = Compte::with('client')
            ->select('comptes.*')
            ->Where('client_id', $client)
            ->orderBy('comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $comptes->toArray();
        $jsonData["total"] = $comptes->count();
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
        if ($request->isMethod('post')) {

            $data = $request->all();

            try {

                $Compte = Compte::where([['client_id', $data['client_id']], ['id', '!=', $data['id']]])->first();
                if ($Compte != null) {
                    return response()->json(["code" => 0, "msg" => "Ce client possède déjà une carte.", "data" => null]);
                }

                $compte = $data['id'] ? Compte::findOrFail($data['id']) : new Compte;

                if (empty($data['id'])) {
                    //Création du numero de compte
                    $maxIdTable = DB::table('comptes')->max('id');
                    $idCompte = $maxIdTable + 1;
                    $date = date("dmYHis");
                    $numero_compte = $date . $idCompte;
                    $compte->numero_compte = $numero_compte;
                }

                $compte->client_id = isset($data['client_id']) && !empty($data['client_id']) ? $data['client_id'] : null;
                $compte->carte_id = isset($data['carte_id']) && !empty($data['carte_id']) ? $data['carte_id'] : null;
                $compte->fournisseur_id = isset($data['fournisseur_id']) && !empty($data['fournisseur_id']) ? $data['fournisseur_id'] : null;
                $compte->save();

                $jsonData["data"] = json_decode($compte);
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

    public function rechargeCompte(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('id')) {

            $data = $request->all();

            try {

                $compte = Compte::findOrFail($data['id']);

                $initial = $compte->entree - $compte->sortie;

                $compte->entree = $compte->entree + $data['montant'];
                $compte->save();

                if ($compte) {
                    $mouvement = new MouvementCompte;
                    $mouvement->compte_id = $compte->id;
                    $mouvement->initiale = $initial;
                    $mouvement->entree = $data['montant'];
                    $mouvement->date_mouvement = now();
                    $mouvement->save();
                }

                $jsonData["data"] = json_decode($compte);
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
     * @param  \App\Models\Compte  $compte
     * @return Response
     */
    public function destroy($id)
    {
        $compte = Compte::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($compte) {
            try {

                //Lors de la suppression d'un compte on supprime tous ses mouvements
                $mouvements = MouvementCompte::where('compte_id', $compte->id)->get();
                if ($mouvements) {
                    foreach ($mouvements as $mouvement) {
                        $mouvement->delete();
                    }
                }
                $compte->delete();

                $jsonData["data"] = json_decode($compte);
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

    //Confaction de la carte de fidélité
    public function carteFidelitePdf($idCompte)
    {
        // Chargement des données
        $compte = Compte::with('carte', 'client')
            ->select('comptes.*')
            ->Where([['id', $idCompte], ['carte_id', '!=', null]])
            ->orderBy('comptes.id', 'DESC')
            ->first();
        $configs = $this->infosConfig();
        // Génération du code barre
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $barcode = base64_encode(
            $barcodeGenerator->getBarcode(
                $compte->numero_compte,
                $barcodeGenerator::TYPE_CODE_128
            )
        );
        // Affichage
        //return $compte;
        return view('crm.etats.carte-fidelite', compact('compte', 'configs', 'barcode'));
    }
    //Etat pour les listes en pdf
    public function listeComptePdf()
    {
        // Chargement des données
        $data = $this->listeCompte();
        $data = json_decode($data->content(), true);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['rows']);
        $data['montantTotal'] = 0;
        // Calcul du montant total
        foreach ($data['rows'] as $row) {
            $data['montantTotal'] += $row['entree'];
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-comptes', $data);
    }

    public function listeCompteClientPdf()
    {
        // Chargement des données
        $data = $this->listeCompteClient();
        $data = json_decode($data->content(), true);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['rows']);
        $data['montantTotal'] = 0;
        // Calcul du montant total
        foreach ($data['rows'] as $row) {
            $data['montantTotal'] += $row['entree'];
        }
        // Affichage
        return view('crm.etats.liste-comptes-client', $data);
    }

    public function listeCompteCartePdf()
    {
        // Chargement des données
        $data = $this->listeCompteCarte();
        $data = json_decode($data->content(), true);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['rows']);
        $data['montantTotal'] = 0;
        // Calcul du montant total
        foreach ($data['rows'] as $row) {
            $data['montantTotal'] += $row['entree'];
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-comptes-carte-fidelite', $data);
    }

    public function listeCompteCarteByCartePdf($carte)
    {
        // Chargement des données
        $data = $this->listeCompteCarteByCarte($carte);
        $data = json_decode($data->content(), true);
        $data['configs'] = $this->infosConfig();
        $data['searchBy'] = 'carte';
        $data['Total'] = count($data['rows']);
        $data['montantTotal'] = 0;
        // Calcul du montant total
        foreach ($data['rows'] as $row) {
            $data['montantTotal'] += $row['entree'];
        }
        // Affichage
        return view('crm.etats.liste-comptes-carte-fidelite', $data);
    }

    public function listeCompteCarteByClientPdf($client)
    {
        // Chargement des données
        $data = $this->listeCompteCarteByClient($client);
        $data = json_decode($data->content(), true);
        $data['configs'] = $this->infosConfig();
        $data['searchBy'] = 'client';
        $data['Total'] = count($data['rows']);
        $data['montantTotal'] = 0;
        // Calcul du montant total
        foreach ($data['rows'] as $row) {
            $data['montantTotal'] += $row['entree'];
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-comptes-carte-fidelite', $data);
    }
}
