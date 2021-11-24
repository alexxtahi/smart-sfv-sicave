<?php

namespace App\Http\Controllers\Crm;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\Crm\Fournisseur;
use App\Models\Parametre\Banque;
use App\Models\Parametre\Nation;
use App\Models\Stock\ArticleBon;
use App\Models\Stock\BonCommande;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $banques = DB::table('banques')->Where('deleted_at', null)->orderBy('libelle_banque', 'asc')->get();
        $nations = DB::table('nations')->Where('deleted_at', null)->orderBy('libelle_nation', 'asc')->get();

        $menuPrincipal = "CRM";
        $titleControlleur = "Fournisseur";
        $btnModalAjout = "TRUE";
        return view('crm.fournisseur.index', compact('nations', 'banques', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function ficheFournisseur($fournisseur)
    {
        $infoFournisseur = Fournisseur::find($fournisseur);
        $menuPrincipal = "Fiche fournisseur";
        $titleControlleur = "";
        $btnModalAjout = "FALSE";
        return view('crm.fournisseur.fiche', compact('infoFournisseur', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeFournisseur()
    {
        $fournisseurs = Fournisseur::with('nation', 'banque')
            ->select('fournisseurs.*')
            ->Where('deleted_at', null)
            ->orderBy('full_name_fournisseur', 'ASC')
            ->get();
        $jsonData["rows"] = $fournisseurs->toArray();
        $jsonData["total"] = $fournisseurs->count();
        return response()->json($jsonData);
    }

    public function listeFournisseurByNation($nation)
    {
        $fournisseurs = Fournisseur::with('nation', 'banque')
            ->select('fournisseurs.*')
            ->Where([['deleted_at', null], ['nation_id', $nation]])
            ->orderBy('full_name_fournisseur', 'ASC')
            ->get();
        $jsonData["rows"] = $fournisseurs->toArray();
        $jsonData["total"] = $fournisseurs->count();
        return response()->json($jsonData);
    }

    public function listeBonFournisseur($fournisseur){
        $bon_commandes =  BonCommande::with('fournisseur')
                                        ->join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
                                        ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantBonRecu'))
                                        ->where([['bon_commandes.deleted_at', null], ['etat', 5], ['fournisseur_id', $fournisseur]])
                                        ->orderBy('bon_commandes.date_bon', 'DESC')
                                        ->groupBy('bon_commandes.id')
                                        ->get();
        $jsonData["rows"] = $bon_commandes->toArray();
        $jsonData["total"] = $bon_commandes->count();
        return response()->json($jsonData);
    }

    public function listeTousBonFournisseur($fournisseur){
        $totalTousBon = 0; $totalBonRecu = 0; $totalAcompte = 0;
        $bon_commandes =  BonCommande::with('fournisseur')
                                        ->join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
                                        ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantBonRecu'),DB::raw('sum(article_bons.quantite_demande*article_bons.prix_article) as montantBonDemande'),DB::raw('DATE_FORMAT(bon_commandes.date_bon, "%d-%m-%Y") as date_bons'))
                                        ->where([['bon_commandes.deleted_at', null],['bon_commandes.fournisseur_id', $fournisseur]])
                                        ->orderBy('bon_commandes.date_bon', 'DESC')
                                        ->groupBy('bon_commandes.id')
                                        ->get();

        foreach($bon_commandes as $bon_commande){
            $totalTousBon = $totalTousBon + ($bon_commande->montantBonRecu + $bon_commande->montantBonDemande);
            $totalBonRecu = $totalBonRecu + $bon_commande->montantBonRecu;
            $totalAcompte = $totalAcompte + $bon_commande->accompte;
        }
        $jsonData["rows"] = $bon_commandes->toArray();
        $jsonData["total"] = $bon_commandes->count();
        $jsonData["totalTousBon"] = $totalTousBon;
        $jsonData["totalBonRecu"] = $totalBonRecu;
        $jsonData["totalAcompte"] = $totalAcompte;
        return response()->json($jsonData);
    }

    public function listeArticleCommandeByFournisseur($fournisseur){
        $articles = ArticleBon::with('article')
                                ->join('articles','articles.id','=','article_bons.article_id')
                                ->join('bon_commandes','bon_commandes.id','=','article_bons.bon_commande_id')
                                ->select('articles.libelle_article','article_bons.prix_article',DB::raw('sum(article_bons.quantite_demande) as qteTotaleDem'),DB::raw('sum(article_bons.quantite_recu) as qteTotaleRec'))
                                ->Where([['article_bons.deleted_at', NULL],['bon_commandes.fournisseur_id',$fournisseur]])
                                ->groupBy('articles.id')
                                ->orderBy('qteTotaleDem','DESC')
                                ->take(10)->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeSoldeFournisseur(){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $bon_commandes = BonCommande::with('fournisseur')
                         ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                         ->select('bon_commandes.*','bon_commandes.accompte',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                         ->Where([['bon_commandes.deleted_at', NULL],['etat',5]])
                         ->groupBy('bon_commandes.id')
                         ->get();
         foreach ($bon_commandes as $bon_commande){
             $MontantTotalDu = $MontantTotalDu + $bon_commande->montantCommande;
             $MontantTotalAcompte = $MontantTotalAcompte + $bon_commande->accompte;
         }
         $jsonData["rows"] = $bon_commandes->toArray();
         $jsonData["total"] = $bon_commandes->count();
         $jsonData["MontantTotalDu"] = $MontantTotalDu;
         $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
         return response()->json($jsonData);
    }

    public function listeSoldeByFournisseur($fournisseur){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $bon_commandes = BonCommande::with('fournisseur')
                         ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                         ->select('bon_commandes.*','bon_commandes.accompte',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                         ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.fournisseur_id',$fournisseur],['etat',5]])
                         ->groupBy('bon_commandes.id')
                         ->get();
         foreach ($bon_commandes as $bon_commande){
             $MontantTotalDu = $MontantTotalDu + $bon_commande->montantCommande;
             $MontantTotalAcompte = $MontantTotalAcompte + $bon_commande->accompte;
         }
         $jsonData["rows"] = $bon_commandes->toArray();
         $jsonData["total"] = $bon_commandes->count();
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
        if ($request->isMethod('post') && $request->input('full_name_fournisseur')) {

            $data = $request->all();

            try {

                $fournisseur = $data['id'] ? Fournisseur::findOrFail($data['id']) : new Fournisseur;

                if (empty($data['id'])) {
                    //Création code du fournisseur
                    $maxIdTable = DB::table('fournisseurs')->max('id');
                    $idFournisseur = $maxIdTable + 1;
                    $caractere_speciaux = array("'", "-", " ");
                    $code_fournisseur = '401' . substr(strtoupper(str_replace($caractere_speciaux, '', $data['full_name_fournisseur'])), 0, 3) . $idFournisseur;
                    $fournisseur->code_fournisseur = $code_fournisseur;
                }

                $fournisseur->full_name_fournisseur = $data['full_name_fournisseur'];
                $fournisseur->contact_fournisseur = $data['contact_fournisseur'];
                $fournisseur->nation_id = $data['nation_id'];
                $fournisseur->email_fournisseur = isset($data['email_fournisseur']) && !empty($data['email_fournisseur']) ? $data['email_fournisseur'] : null;
                $fournisseur->banque_id = isset($data['banque_id']) && !empty($data['banque_id']) ? $data['banque_id'] : null;
                $fournisseur->compte_banque_fournisseur = isset($data['compte_banque_fournisseur']) && !empty($data['compte_banque_fournisseur']) ? $data['compte_banque_fournisseur'] : null;
                $fournisseur->compte_contribuable_fournisseur = isset($data['compte_contribuable_fournisseur']) && !empty($data['compte_contribuable_fournisseur']) ? $data['compte_contribuable_fournisseur'] : null;
                $fournisseur->boite_postale_fournisseur = isset($data['boite_postale_fournisseur']) && !empty($data['boite_postale_fournisseur']) ? $data['boite_postale_fournisseur'] : null;
                $fournisseur->adresse_fournisseur = isset($data['adresse_fournisseur']) && !empty($data['adresse_fournisseur']) ? $data['adresse_fournisseur'] : null;
                $fournisseur->fax_fournisseur = isset($data['fax_fournisseur']) && !empty($data['fax_fournisseur']) ? $data['fax_fournisseur'] : null;
                $fournisseur->created_by = Auth::user()->id;
                $fournisseur->save();
                $jsonData["data"] = json_decode($fournisseur);
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
        $spreadsheet = $reader->load(storage_path("modeles/modele-importation-en-masse-fournisseur.xlsx"));
        $worksheet=$spreadsheet->getActiveSheet();
        // - Couleur des titres de la feuille
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A4:J4')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FF8EA9DB');

        // - Validation banque
        $validation = $spreadsheet->getActiveSheet()->getCell('G5')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir une banque de la liste');
        $validation->setPromptTitle('Liste des banques disponibles');
        $validation->setPrompt("Veuillez choisir la banque du client");
        $banques = Banque::orderBy('libelle_banque')
        ->get()
        ->pluck('libelle_banque')
        ->implode(',');
        $validation->setFormula1('"'.$banques.'"');
        $worksheet->setDataValidation('G5:G10000', $validation);

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

        return $this->download($spreadsheet,"Modele d'importation en masse des fournisseurs ".now()->toDateString()." à ".now()->toTimeString());
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
            $ncc=$element[1];
            $contact=$element[2];
            $email=$element[3];
            $pays=$element[4];
            $adresse_postale=$element[5];
            $banque=$element[6];
            $compte_banque=$element[7];
            $numero_fax=$element[8];
            $adresse_geo=$element[9];
            // - Validation des donnée fournies
            if(empty($nom_complet)){
                $rapport[$key][10]="Veuillez remplir la cellule Nom complet ou raison sociale du client.";
                $error = true;
            }
            if(empty($contact)){
                $rapport[$key][10]="Veuillez saisir le contact du client.";
                $error = true;
            }
            if(!empty($pays)){
                $pays = Nation::where('libelle_nation', $pays)->first();
                if (!$pays) {
                    $rapport[$key][10]="Le pays choisi est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des pays.";
                    $error = true;
                }
            }
            if(!empty($banque)){
                $banque = Banque::where('libelle_banque', $banque)->first();
                if (!$banque) {
                    $rapport[$key][10]="La banque choisi est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des banques.";
                    $error = true;
                }
            }
            if($error) continue;
            // - On vérifie si l'utilisateur n'existe pas déjà
            $data = Fournisseur::where('full_name_fournisseur', $nom_complet)
            ->where('nation_id', $pays->id)
            ->exists();
            if($data){
                $rapport[$key][10]="Ce client existe déjà dans la base.";
                continue;
            }

            // - Création code du fournisseur
            $maxIdTable = DB::table('fournisseurs')->max('id');
            $idFournisseur = $maxIdTable + 1;
            $caractere_speciaux = array("'","-"," ");
            $code_fournisseur = '401'.substr(strtoupper(str_replace($caractere_speciaux,'', $nom_complet)), 0, 3).$idFournisseur;

            Fournisseur::create([
                'code_fournisseur' => $code_fournisseur,
                'full_name_fournisseur' => $nom_complet,
                'contact_fournisseur' => $contact,
                'nation_id' => empty($pays) ? null : $pays->id,
                'email_fournisseur' => $email,
                'banque_id' => empty($banque) ? null : $banque->id,
                'compte_banque_fournisseur' => $compte_banque,
                'compte_contribuable_fournisseur' => $ncc,
                'boite_postale_fournisseur' => $adresse_postale,
                'adresse_fournisseur' => $adresse_geo,
                'fax_fournisseur' => $numero_fax,
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
     * @param  \App\Fournisseur  $fournisseur
     * @return Response
     */
    public function destroy(Fournisseur $fournisseur)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($fournisseur) {
            try {

                $fournisseur->update(['deleted_by' => Auth::user()->id]);
                $fournisseur->delete();
                $jsonData["data"] = json_decode($fournisseur);
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
