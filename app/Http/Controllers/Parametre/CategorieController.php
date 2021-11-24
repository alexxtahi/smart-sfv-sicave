<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Categorie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Validator;
use function response;
use function view;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Catégorie";
        $btnModalAjout = "FALSE";
        return view('parametre.categorie.index', compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function sousCategorie()
    {
        $categories = DB::table('categories')->select('categories.*')->Where([['deleted_at', null], ['categorie_id', null]])->orderBy('categories.libelle_categorie', 'ASC')->get();
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Sous catégorie";
        $btnModalAjout = "FALSE";
        return view('parametre.sous-categorie.index', compact('categories', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeCategorie()
    {
        $categories = DB::table('categories')->select('categories.*')->Where([['deleted_at', null], ['categorie_id', null]])->orderBy('categories.libelle_categorie', 'ASC')->get();

        $jsonData["rows"] = $categories->toArray();
        $jsonData["total"] = $categories->count();

        return response()->json($jsonData);
    }

    public function listeSousCategorie()
    {
        $sous_categories = Categorie::with('categorie')
            ->select('categories.*')
            ->Where([['categories.deleted_at', null], ['categories.categorie_id', '!=', null]])
            ->orderBy('categories.libelle_categorie', 'ASC')
            ->get();

        $jsonData["rows"] = $sous_categories->toArray();
        $jsonData["total"] = $sous_categories->count();
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
        if ($request->isMethod('post') && $request->input('libelle_categorie')) {

            $data = $request->all();

            try {

                $categorie = $data['id'] ? Categorie::findOrFail($data['id']) : new Categorie;

                $categorie->libelle_categorie = $data['libelle_categorie'];
                $categorie->categorie_id = isset($data['categorie_id']) && !empty($data['categorie_id']) ? $data['categorie_id'] : null;
                $categorie->created_by = Auth::user()->id;
                $categorie->save();

                $jsonData["data"] = json_decode($categorie);
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
        $rapport[0][8]="Message";
        // - Insertion dans la table
        foreach($data as $key => $element) {
            // - On retire l'entete
            if($key==0) continue;
            $libelle=$element[0];
            // - Validation des donnée fournies
            if(empty($libelle)){
                $rapport[$key][8]="Veuillez remplir la cellule Nom de la catégorie.";
                continue;
            }
            // - On vérifie si l'utilisateur n'existe pas déjà
            $data = Categorie::whereSlug(Str::slug($libelle))->exists();
            if($data){
                $rapport[$key][8]="Cette catégorie existe déjà.";
                continue;
            }
            // - On crée la catégorie
            Categorie::create([
                'libelle_categorie' => $libelle,
                'slug' => Str::slug($libelle),
                'created_by' => Auth::user()->id
            ]);
            $rapport[$key][8]="OK";
        }
        // - Téléchargement du fichier
        return response()->json([
            'error'     => false,
            'message'   => "Veuillez consulter le rapport.",
            'isFile'    => true,
            'filename'  => "Rapport Création de Catégorie en Masse",
            'data'      => $rapport,
        ], 200);
    }


    public function downloadModelSousCategorie () {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(storage_path("modeles/modele-importation-en-masse-sous-categorie.xlsx"));
        $worksheet=$spreadsheet->getActiveSheet();
        // - Couleur des titres de la feuille
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A4:B4')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FF8EA9DB');

        // - Validation catégorie
        $validation = $spreadsheet->getActiveSheet()->getCell('A5')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir une catégorie de la liste');
        $validation->setPromptTitle('Liste des catégories disponibles');
        $validation->setPrompt("Veuillez choisir la catégorie parent");
        $categories = Categorie::orderBy('libelle_categorie')
        ->get()
        ->pluck('libelle_categorie')
        ->implode(',');
        $validation->setFormula1('"'.$categories.'"');
        $worksheet->setDataValidation('A5:A10000', $validation);

        return $this->download($spreadsheet,"Modele d'importation en masse des sous catégories ".now()->toDateString()." à ".now()->toTimeString());
    }


    public function storeFromUploadSousCategorie(Request $request){
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
        $rapport[3][3]="Message";
        // - Insertion dans la table
        foreach($data as $key => $element) {
            $error = false;
            // - On retire les entetes
            if($key<=3) continue;
            $categorie=$element[0];
            $sous_categorie=$element[1];
            // - Validation des données fournies
            if(!empty($categorie)){
                $categorie = Categorie::whereSlug($categorie)->first();
                if (!$categorie) {
                    $rapport[$key][3]="La catégorie choisie est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des catégories.";
                    $error = true;
                }
            }
            if(empty($sous_categorie)){
                $rapport[$key][3]="Veuillez saisir le libellé de la sous catégorie.";
                $error = true;
            }
            if($error) continue;
            // - On vérifie si la catégorie n'existe pas déjà
            $data = Categorie::whereSlug(Str::slug($sous_categorie))->exists();
            if($data){
                $rapport[$key][3]="Cette catégorie existe déjà dans la base.";
                continue;
            }

            // - Création code du fournisseur
            Categorie::create([
                'categorie_id' => $categorie->id,
                'libelle_categorie' => $sous_categorie,
                'slug' => Str::slug($sous_categorie),
                'created_by' => Auth::user()->id
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
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $categorie = Categorie::find($id);
        if ($categorie) {
            try {

                $categorie->update(['deleted_by' => Auth::user()->id]);
                $categorie->delete();

                $jsonData["data"] = json_decode($categorie);

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
