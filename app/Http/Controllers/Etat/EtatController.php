<?php

namespace App\Http\Controllers\Etat;

use App\Http\Controllers\Controller;
use App\Models\Stock\Approvisionnement;
use App\Models\Stock\Destockage;
use App\Models\Stock\Article;
use App\Models\Parametre\Categorie;
use App\Models\Crm\Client;
use App\Models\Stock\Depot;
use App\Models\Crm\Fournisseur;
use App\Models\Parametre\Nation;
use App\Models\Parametre\Unite;
use App\Models\Stock\Inventaire;
use App\Models\Stock\TransfertStock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EtatController extends Controller
{
    public function vuApprovisionnement()
    {
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', null)->orderBy('full_name_fournisseur', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Approvisionnement";
        $btnModalAjout = "FALSE";
        return view('etat.approvisionnement', compact('fournisseurs', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuArticle()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $categories = DB::table('categories')->Where([['deleted_at', null], ['categorie_id', null]])->orderBy('libelle_categorie', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Article";
        $btnModalAjout = "FALSE";
        return view('etat.article', compact('depots', 'categories', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuFournisseur()
    {
        $nations = DB::table('nations')->Where('deleted_at', null)->orderBy('libelle_nation', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Fournisseur";
        $btnModalAjout = "FALSE";
        return view('etat.fournisseur', compact('nations', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuClient()
    {
        $nations = DB::table('nations')->Where('deleted_at', null)->orderBy('libelle_nation', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Client";
        $btnModalAjout = "FALSE";
        return view('etat.client', compact('nations', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuDepot()
    {
        $menuPrincipal = "Etat";
        $titleControlleur = "Dépôt";
        $btnModalAjout = "FALSE";
        return view('etat.depot', compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuInventaire(){
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Inventaire";
        $btnModalAjout = "FALSE";
        return view('etat.inventaire', compact('depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuDestockage(){
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Etat";
        $titleControlleur = "Déstockage";
        $btnModalAjout = "FALSE";
        return view('etat.destockage', compact('depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuTransfertStock(){
        $menuPrincipal = "Etat";
        $titleControlleur = "Transfert de stock";
        $btnModalAjout = "FALSE";
        return view('etat.transfert-stock', compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuBonCommande(){
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', NULL)->orderBy('full_name_fournisseur', 'asc')->get();

        $menuPrincipal = "Etat";
        $titleControlleur = "Bon de commande";
        $btnModalAjout = "FALSE";
        return view('etat.bon-commande', compact('fournisseurs','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    // ***** Les Etats ***** //

    //Approvisionnement PDF
    public function listeApprovisionnementPdf()
    {
        // Chargement des données
        $data = $this->approvisionnement();
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['approvisionnements']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['approvisionnements'] as $article) {
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-approvisionnement', $data);
    }
    public function approvisionnement()
    {
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where('deleted_at', null)
            ->orderBy('approvisionnements.id', 'DESC')
            ->get();
        return [
            'approvisionnements' => $approvisionnements,
            'title' => 'liste-approvisionnements-pdf',
        ];
    }

    //Approvisionnement par période PDF
    public function listeApprovisionnementByPeriodePdf($debut, $fin)
    {
        // Chargement des données
        $data = $this->approvisionnementByPeriode($debut, $fin);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['approvisionnements']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['approvisionnements'] as $article) {
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-approvisionnement', $data);
    }
    public function approvisionnementByPeriode($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where('deleted_at', null)
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $date1)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $date2)
            ->orderBy('approvisionnements.date_approvisionnement', 'ASC')
            ->get();
        return [
            'approvisionnements' => $approvisionnements,
            'title' => 'liste-approvisionnements-du-' . $debut . '-au-' . $fin . '-pdf',
            'dates' => [
                'debut' => $debut,
                'fin' => $fin,
            ],
            'searchBy' => [
                'research' => 'Periode',
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
    }

    //Approvisionnement par fournisseur PDF
    public function listeApprovisionnementByFournisseurPdf($fournisseur)
    {
        // Chargement des données
        $data = $this->approvisionnementByFournisseur($fournisseur);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['approvisionnements']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['approvisionnements'] as $article) {
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-approvisionnement', $data);
    }
    public function approvisionnementByFournisseur($fournisseur)
    {
        $infos_fournisseur = Fournisseur::find($fournisseur);
        $approvisionnements = Approvisionnement::with('depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['deleted_at', null], ['approvisionnements.fournisseur_id', $fournisseur]])
            ->orderBy('approvisionnements.date_approvisionnement', 'ASC')
            ->get();
        return [
            'infos_fournisseur' => $infos_fournisseur,
            'approvisionnements' => $approvisionnements,
            'title' => 'liste-approvisionnements-du-fournisseur-' . $infos_fournisseur->full_name_fournisseur . '-pdf',
            'searchBy' => [
                'research' => 'Fournisseur',
                'value' => $infos_fournisseur->full_name_fournisseur,
            ],
        ];
    }

    //Approvisionnement par fournisseur PDF
    public function listeApprovisionnementByPeriodeFournisseurPdf($debut, $fin, $fournisseur)
    {
        // Chargement des données
        $data = $this->approvisionnementByPeriodeFournisseur($debut, $fin, $fournisseur);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['approvisionnements']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['approvisionnements'] as $article) {
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-approvisionnement', $data);
    }
    public function approvisionnementByPeriodeFournisseur($debut, $fin, $fournisseur)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $infos_fournisseur = Fournisseur::find($fournisseur);
        $approvisionnements = Approvisionnement::with('fournisseur', 'depot')
            ->select('approvisionnements.*', DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
            ->Where([['deleted_at', null], ['approvisionnements.fournisseur_id', $fournisseur]])
            ->whereDate('approvisionnements.date_approvisionnement', '>=', $date1)
            ->whereDate('approvisionnements.date_approvisionnement', '<=', $date2)
            ->orderBy('approvisionnements.id', 'DESC')
            ->get();
        return [
            'infos_fournisseur' => $infos_fournisseur,
            'approvisionnements' => $approvisionnements,
            'title' => 'liste-approvisionnements-du-fournisseur-' . $infos_fournisseur->full_name_fournisseur . '-du-' . $debut . '-au-' . $fin . '-pdf',
            'dates' => [
                'debut' => $debut,
                'fin' => $fin,
            ],
            'searchBy' => [
                'research' => 'Periode-Fournisseur',
                'value' => $infos_fournisseur->full_name_fournisseur,
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
    }

    //Article PDF
    public function listeArticlePdf()
    {
        // Chargement des données
        $data = $this->article();
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articles']);
        $data['montantTotal'] = 0;
        $index = 1;
        // Calcul du montant total
        foreach ($data['articles'] as $article) {
            $article['code_barre'] = str_replace(['[', '"', ']', ','], ['', '', '', ' - '], json_encode($article['code_barre']));
            $data['montantTotal'] += $article['prix_achat_ttc'];
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        //$this->pdfGenerator(view('crm.etats.liste-articles', $data));
        return view('crm.etats.liste-articles', $data);
    }
    //Liste des dépenses
    public function pdfGenerator($view)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view->render());
        return $pdf->stream('test.pdf');
    }
    public function article()
    {
        $articles = Article::with('categorie', 'sous_categorie')
            ->join('categories', 'categories.id', '=', 'articles.categorie_id')
            ->leftjoin('categories as sc', 'sc.id', '=', 'articles.sous_categorie_id')
            ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->select('articles.*', 'categories.libelle_categorie', 'sc.libelle_categorie as scl', 'param_tvas.tva')
            ->Where('articles.deleted_at', null)
            ->orderBy('libelle_article', 'ASC')
            ->get();
        return [
            'articles' => $articles,
            'title' => 'liste-articles-pdf',
        ];
    }

    //Article par catégorie PDF
    public function listeArticleByCategoriePdf($categorie)
    {
        // Chargement des données
        $data = $this->articleByCategorie($categorie);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articles']);
        $data['montantTotal'] = 0;
        $index = 1;
        // Calcul du montant total
        foreach ($data['articles'] as $article) {
            $article['code_barre'] = str_replace(['[', '"', ']', ','], ['', '', '', ' - '], json_encode($article['code_barre']));
            $data['montantTotal'] += $article['prix_achat_ttc'];
            $article['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-articles', $data);
    }
    public function articleByCategorie($categorie)
    {
        $infos_categorie = Categorie::find($categorie);
        $articles = Article::where([['articles.deleted_at', null], ['articles.categorie_id', $categorie]])
            ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->leftjoin('categories as sc', 'sc.id', '=', 'articles.sous_categorie_id')
            ->select('articles.*', 'sc.libelle_categorie as scl', 'param_tvas.tva')
            ->orderBy('libelle_article', 'ASC')
            ->get();
        return [
            'infos_categorie' => $infos_categorie,
            'articles' => $articles,
            'title' => 'liste-articles-de-categorie-' . $infos_categorie->libelle_categorie . '-pdf',
            'searchBy' => [
                'research' => 'Categorie',
                'value' => $infos_categorie->libelle_categorie,
            ],
        ];
    }

    //Fournisseur PDF
    public function listeFournisseurPdf()
    {
        // Chargement des données
        $data = $this->fournisseur();
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['fournisseurs']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['fournisseurs'] as $fournisseur) {
            $fournisseur['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-fournisseurs', $data);
    }
    public function fournisseur()
    {
        $fournisseurs = Fournisseur::with('nation')
            ->select('fournisseurs.*')
            ->Where('deleted_at', null)
            ->orderBy('full_name_fournisseur', 'ASC')
            ->get();
        return [
            'fournisseurs' => $fournisseurs,
            'title' => 'liste-fournisseurs-pdf',
        ];
    }

    //Fournisseur par pays PDF
    public function listeFournisseurByNationPdf($pays)
    {
        // Chargement des données
        $data = $this->fournisseurByNation($pays);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['fournisseurs']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['fournisseurs'] as $fournisseur) {
            $fournisseur['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-fournisseurs', $data);
    }
    public function fournisseurByNation($pays)
    {
        $info_pays = Nation::find($pays);
        $fournisseurs = Fournisseur::with('nation')
            ->select('fournisseurs.*')
            ->Where([['deleted_at', null], ['fournisseurs.nation_id', $pays]])
            ->orderBy('full_name_fournisseur', 'ASC')
            ->get();
        return [
            'info_pays' => $info_pays,
            'fournisseurs' => $fournisseurs,
            'title' => 'liste-fournisseurs-de-' . $info_pays->libelle_nation . '-pdf',
            'searchBy' => [
                'research' => 'Pays',
                'value' => $info_pays->libelle_nation,
            ],
        ];
    }

    //Client PDF
    public function listeClientPdf()
    {
        // Chargement des données
        $data = $this->client();
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['clients']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['clients'] as $client) {
            $client['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-clients', $data);
    }
    public function client()
    {
        $clients = Client::with('nation')
            ->select('clients.*')
            ->Where('deleted_at', null)
            ->orderBy('full_name_client', 'ASC')
            ->get();
        return [
            'clients' => $clients,
            'title' => 'liste-clients-pdf',
        ];
    }

    //Client par pays PDF
    public function listeClientByNationPdf($pays)
    {
        // Chargement des données
        $data = $this->clientByNation($pays);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['clients']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['clients'] as $client) {
            $client['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-clients', $data);
    }
    public function clientByNation($pays)
    {
        $info_pays = Nation::find($pays);
        $clients = Client::with('nation')
            ->select('clients.*')
            ->Where([['deleted_at', null], ['clients.nation_id', $pays]])
            ->orderBy('full_name_client', 'ASC')
            ->get();
        return [
            'info_pays' => $info_pays,
            'clients' => $clients,
            'title' => 'liste-clients-de-' . $info_pays->libelle_nation . '-pdf',
            'searchBy' => [
                'research' => 'Pays',
                'value' => $info_pays->libelle_nation,
            ],
        ];
    }

    //Dépôt PDF
    public function listeDepotPdf()
    {
        // Chargement des données
        $data = $this->depot();
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['depots']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['depots'] as $depot) {
            $depot['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-depots', $data);
    }
    public function depot()
    {
        //$depots = DB::table('depots')->select('depots.*')->Where('deleted_at', null)->orderBy('depots.libelle_depot', 'ASC')->get();
        $depots = Depot::select('depots.*')->Where('deleted_at', null)->orderBy('depots.libelle_depot', 'ASC')->get();
        return [
            'depots' => $depots,
            'title' => 'liste-depots-pdf',
        ];
    }

    public function listeTransfertStockPdf()
    {
        // Chargement des données
        $data = $this->transfertStock();
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['transfert_stocks']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['transfert_stocks'] as $transfert_stock) {
            $transfert_stock['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-transfert-stock', $data);
    }
    public function transfertStock()
    {
        $transfert_stocks = TransfertStock::with('depot_depart', 'depot_arrivee')
        ->select('transfert_stocks.*', DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
        ->Where('transfert_stocks.deleted_at', null)
        ->orderBy('transfert_stocks.date_transfert', 'DESC')
            ->get();
        return [
            'transfert_stocks' => $transfert_stocks,
            'title' => 'liste-transfert-stock-pdf',
        ];
    }

    public function listeTransfertStockByPeriodePdf($debut, $fin)
    {
        // Chargement des données
        $data = $this->transfertStockByPeriode($debut, $fin);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['transfert_stocks']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['transfert_stocks'] as $transfert_stock) {
            $transfert_stock['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-transfert-stock', $data);
    }
    public function transfertStockByPeriode($debut, $fin)
    {
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $transfert_stocks = TransfertStock::with('depot_depart', 'depot_arrivee')
        ->select('transfert_stocks.*', DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
        ->Where('transfert_stocks.deleted_at', null)
        ->whereDate('transfert_stocks.date_transfert', '>=', $date1)
        ->whereDate('transfert_stocks.date_transfert', '<=', $date2)
        ->orderBy('transfert_stocks.date_transfert', 'DESC')
            ->get();
        return [
            'transfert_stocks' => $transfert_stocks,
            'title' => 'liste-transfert-stock-du-' . $debut . '-au-' . $fin . '-pdf',
            'searchBy' => [
                'research' => 'Periode',
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
    }

    //Inventaire pdf
    public function listeInventairePdf(){
        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where('inventaires.deleted_at', null)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        return [
            'transfert_stocks' => $transfert_stocks,
            'title' => 'liste-transfert-stock-pdf',
        ];
    }
    public function listeInventaireByDepotPdf($depot){
        $info_depot = Depot::find($depot)->first();
        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where([['inventaires.deleted_at', null], ['inventaires.depot_id', $depot]])
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'inventaires' => $inventaires,
            'title' => 'liste-inventaires-du-depot-' . $info_depot->libelle_depot . '-pdf',
            'searchBy' => [
                'research' => 'Depot',
                'depot' => $info_depot->libelle_depot,
            ],
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['inventaires']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['inventaires'] as $inventaire) {
            $inventaire['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-inventaires', $data);
    }
    public function listeInventaireByPeriodePdf($debut, $fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where('inventaires.deleted_at', null)
            ->whereDate('inventaires.date_inventaire', '>=', $date1)
            ->whereDate('inventaires.date_inventaire', '<=', $date2)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'inventaires' => $inventaires,
            'title' => 'liste-inventaires-du-' . $debut . '-au-' . $fin . '-pdf',
            'searchBy' => [
                'research' => 'Periode',
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['inventaires']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['inventaires'] as $inventaire) {
            $inventaire['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-inventaires', $data);
    }
    public function listeInventaireByPeriodeDepotPdf($debut, $fin, $depot){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot)->first();
        $inventaires = Inventaire::with('depot')
            ->select('inventaires.*', DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
            ->Where([['inventaires.deleted_at', null], ['inventaires.depot_id', $depot]])
            ->whereDate('inventaires.date_inventaire', '>=', $date1)
            ->whereDate('inventaires.date_inventaire', '<=', $date2)
            ->orderBy('inventaires.id', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'inventaires' => $inventaires,
            'title' => 'liste-inventaires-du-depot-' . $info_depot->libelle_depot . '-du-' . $debut . '-au-' . $fin . '-pdf',
            'searchBy' => [
                'research' => 'PeriodeDepot',
                'depot' => $info_depot->libelle_depot,
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['inventaires']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['inventaires'] as $inventaire) {
            $inventaire['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-inventaires', $data);
    }

    //Destockage
    public function listeDestockagePdf(){
        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where('destockages.deleted_at', null)
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'destockages' => $destockages,
            'title' => 'liste-destockages-pdf',
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['destockages']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['destockages'] as $destockage) {
            $destockage['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-destockages', $data);
    }

    public function listeDestockageByDepotPdf($depot){
        $info_depot = Depot::find($depot)->first();
        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where([['destockages.deleted_at', null], ['destockages.depot_id', $depot]])
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'destockages' => $destockages,
            'title' => 'liste-destockages-du-depot-' . $info_depot->libelle_depot . '-pdf',
            'searchBy' => [
                'research' => 'Depot',
                'depot' => $info_depot->libelle_depot,
            ],
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['destockages']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['destockages'] as $destockage) {
            $destockage['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-destockages', $data);
    }
    public function listeDestockageByPeriodePdf($debut, $fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where('destockages.deleted_at', null)
            ->whereDate('destockages.date_destockage', '>=', $date1)
            ->whereDate('destockages.date_destockage', '<=', $date2)
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'destockages' => $destockages,
            'title' => 'liste-destockages-du-' . $debut . '-au-' . $fin . '-pdf',
            'searchBy' => [
                'research' => 'Periode',
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['destockages']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['destockages'] as $destockage) {
            $destockage['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-destockages', $data);
    }
    public function listeDestockageByPeriodeDepotPdf($debut, $fin, $depot){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot)->first();
        $destockages = Destockage::with('depot')
            ->select('destockages.*', DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
            ->Where([['destockages.deleted_at', null], ['destockages.depot_id', $depot]])
            ->whereDate('destockages.date_destockage', '>=', $date1)
            ->whereDate('destockages.date_destockage', '<=', $date2)
            ->orderBy('destockages.date_destockage', 'DESC')
            ->get();
        // Tableau de données
        $data = [
            'destockages' => $destockages,
            'title' => 'liste-destockages-du-depot-' . $info_depot->libelle_depot . '-du-' . $debut . '-au-' . $fin . '-pdf',
            'searchBy' => [
                'research' => 'PeriodeDepot',
                'depot' => $info_depot->libelle_depot,
                'date-debut' => $debut,
                'date-fin' => $fin,
            ],
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['destockages']);
        $index = 1;
        // Calcul du montant total
        foreach ($data['destockages'] as $destockage) {
            $destockage['index'] = $index++;
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-destockages', $data);
    }

    //Header and footer des pdf
    public function header()
    {
        $header = '<html>
                    <head>
                        <style>
                            @page{
                                margin: 100px 25px;
                                }
                            header{
                                    position: absolute;
                                    top: -60px;
                                    left: 0px;
                                    right: 0px;
                                    height:20px;
                                }
                            .container-table{
                                            margin:80px 0;
                                            width: 100%;
                                        }
                            .fixed-footer{.
                                width : 100%;
                                position: fixed;
                                bottom: -28;
                                left: 0px;
                                right: 0px;
                                height: 50px;
                                text-align:center;
                            }
                            .fixed-footer-right{
                                position: absolute;
                                bottom: -150;
                                height: 0;
                                font-size:13px;
                                float : right;
                            }
                            .page-number:before {

                            }
                        </style>
                    </head>
    /
    <script type="text/php">
        if (isset($pdf)){
            $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
        <body>
        <header>
        <p style="margin:0; position:left;">
            <img src=' . $this->infosConfig()->logo . ' width="200" height="160"/>
        </p>
        </header>';
        return $header;
    }
    public function footer()
    {
        $footer = "<div class='fixed-footer'>
                        <div class='page-number'></div>
                    </div>
                    <div class='fixed-footer-right'>
                     <i> Editer le " . date('d-m-Y') . "</i>
                    </div>
            </body>
        </html>";
        return $footer;
    }

}
