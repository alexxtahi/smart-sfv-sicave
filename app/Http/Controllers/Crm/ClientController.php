<?php

namespace App\Http\Controllers\Crm;

use Exception;
use Validator;
use App\Models\Crm\Client;
use App\Models\Crm\Regime;
use App\Models\Vente\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Parametre\Nation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
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
        $nations = DB::table('nations')->Where('deleted_at', null)->orderBy('libelle_nation', 'asc')->get();
        $regimes = DB::table('regimes')->Where('deleted_at', null)->orderBy('libelle_regime', 'asc')->get();

        $menuPrincipal = "CRM";
        $titleControlleur = "Client";
        $btnModalAjout = "TRUE";
        return view('crm.client.index', compact('nations', 'regimes', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function compteClient()
    {
        $clients = DB::table('clients')->Where('deleted_at', null)->orderBy('full_name_client', 'asc')->get();

        $menuPrincipal = "CRM";
        $titleControlleur = "Compte des clients";
        $btnModalAjout = "TRUE";
        return view('crm.client.compte-client', compact('clients', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function ficheClient($client)
    {
        $infoClient = Client::find($client);
        $menuPrincipal = "Fiche client";
        $titleControlleur = "";
        $btnModalAjout = "FALSE";
        return view('crm.client.fiche', compact('infoClient', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeClient(){
        $clients = Client::with('nation', 'regime')
            ->select('clients.*')
            ->Where('deleted_at', null)
            ->orderBy('clients.id', 'ASC')
            ->get();
        $jsonData["rows"] = $clients->toArray();
        $jsonData["total"] = $clients->count();
        return response()->json($jsonData);
    }

    public function listeClientByNation($nation){
        $clients = Client::with('nation', 'regime')
            ->select('clients.*')
            ->Where([['deleted_at', null], ['nation_id', $nation]])
            ->orderBy('clients.id', 'ASC')
            ->get();
        $jsonData["rows"] = $clients->toArray();
        $jsonData["total"] = $clients->count();
        return response()->json($jsonData);
    }

    public function getClientById($id){
        $clients = Client::with('nation', 'regime')
                            ->select('clients.*')
                            ->Where([['deleted_at', null], ['clients.id', $id]])
                            ->get();
        $jsonData["rows"] = $clients->toArray();
        $jsonData["total"] = $clients->count();
        return response()->json($jsonData);
    }

    public function listeFactureClient($client){
        $totalFacture = 0; $totalAcompte = 0;
        $factures = Vente::with('client','depot')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                            ->groupBy('article_ventes.vente_id')
                            ->get();
        foreach($factures as $facture){
            $totalFacture = $totalFacture + $facture->montantTTC;
            $totalAcompte = $totalAcompte + $facture->acompte_facture;
        }

        $jsonData["rows"] = $factures->toArray();
        $jsonData["total"] = $factures->count();
        $jsonData["totalFacture"] = $totalFacture;
        $jsonData["totalAcompte"] = $totalAcompte;
        return response()->json($jsonData);
    }

    public function listeArticleAcheteByClient($client){
        $achats = Vente::where([['ventes.deleted_at',null],['ventes.client_id',$client],['ventes.proformat',0]])
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                        ->join('articles','articles.id','=','article_ventes.article_id')
                        ->select('articles.libelle_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('sum(article_ventes.quantite) as qteTotale'))
                        ->groupBy('article_ventes.article_id')
                        ->orderBy('qteTotale','DESC')
                        ->take(10)->get();

        $jsonData["rows"] = $achats->toArray();
        $jsonData["total"] = $achats->count();
        return response()->json($jsonData);
    }

    public function listeSoldeClient(){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $ventes = Vente::with('client')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                        ->where([['ventes.deleted_at', NULL],['ventes.client_id','!=', NULL],['ventes.proformat',0]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('article_ventes.vente_id','DESC')
                        ->get();
        foreach ($ventes as $vente){
            $MontantTotalDu = $MontantTotalDu + $vente->sommeTotale;
            $MontantTotalAcompte = $MontantTotalAcompte + $vente->sommeAcompte;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["MontantTotalDu"] = $MontantTotalDu;
        $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
        return response()->json($jsonData);
    }

    public function listeSoldeByClient($client){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $ventes = Vente::with('client')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                        ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                        ->where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.proformat',0]])
                        ->groupBy('article_ventes.vente_id')
                        ->orderBy('article_ventes.vente_id','DESC')
                        ->get();
        foreach ($ventes as $vente){
            $MontantTotalDu = $MontantTotalDu + $vente->sommeTotale;
            $MontantTotalAcompte = $MontantTotalAcompte + $vente->sommeAcompte;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["MontantTotalDu"] = $MontantTotalDu;
        $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
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
        if ($request->isMethod('post') && $request->input('full_name_client')) {

            $data = $request->all();

            try {
                $Client = Client::where([['full_name_client', $data['full_name_client']], ['id', '!=', $data['id']]])->first();
                if ($Client != null) {
                    return response()->json(["code" => 0, "msg" => "Ce client existe déjà dans la base, vérifier le nom svp", "data" => null]);
                }
                $client = $data['id'] ? Client::findOrFail($data['id']) : new Client;

                if (empty($data['id'])) {
                    //Création code du client
                    $maxIdTable = DB::table('clients')->max('id');
                    $idClient = $maxIdTable + 1;
                    $caractere_speciaux = array("'", "-", " ");
                    $code_client = '411' . substr(strtoupper(str_replace($caractere_speciaux, '', $data['full_name_client'])), 0, 3) . $idClient;
                    $client->code_client = $code_client;
                }

                $client->full_name_client = $data['full_name_client'];
                $client->contact_client = $data['contact_client'];
                $client->nation_id = $data['nation_id'];
                $client->regime_id = isset($data['regime_id']) && !empty($data['regime_id']) ? $data['regime_id'] : null;
                $client->email_client = isset($data['email_client']) && !empty($data['email_client']) ? $data['email_client'] : null;
                $client->plafond_client = isset($data['plafond_client']) && !empty($data['plafond_client']) ? $data['plafond_client'] : 0;
                $client->compte_contribuable_client = isset($data['compte_contribuable_client']) && !empty($data['compte_contribuable_client']) ? $data['compte_contribuable_client'] : null;
                $client->boite_postale_client = isset($data['boite_postale_client']) && !empty($data['boite_postale_client']) ? $data['boite_postale_client'] : null;
                $client->adresse_client = isset($data['adresse_client']) && !empty($data['adresse_client']) ? $data['adresse_client'] : null;
                $client->fax_client = isset($data['fax_client']) && !empty($data['fax_client']) ? $data['fax_client'] : null;
                $client->created_by = Auth::user()->id;
                $client->save();
                $jsonData["data"] = json_decode($client);
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

    public function downloadModel () {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(storage_path("modeles/modele-importation-en-masse-client.xlsx"));
        $worksheet=$spreadsheet->getActiveSheet();
        // - Couleur des titres de la feuille
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A4:J4')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FF8EA9DB');

        // - Validation régime
        $validation = $spreadsheet->getActiveSheet()->getCell('B5')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir un régime de la liste');
        $validation->setPromptTitle('Liste des régimes disponibles');
        $validation->setPrompt("Veuillez choisir le régime du client");
        $regimes = Regime::orderBy('libelle_regime')
        ->get()
        ->pluck('libelle_regime')
        ->implode(',');
        $validation->setFormula1('"'.$regimes.'"');
        $worksheet->setDataValidation('B5:B10000', $validation);

        // - Validation pays
        $validation = $spreadsheet->getActiveSheet()->getCell('E5')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir un pays de la liste');
        $validation->setPromptTitle('Liste des pays disponibles');
        $validation->setPrompt("Veuillez choisir le pays du client");
        // $pays = Nation::orderBy('libelle_nation')
        // ->get()
        // ->pluck('libelle_nation')
        // ->implode(',');
        $validation->setFormula1('Parametres!$A$1:$A$253');
        // $validation->setFormula1('"'.$pays.'"');
        $worksheet->setDataValidation('E5:E10000', $validation);

        return $this->download($spreadsheet,"Modele d'importation en masse des clients ".now()->toDateString()." à ".now()->toTimeString());
    }


    public function storeFromUpload(Request $request){
        $input=$request->all();
        $validator=Validator::make($input,
            [
                'fileToUpload'   =>'required|mimes:xlsx,xls',
            ],
            [
                'fileToUpload.required'   =>"Veuillez choisir le fichier à uploader svp.",
                'fileToUpload.mimes'   =>"Votre fichier doit être de type xlxs ou xls",
            ]
        );
        if($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->toArray()['fileToUpload'][0]
            ]);
        }
        // - On Transforme le fichier en tableau
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($request->fileToUpload);
        $sheet=$spreadsheet->getActiveSheet();
        // - On transforme le fichier en tableau
        $data=$sheet->toArray();
        $rapport=$data;
        $rapport[3][10]="Message";
        // - Insertion dans la table
        foreach($data as $key => $element) {
            $error = false;
            // - On retire les entetes
            if($key<=3) continue;
            $nom_complet=$element[0];
            $regime=$element[1];
            $contact=$element[2];
            $email=$element[3];
            $pays=$element[4];
            $adresse_postale=$element[5];
            $numero_fax=$element[6];
            $adresse_geo=$element[7];
            $ncc=$element[8];
            $montant_plafond=$element[9];
            // - Validation des donnée fournies
            if(empty($nom_complet)){
                $rapport[$key][10]="Veuillez remplir la cellule Nom complet ou raison sociale du client.";
                $error = true;
            }
            if(empty($regime)){
                $rapport[$key][10]="Veuillez choisir le régime du client.";
                $error = true;
            }
            if(empty($contact)){
                $rapport[$key][10]="Veuillez saisir le contact du client.";
                $error = true;
            }
            if(!empty($email)){
                $v = Validator::make(['email'=> $email],['email' => 'email']);
                if ($v->fails()) {
                    $rapport[$key][10]="Veuillez saisir une adresse email valide.";
                    $error = true;
                }
            }
            $pays = Nation::where('libelle_nation', $pays)->first();
            if(empty($pays)){
                $rapport[$key][10]="Le pays choisi est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des pays.";
                $error = true;
            }
            $regime = Regime::where('libelle_regime', $regime)->first();
            if(empty($regime)){
                $rapport[$key][10]="Le régime choisi est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des régimes.";
                $error = true;
            }
            if($error) continue;
            // - On vérifie si l'utilisateur n'existe pas déjà
            $data = Client::where('full_name_client', $nom_complet)
            ->where('nation_id', $pays->id)
            ->exists();
            if($data){
                $rapport[$key][10]="Ce client existe déjà dans la base.";
                continue;
            }

            // - Création du client
            // - Création code du client
            $maxIdTable = DB::table('clients')->max('id');
            $idClient = $maxIdTable + 1;
            $caractere_speciaux = array("'", "-", " ");
            $code_client = '411' . substr(strtoupper(str_replace($caractere_speciaux, '', $nom_complet)), 0, 3) . $idClient;
            Client::create([
                'code_client' => $code_client,
                'full_name_client' => $nom_complet,
                'contact_client' => $contact,
                'nation_id' => $pays->id,
                'regime_id' => $regime->id,
                'email_client' => $email,
                'plafond_client' => abs($montant_plafond),
                'compte_contribuable_client' => $ncc,
                'boite_postale_client' => $adresse_postale,
                'adresse_client' => $adresse_geo,
                'fax_client' => $numero_fax,
                'created_by' => Auth::user()->id,
            ]);

            $rapport[$key][10]="OK";
        }
        // - Téléchargement du fichier
        return response()->json([
            'error'     => false,
            'message'   => "Veuillez consulter le rapport.",
            'isFile'    => true,
            'filename'  => "Rapport de Création de clients en Masse",
            'data'      => $rapport,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return Response
     */
    public function destroy(Client $client)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($client) {
            try {

                $client->update(['deleted_by' => Auth::user()->id]);
                $client->delete();
                $jsonData["data"] = json_decode($client);
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
