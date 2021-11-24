<?php

namespace App\Http\Controllers\Stock;

use Image;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Stock\Article;
use Illuminate\Http\Response;
use App\Models\Stock\DepotArticle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Parametre\Categorie;
use App\Models\Parametre\ParamTva;
use App\Models\Stock\Depot;
use Illuminate\Support\Facades\Auth;
use Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->get();
        $param_tvas = DB::table('param_tvas')->Where('deleted_at', null)->get();
        $rangees = DB::table('rangees')->Where('deleted_at', null)->orderBy('libelle_rangee', 'asc')->get();
        $rayons = DB::table('rayons')->Where('deleted_at', null)->orderBy('libelle_rayon', 'asc')->get();
        $unites = DB::table('unites')->Where('deleted_at', null)->orderBy('libelle_unite', 'asc')->get();
        $tailles = DB::table('tailles')->Where('deleted_at', null)->orderBy('libelle_taille', 'asc')->get();
        $categories = DB::table('categories')->Where([['deleted_at', null], ['categorie_id', null]])->orderBy('libelle_categorie', 'asc')->get();
        $sous_categories = DB::table('categories')->Where([['deleted_at', null], ['categorie_id', '!=', null]])->orderBy('libelle_categorie', 'asc')->get();
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();

        $menuPrincipal = "Stock";
        $titleControlleur = "Article";
        $btnModalAjout = "TRUE";

        return view('stock.article.index', compact('categories', 'sous_categories', 'fournisseurs', 'depots', 'tailles', 'rayons', 'unites', 'rangees', 'param_tvas', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeArticle()
    {
        $articles = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente')
            ->join('depot_articles', 'depot_articles.article_id', '=', 'articles.id')
            ->select('articles.*', 'depot_articles.prix_vente_detail as prix_vente_ttc')
            ->Where('articles.deleted_at', null)
            ->orderBy('libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByCategorie($categorie)
    {
        $articles = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente')
            ->select('articles.*')
            ->Where([['articles.deleted_at', null], ['categorie_id', $categorie]])
            ->orderBy('libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleBySousCategorie($sousCategorie)
    {
        $articles = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente')
            ->select('articles.*')
            ->Where([['articles.deleted_at', null], ['sous_categorie_id', $sousCategorie]])
            ->orderBy('libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByLibelle($libelle)
    {
        $articles = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente')
            ->select('articles.*')
            ->Where([['articles.deleted_at', null], ['articles.libelle_article', 'like', '%' . $libelle . '%']])
            ->orderBy('libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByCode($code)
    {
        $articles = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente')
            ->select('articles.*')
            ->Where('articles.deleted_at', null)
            ->whereJsonContains('code_barre', $code)
            ->orderBy('libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function getArticleById($id)
    {
        $article = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente')
            ->join('depot_articles', 'depot_articles.article_id', '=', 'articles.id')
            ->select('articles.*', 'depot_articles.quantite_disponible as qteEnStock')
            ->Where([['articles.deleted_at', null], ['articles.id', $id]])
            ->orderBy('libelle_article', 'ASC')
            ->get();
        $jsonData["rows"] = $article->toArray();
        $jsonData["total"] = $article->count();
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
        if ($request->isMethod('post') && $request->input('libelle_article')) {

            $data = $request->all();

            try {

                if (isset($data['code_barre'][0]) && !empty($data['code_barre'][0])) {
                    //Verification des dubblons sur les codes barres
                    foreach (explode(",", $data['code_barre'][0]) as $codebarre) {
                        $ArticleCode = Article::Where('code_barre', $codebarre)->first();
                    }
                    if ($ArticleCode != null) {
                        return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base, vérifier les codes barres", "data" => null]);
                    }
                }

                //Verification des dubblons sur le non de l'article
                $Article = Article::where('libelle_article', $data['libelle_article'])->first();
                if ($Article != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base, vérifier le nom de l'article", "data" => null]);
                }

                $article = new Article;
                $article->libelle_article = $data['libelle_article'];
                //$article->slug = Str::slug($data['libelle_article']);
                $article->categorie_id = isset($data['categorie_id']) && !empty($data['categorie_id']) ? $data['categorie_id'] : null;
                $article->prix_achat_ttc = $data['prix_achat_ttc'];
                $article->param_tva_id = $data['param_tva_id'];
                $article->code_barre = isset($data['code_barre'][0]) && !empty($data['code_barre'][0]) ? $data['code_barre'][0] : "";
                $article->code_article = isset($data['code_article']) && !empty($data['code_article']) ? $data['code_article'] : "";
                $article->fournisseurs = isset($data['fournisseurs']) && !empty($data['fournisseurs']) ? array_map(function ($id) {
                    return intval($id);
                }, $data['fournisseurs']) : [];
                $article->sous_categorie_id = isset($data['sous_categorie_id']) && !empty($data['sous_categorie_id']) ? $data['sous_categorie_id'] : null;
                $article->unite_id = isset($data['unite_id']) && !empty($data['unite_id']) ? $data['unite_id'] : null;
                $article->taille_id = isset($data['taille_id']) && !empty($data['taille_id']) ? $data['taille_id'] : null;
                $article->rayon_id = isset($data['rayon_id']) && !empty($data['rayon_id']) ? $data['rayon_id'] : null;
                $article->rangee_id = isset($data['rangee_id']) && !empty($data['rangee_id']) ? $data['rangee_id'] : null;
                $article->poids_net = isset($data['poids_net']) && !empty($data['poids_net']) ? $data['poids_net'] : null;
                $article->poids_brut = isset($data['poids_brut']) && !empty($data['poids_brut']) ? $data['poids_brut'] : null;
                $article->stock_mini = isset($data['stock_mini']) && !empty($data['stock_mini']) ? $data['stock_mini'] : null;
                $article->stock_max = isset($data['stock_max']) && !empty($data['stock_max']) ? $data['stock_max'] : null;
                $article->volume = isset($data['volume']) && !empty($data['volume']) ? $data['volume'] : null;
                $article->non_stockable = isset($data['non_stockable']) ? TRUE : FALSE;

                //Ajout de l'image de l'article s'il y a en
                if (isset($data['image_article'])) {
                    $name = Str::slug($data['libelle_article'] . date('dmY')) . '.jpg';

                    $img = Image::make($data['image_article']);
                    $img->resize(128, 128);

                    $path = public_path() . '/img/article/';

                    $img->save($path . $name, 60);
                    //$image_article->move($path, $file_name);
                    $article->image_article = '/img/article/' . $name;
                }
                $article->created_by = Auth::user()->id;
                $article->save();

                //Création de l'article dans les dépôts avec leur prix par rapport au dépôt
                if ($article && !empty($data['depots'])) {
                    $depots = $data["depots"];
                    $prix_v_detail = $data["prix_details"];
                    $prix_v_demi_gros = $data["prix_details"];
                    $prix_v_gros = $data["prix_details"];

                    foreach ($depots as $index => $depot) {
                        $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $article->id]])->first();

                        if (!$DepotArticle) {
                            $depotArticle = new DepotArticle;
                            $depotArticle->article_id = $article->id;
                            $depotArticle->depot_id = $depot;
                            $depotArticle->prix_vente_detail = $prix_v_detail[$index];
                            $depotArticle->prix_vente_gros = $prix_v_gros[$index];
                            $depotArticle->prix_vente_demi_gros = $prix_v_demi_gros[$index];
                            $depotArticle->created_by = Auth::user()->id;
                            $depotArticle->save();
                        }
                    }
                }
                $jsonData["data"] = json_decode($article);
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
        $spreadsheet = $reader->load(storage_path("modeles/modele-importation-en-masse-article.xlsx"));
        $worksheet=$spreadsheet->getActiveSheet();
        // - Couleur des titres de la feuille
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A2')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FF8EA9DB');
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A4:K4')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FF8EA9DB');
        // - Validation depot
        $validation = $spreadsheet->getActiveSheet()->getCell('B2')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir un depot de la liste');
        $validation->setPromptTitle('Liste des depots disponibles');
        $validation->setPrompt("Veuillez choisir le depot de l'article");
        $depots = Depot::orderBy('libelle_depot')
        ->get()
        ->pluck('libelle_depot')
        ->implode(',');
        $validation->setFormula1('"'.$depots.'"');

        // - Validation catégorie
        $validation = $spreadsheet->getActiveSheet()->getCell('B5')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir une catégorie de la liste');
        $validation->setPromptTitle('Liste des catégories disponibles');
        $validation->setPrompt("Veuillez choisir la catégorie de l'article");
        $categories = Categorie::orderBy('libelle_categorie')
        ->get()
        ->pluck('libelle_categorie')
        ->implode(',');
        $validation->setFormula1('"'.$categories.'"');
        $worksheet->setDataValidation('B5:B10000', $validation);

        // - Validation tva
        $validation = $spreadsheet->getActiveSheet()->getCell('C5')->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur de saisie');
        $validation->setError('Veuillez choisir une TVA de la liste');
        $validation->setPromptTitle('Liste des TVA disponibles');
        $validation->setPrompt("Veuillez choisir la TVA de l'article");
        $param_tvas = ParamTva::orderBy('tva')
        ->get()
        ->pluck('tva')
        ->implode(',');
        $validation->setFormula1('"'.$param_tvas.'"');
        $worksheet->setDataValidation('C5:C10000', $validation);

        return $this->download($spreadsheet,"Modele d'importation en masse des articles ".now()->toDateString()." à ".now()->toTimeString());
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
        $rapport[4][11]="Message";
        // - Insertion dans la table
        $depot=$data[1][1];
        $depot = Depot::whereSlug(Str::slug($depot))->first();
        if(!$depot){
            $rapport[1][2]=" <= Le dépot choisi est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des dépots.";
            // - Téléchargement du fichier
            return response()->json([
                'error'     => false,
                'message'   => "Veuillez consulter le rapport.",
                'isFile'    => true,
                'filename'  => "Rapport de Création d'Article en Masse",
                'data'      => $rapport,
            ], 200);
        }

        foreach($data as $key => $element) {
            $error = false;
            // - On retire les entetes
            if($key<=3) continue;
            $libelle=$element[0];
            $categorie=$element[1];
            $tva=$element[2];
            $prix_achat_ttc=$element[3];
            $prix_achat_ht=$element[4];
            $stock_minimum=$element[5];
            $stock_maximum=$element[6];
            $article_non_stockable=$element[7];
            $prix_detail_depot=$element[8];
            $prix_demi_gros_depot=$element[9];
            $prix_gros_depot=$element[10];
            // - Validation des donnée fournies
            if(empty($libelle)){
                $rapport[$key][11]="Veuillez remplir la cellule Libellé de l'article.";
                $error = true;
            }
            if(empty($categorie)){
                $rapport[$key][11]="Veuillez choisir la catégorie de l'article.";
                $error = true;
            }
            if(empty($categorie)){
                $rapport[$key][11]="Veuillez choisir la catégorie de l'article.";
                $error = true;
            }
            $categorie = Categorie::where('libelle_categorie', $categorie)->first();
            if(empty($categorie)){
                $rapport[$key][11]="La catégorie choisie est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des catégories.";
                $error = true;
            }
            if(empty($tva)){
                $rapport[$key][11]="Veuillez choisir le TVA de l'article.";
                $error = true;
            }
            $tva = ParamTva::where('tva', $tva)->first();
            // $tva = ParamTva::where('tva', (abs($tva) / 100))->first();
            if(empty($tva)){
                $rapport[$key][11]="La TVA choisie est invalide. Vous devez télécharger à nouveau le modèle d'importation pour avoir la dernière mise à jour de la liste des TVA.";
                $error = true;
            }
            if(empty($prix_achat_ttc)){
                $rapport[$key][11]="Veuillez saisir le prix d'achat TTC de l'article.";
                $error = true;
            }
            if(empty($prix_achat_ht)){
                $rapport[$key][11]="Veuillez saisir le prix d'achat HT de l'article.";
                $error = true;
            }
            if(empty($stock_minimum)){
                $rapport[$key][11]="Veuillez saisir le stock minimum de l'article.";
                $error = true;
            }
            if(empty($stock_maximum)){
                $rapport[$key][11]="Veuillez saisir le stock maximum de l'article.";
                $error = true;
            }
            if(empty($prix_detail_depot)){
                $rapport[$key][11]="Veuillez saisir le prix en détail en dépot de l'article.";
                $error = true;
            }
            if(empty($prix_demi_gros_depot)){
                $rapport[$key][11]="Veuillez saisir le prix demi gros en dépot de l'article.";
                $error = true;
            }
            if(empty($prix_gros_depot)){
                $rapport[$key][11]="Veuillez saisir le prix gros en dépot de l'article.";
                $error = true;
            }
            if($error) continue;
            // - On vérifie si l'utilisateur n'existe pas déjà
            $data = Article::whereSlug(Str::slug($libelle))->exists();
            if($data){
                $rapport[$key][11]="Ce article existe déjà dans la base.";
                continue;
            }

            // - Création de l'article
            $article = Article::create([
                'libelle_article' => $libelle,
                'slug' => Str::slug($libelle),
                'categorie_id' => $categorie->id,
                'prix_achat_ttc' => $prix_achat_ttc,
                'param_tva_id' => $tva->id,
                'code_barre' => "",
                'fournisseurs' => "",
                'sous_categorie_id' => null,
                'unite_id' => null,
                'taille_id' => null,
                'rayon_id' => null,
                'rangee_id' => null,
                'poids_net' => null,
                'poids_brut' => null,
                'stock_mini' => abs($stock_minimum),
                'stock_max' => abs($stock_maximum),
                'volume' => null,
                'non_stockable' => $article_non_stockable == "oui" ? 1 : 0,
                'created_by' => Auth::user()->id,
            ]);

            // - Association de l'article au dépot
            DepotArticle::create([
                'article_id' => $article->id,
                'depot_id' => $depot->id,
                'prix_vente_detail' => $prix_detail_depot,
                'prix_vente_gros' => $prix_gros_depot,
                'prix_vente_demi_gros' => $prix_demi_gros_depot,
                'created_by' => Auth::user()->id,
            ]);

            $rapport[$key][11]="OK";
        }
        // - Téléchargement du fichier
        return response()->json([
            'error'     => false,
            'message'   => "Veuillez consulter le rapport.",
            'isFile'    => true,
            'filename'  => "Rapport de Création d'Article en Masse",
            'data'      => $rapport,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Article  $article
     * @return Response
     */
    public function updateArticle(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all();
        $article = Article::find($data['idArticle']);
        if ($article) {

            try {

                $article->libelle_article = $data['libelle_article'];
                $article->categorie_id = isset($data['categorie_id']) && !empty($data['categorie_id']) ? $data['categorie_id'] : null;
                $article->prix_achat_ttc = $data['prix_achat_ttc'];
                $article->param_tva_id = $data['param_tva_id'];
                $article->code_barre = isset($data['code_barre'][0]) && !empty($data['code_barre'][0]) ? explode(",", $data['code_barre'][0]) : [];
                $article->fournisseurs = isset($data['fournisseurs']) && !empty($data['fournisseurs']) ? array_map(function ($id) {
                    return intval($id);
                }, $data['fournisseurs']) : [];
                $article->sous_categorie_id = isset($data['sous_categorie_id']) && !empty($data['sous_categorie_id']) ? $data['sous_categorie_id'] : null;
                $article->unite_id = isset($data['unite_id']) && !empty($data['unite_id']) ? $data['unite_id'] : null;
                $article->taille_id = isset($data['taille_id']) && !empty($data['taille_id']) ? $data['taille_id'] : null;
                $article->rayon_id = isset($data['rayon_id']) && !empty($data['rayon_id']) ? $data['rayon_id'] : null;
                $article->rangee_id = isset($data['rangee_id']) && !empty($data['rangee_id']) ? $data['rangee_id'] : null;
                $article->poids_net = isset($data['poids_net']) && !empty($data['poids_net']) ? $data['poids_net'] : null;
                $article->poids_brut = isset($data['poids_brut']) && !empty($data['poids_brut']) ? $data['poids_brut'] : null;
                $article->stock_mini = isset($data['stock_mini']) && !empty($data['stock_mini']) ? $data['stock_mini'] : null;
                $article->stock_max = isset($data['stock_max']) && !empty($data['stock_max']) ? $data['stock_max'] : null;
                $article->volume = isset($data['volume']) && !empty($data['volume']) ? $data['volume'] : null;
                $article->non_stockable = isset($data['non_stockable']) ? TRUE : FALSE;

                //Ajout de l'image de l'article s'il y a en
                if (isset($data['image_article'])) {

                    $name = Str::slug($data['libelle_article'] . date('dmY')) . '.jpg';

                    $img = Image::make($data['image_article']);
                    $img->resize(128, 128);

                    $path = public_path() . '/img/article/';

                    $img->save($path . $name, 60);
                    //$image_article->move($path, $file_name);
                    $article->image_article = '/img/article/' . $name;
                }
                $article->updated_by = Auth::user()->id;
                $article->save();

                $jsonData["data"] = json_decode($article);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Echec de modification", "data" => null]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return Response
     */
    public function destroy(Article $article)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($article) {
            try {

                $depotArticles = DepotArticle::where('article_id', $article->id)->get();

                foreach ($depotArticles as $depotArticle) {
                    $depotArticle->delete();
                }

                $article->update(['deleted_by' => Auth::user()->id]);
                $article->delete();
                $jsonData["data"] = json_decode($article);
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
