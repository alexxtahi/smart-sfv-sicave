<?php

use App\Http\Controllers\Crm\CompteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('auth.login');
});


Auth::routes();
Route::get('/confirmer_compte/{id}/{token}', 'Auth\RegisterController@confirmationCompte');
Route::post('/update_password', 'Auth\RegisterController@updatePassword')->name('update_password');
Route::get('/configuration', 'ConfigurationController@index')->name('configuration')->middleware('auth');
Route::post('/configuration/store', 'ConfigurationController@store')->name('configuration.store')->middleware('auth');
Route::get('/configuration/infos-update', 'ConfigurationController@show')->name('configuration.infos-update')->middleware('auth');
Route::put('/configuration/update', 'ConfigurationController@update')->name('configuration.update')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/caissier', 'HomeController@caissier')->name('caissier');

//les routes du module Parametre
Route::namespace('Parametre')->middleware('auth')->name('parametre.')->prefix('parametre')->group(function () {
    //Route categries et sous catégories
    Route::get('/sous-categories/modele/download', 'CategorieController@downloadModelSousCategorie')->name('sous_categorie.dowload_model');
    Route::post('/sous-categories/store-from-upload', 'CategorieController@storeFromUploadSousCategorie')->name('sous_categorie.store_from_upload');
    Route::resource('categories', 'CategorieController');
    Route::post('/categories/store-from-upload', 'CategorieController@storeFromUpload')->name('categorie.store_from_upload');
    Route::get('sous-categories', 'CategorieController@sousCategorie')->name('sous-categories');
    Route::get('liste-categories', 'CategorieController@listeCategorie')->name('liste-categories');
    Route::get('liste-sous-categories', 'CategorieController@listeSousCategorie')->name('liste-sous-categories');

    //Route uniteés
    Route::resource('unites', 'UniteController');
    Route::get('liste-unites', 'UniteController@listeUnite')->name('liste-unites');

    //Route taille
    Route::resource('tailles', 'TailleController');
    Route::get('liste-tailles', 'TailleController@listeTaille')->name('liste-tailles');

    //Route rayon
    Route::resource('rayons', 'RayonController');
    Route::get('liste-rayons', 'RayonController@listeRayon')->name('liste-rayons');

    //Route rangée
    Route::resource('rangees', 'RangeeController');
    Route::get('liste-rangees', 'RangeeController@listeRangee')->name('liste-rangees');

    //Route casier
    Route::resource('casiers', 'CasierController');
    Route::get('liste-casiers', 'CasierController@listeCasier')->name('liste-casiers');

    //Route parame tva
    Route::resource('param-tva', 'ParamTvaController');
    Route::get('liste-param-tva', 'ParamTvaController@listeParamTva')->name('liste-param-tva');

    //Route caisse
    Route::resource('caisses', 'CaisseController');
    Route::get('liste-caisses', 'CaisseController@listeCaisse')->name('liste-caisses');
    Route::get('liste-caisses-by-id/{id}', 'CaisseController@listeCaisseById');

    //Route banque
    Route::resource('banques', 'BanqueController');
    Route::get('liste-banques', 'BanqueController@listeBanque')->name('liste-banques');

    //Route nations
    Route::resource('nations', 'NationController');
    Route::get('liste-nations', 'NationController@listeNation')->name('liste-nations');

    //Route moyen-reglements
    Route::resource('moyen-reglements', 'MoyenReglementController');
    Route::get('liste-moyen-reglements', 'MoyenReglementController@listeMoyenReglement')->name('liste-moyen-reglements');

    //Route cartes-fidelites
    Route::resource('cartes-fidelites', 'CarteFideliteController');
    Route::get('liste-cartes-fidelites', 'CarteFideliteController@listeCarteFidelite')->name('liste-cartes-fidelites');

    //Route categories-depenses
    Route::resource('categorie-depenses', 'CategorieDepenseController');
    Route::get('liste-categorie-depenses', 'CategorieDepenseController@listeCategorieDepense')->name('liste-categorie-depenses');
});

//les routes du module CRM
Route::namespace('Crm')->middleware('auth')->name('crm.')->prefix('crm')->group(function () {
    //Route regimes
    Route::resource('regimes', 'RegimeController');
    Route::get('liste-regimes', 'RegimeController@listeRegime')->name('liste-regimes');

    //Route clients
    Route::resource('clients', 'ClientController');
    Route::get('fiche-client/{client}', 'ClientController@ficheClient');
    Route::get('compte-client', 'ClientController@compteClient')->name('compte-client');
    Route::get('liste-clients', 'ClientController@listeClient')->name('liste-clients');
    Route::get('liste-clients-by-nation/{nation}', 'ClientController@listeClientByNation');
    Route::get('liste-soldes-clients', 'ClientController@listeSoldeClient');
    Route::get('liste-soldes-by-clients/{client}', 'ClientController@listeSoldeByClient');
    Route::get('get-clients-by-id/{id}', 'ClientController@getClientById');
    Route::get('liste-factures-client/{id}', 'ClientController@listeFactureClient');
    Route::get('liste-articles-achetes-by-client/{id}', 'ClientController@listeArticleAcheteByClient');
    Route::get('/clients/modele/download', 'ClientController@downloadModel')->name('client.dowload_model');
    Route::post('/clients/store-from-upload', 'ClientController@storeFromUpload')->name('client.store_from_upload');

    //Route fournisseurs
    Route::resource('fournisseurs', 'FournisseurController');
    Route::get('fiche-fournisseur/{fournisseur}', 'FournisseurController@ficheFournisseur');
    Route::get('liste-fournisseurs', 'FournisseurController@listeFournisseur')->name('liste-fournisseurs');
    Route::get('liste-fournisseurs-by-nation/{nation}', 'FournisseurController@listeFournisseurByNation');
    Route::get('liste-soldes-fournisseurs', 'FournisseurController@listeSoldeFournisseur');
    Route::get('liste-soldes-by-fournisseurs/{fournisseur}', 'FournisseurController@listeSoldeByFournisseur');
    Route::get('liste-bon-fournisseurs/{fournisseur}', 'FournisseurController@listeBonFournisseur');
    Route::get('liste-tous-les-bons-fournisseur/{fournisseur}', 'FournisseurController@listeTousBonFournisseur');
    Route::get('liste-articles-commandes-by-fournisseur/{fournisseur}', 'FournisseurController@listeArticleCommandeByFournisseur');
    Route::get('/fournisseurs/modele/download', 'FournisseurController@downloadModel')->name('fournisseur.dowload_model');
    Route::post('/fournisseurs/store-from-upload', 'FournisseurController@storeFromUpload')->name('fournisseur.store_from_upload');

    //Route comptes
    Route::resource('comptes', 'CompteController');
    Route::get('comptes-cartes-fidelites', 'CompteController@compteCarteFidelite')->name('comptes-cartes-fidelites');
    Route::post('recharge.compte', 'CompteController@rechargeCompte')->name('recharge.compte');
    Route::get('liste-comptes', 'CompteController@listeCompte')->name('liste-comptes');
    Route::get('liste-compte-clients', 'CompteController@listeCompteClient')->name('liste-compte-clients');
    Route::get('liste-compte-carte', 'CompteController@listeCompteCarte');
    Route::get('liste-compte-fournisseur', 'CompteController@listeCompteFournisseur');
    Route::get('liste-compte-carte-by-client/{client}', 'CompteController@listeCompteCarteByClient');
    Route::get('liste-compte-carte/{carte}', 'CompteController@listeCompteCarteByCarte');
    Route::get('liste-compte-clients-by-client/{client}', 'CompteController@listeCompteClientByClient');

    //Route Mouvement compte
    Route::resource('mouvements', 'MouvementCompteController');
    Route::get('liste-mouvements-comptes', 'MouvementCompteController@listeMouvementCompte');
    Route::get('liste-mouvements-comptes-pdf', 'MouvementCompteController@listeMouvementComptePdf');
    Route::get('liste-mouvements-comptes-by-date/{date}', 'MouvementCompteController@listeMouvementCompteByDate');
    Route::get('liste-mouvements-comptes-by-compte/{compte}', 'MouvementCompteController@listeMouvementCompteByCompte');

    //Pdf pour les etats
    Route::get('carte-fidelite-pdf/{compte}', 'CompteController@carteFidelitePdf');
    Route::get('liste-compte-pdf', 'CompteController@listeComptePdf');
    Route::get('liste-compte-client-pdf', 'CompteController@listeCompteClientPdf');
    Route::get('liste-compte-un-client-pdf/{client}', 'ClientController@listeCompteClientPDf');
    Route::get('liste-compte-carte-pdf', 'CompteController@listeCompteCartePdf');
    Route::get('liste-compte-carte-by-carte-pdf/{carte}', 'CompteController@listeCompteCarteByCartePdf');
    Route::get('liste-compte-carte-by-client-pdf/{client}', 'CompteController@listeCompteCarteByClientPdf');
    Route::get('liste-compte-fournisseur-pdf', 'CompteController@listeCompteFournisseurPdf');
});

//les routes du module Stock
Route::namespace('Stock')->middleware('auth')->name('stock.')->prefix('stock')->group(function () {
    //Route depots
    Route::resource('depots', 'DepotController');
    Route::get('liste-depots', 'DepotController@listeDepot')->name('liste-depots');

    //Route depots articles
    Route::resource('depot-articles', 'DepotArticleController');
    Route::get('liste-depot-by-article/{article}', 'DepotArticleController@listeDepotByArticle');
    Route::get('liste-article-by-depot/{depot}', 'DepotArticleController@listeArticleByDepot');
    Route::get('liste-article-by-depot-code-barre/{depot}/{codeBarre}', 'DepotArticleController@listeArticleByDepotCodeBarre');
    Route::get('liste-article-by-article-depot/{article}/{depot}', 'DepotArticleController@listeArticleByArticleDepot');
    Route::get('liste-article-by-categorie-depot/{categorie}/{depot}', 'DepotArticleController@listeArticleByCategorieDepot');

    //Route articles
    Route::get('/articles/modele/download', 'ArticleController@downloadModel')->name('article.dowload_model');
    Route::post('/articles/store-from-upload', 'ArticleController@storeFromUpload')->name('article.store_from_upload');
    Route::resource('articles', 'ArticleController');
    Route::post('update-article', 'ArticleController@updateArticle')->name('update-article');
    Route::get('get-article-by-id/{id}', 'ArticleController@getArticleById');
    Route::get('liste-articles', 'ArticleController@listeArticle')->name('liste-articles');
    Route::get('liste-articles-by-categorie/{categorie}', 'ArticleController@listeArticleByCategorie');
    Route::get('liste-articles-by-sous-categorie/{sousCategorie}', 'ArticleController@listeArticleBySousCategorie');
    Route::get('liste-articles-by-libelle/{libelle}', 'ArticleController@listeArticleByLibelle');
    Route::get('liste-articles-by-code/{code}', 'ArticleController@listeArticleByCode');

    //Route approvisionnements
    Route::resource('approvisionnements', 'ApprovisionnementController');
    Route::post('update-approvisionnement', 'ApprovisionnementController@updateApprovisionnement')->name('update-approvisionnement');
    Route::get('liste-approvisionnements', 'ApprovisionnementController@listeApprovisionnement')->name('liste-approvisionnements');
    Route::get('liste-approvisionnements-by-fournisseur/{fournisseur}', 'ApprovisionnementController@listeApprovisionnementByFournisseur');
    Route::get('liste-approvisionnements-by-depot/{depot}', 'ApprovisionnementController@listeApprovisionnementsByDepot');
    Route::get('liste-approvisionnements-by-date/{date}', 'ApprovisionnementController@listeApprovisionnementsByDate');
    Route::get('liste-approvisionnements-by-fournisseur/{fournisseur}', 'ApprovisionnementController@listeApprovisionnementByFournisseur');
    Route::get('liste-approvisionnements-by-periode/{debut}/{find}', 'ApprovisionnementController@listeApprovisionnementByPeriode');
    Route::get('liste-approvisionnements-by-periode-fournisseur/{debut}/{find}/{fournisseur}', 'ApprovisionnementController@listeApprovisionnementByPeriodeFournisseur');
    Route::get('fiche-approvisionnement-pdf/{idApprovisionnement}', 'ApprovisionnementController@ficheApprovisionnementPdf');

    //Route articles approvisionnements
    Route::resource('articles-approvisionnes', 'ArticleApprovisionnementController');
    Route::get('liste-articles-approvisionnes/{article}', 'ArticleApprovisionnementController@listeArticleApprovisionne');

    //Route transfert stocks
    Route::resource('transfert-stocks', 'TransfertStockController');
    Route::post('update-transfert-stocks', 'TransfertStockController@updateTransfertStocks')->name('update-transfert-stocks');
    Route::get('liste-transferts-stocks', 'TransfertStockController@listeTransfertStock');
    Route::get('liste-transferts-stocks-by-date/{date}', 'TransfertStockController@listeTransfertStockByDate');
    Route::get('liste-transferts-stocks-by-periode/{debut}/{fiin}', 'TransfertStockController@listeTransfertStockByPeriode');
    Route::get('transfert-stock-pdf/{idTransfert}', 'TransfertStockController@transfertStockPdf');

    //Route article transferes
    Route::resource('article-transferts', 'ArticleTransfertController');
    Route::get('liste-articles-transferts/{transfert}', 'ArticleTransfertController@listeArticleTransferts');

    //Route destockages
    Route::resource('destockages', 'DestockageController');
    Route::post('update-destockage', 'DestockageController@updateDestockage')->name('update-destockage');
    Route::get('liste-destockages', 'DestockageController@listeDestockage');
    Route::get('liste-destockages-by-depot/{depot}', 'DestockageController@listeDestockageByDepot');
    Route::get('liste-destockages-by-date/{date}', 'DestockageController@listeDestockageByDate');
    Route::get('liste-destockages-by-periode/{debut}/{fin}', 'DestockageController@listeDestockageByPeriode');
    Route::get('liste-destockages-by-periode-depot/{debut}/{fin}/{depot}', 'DestockageController@listeDestockageByPeriodeDepot');
    Route::get('destockage-pdf/{id}', 'DestockageController@destockagePdf');


    //Route article destockers
    Route::resource('article-destockers', 'ArticleDestockerController');
    Route::get('liste-article-destockers/{idDestockage}', 'ArticleDestockerController@listeArticleDestockers');

     //Route inventaires
     Route::resource('inventaires', 'InventaireController');
     Route::post('update-inventaire', 'InventaireController@updateInventaire')->name('update-inventaire');
     Route::get('liste-inventaires', 'InventaireController@listeInventaire');
     Route::get('liste-inventaires-by-depot/{depot}', 'InventaireController@listeInventaireByDepot');
     Route::get('liste-inventaires-by-date/{date}', 'InventaireController@listeInventaireByDate');
     Route::get('liste-inventaires-by-periode/{debut}/{fin}', 'InventaireController@listeInventaireByPeriode');
     Route::get('liste-inventaires-by-periode-depot/{debut}/{fin}/{depot}', 'InventaireController@listeInventaireByPeriodeDepot');
     Route::get('fiche-inventaire-pdf/{id}', 'InventaireController@ficheInventairePdf');

    //Route detail-inventaires
    Route::resource('detail-inventaires', 'DetailInventaireController');
    Route::get('liste-details-inventaire/{idInventaire}', 'DetailInventaireController@listeDetailsInventaire');

    //Route bon-commandes
    Route::resource('bon-commandes', 'BonCommandeController');
    Route::get('reception-bon-commandes', 'BonCommandeController@vuReceptionBonCommande')->name('reception-bon-commandes');
    Route::post('reception-bon-store', 'BonCommandeController@receptionBonStore')->name('reception-bon-store');
    Route::get('liste-bon-commandes', 'BonCommandeController@listeBonCommande');
    Route::get('liste-bon-commandes-by-numero-bon/{numero}', 'BonCommandeController@listeBonCommandeByNumeroBon');
    Route::get('liste-bon-commandes-by-date/{date}', 'BonCommandeController@listeBonCommandeByDate');
    Route::get('liste-bon-commandes-by-fournisseur/{fournisseur}', 'BonCommandeController@listeBonCommandeByFournisseur');
    Route::get('liste-bon-commandes-by-etat/{etat}', 'BonCommandeController@listeBonCommandeByEtat');
    Route::get('fiche-bon-commande-pdf/{id}', 'BonCommandeController@ficheBonCommandePdf');
    Route::get('fiche-reception-bon-commande-pdf/{id}', 'BonCommandeController@ficheReceptionBonCommandePdf');

    //Réception bon
    Route::get('liste-reception-bon-commandes', 'BonCommandeController@listeReceptionBonCommande');
    Route::get('liste-reception-bon-commandes-by-numero/{numero}', 'BonCommandeController@listeReceptionBonCommandeByNumero');
    Route::get('liste-reception-bon-commandes-by-founisseur/{founisseur}', 'BonCommandeController@listeReceptionBonCommandeByFounisseur');
    Route::get('liste-reception-bon-commandes-by-date/{date}', 'BonCommandeController@listeReceptionBonCommandeByDate');

    //Route article-bon-commandes
    Route::resource('articles-bon', 'ArticleBonController');
    Route::get('liste-articles-bon/{idBonCommande}', 'ArticleBonController@listeArticleBon');
});

//les routes du module Vente
Route::namespace('Vente')->middleware('auth')->name('vente.')->prefix('vente')->group(function () {

        //Gestion des facturations
    Route::resource('ventes', 'VenteController');
    Route::post('vente/store', 'VenteController@venteStore')->name('vente.store');
    Route::post('update-vente', 'VenteController@updateVente')->name('update-vente');
    Route::get('liste-ventes/{numero_facture?}', 'VenteController@listeVente');
    Route::get('liste-ventes-by-client/{client}', 'VenteController@listeVenteByClient');
    Route::get('liste-ventes-by-date/{date}', 'VenteController@listeVenteByDate');
    Route::get('facture-vente-pdf/{id}', 'VenteController@factureVentePdf');

        //Gestion du point de caisse
    Route::get('point-caisse', 'VenteController@pointCaisse')->name('point-caisse');
    Route::get('vue-liste-ventes-caisse/{caisse?}', 'VenteController@vueListeVenteCaisse')->name('vue-liste-ventes-caisse');
    Route::post('caisse/store', 'VenteController@caisseStore')->name('caisse.store');
    Route::post('caisse/update', 'VenteController@caisseStoreUpdate')->name('caisse-update');
    Route::get('liste-ventes-caisse/{caisse?}', 'VenteController@listeVenteCaisse');
    Route::get('liste-ventes-caisse-by-numero/{numero}/{caisse?}', 'VenteController@listeVenteCaisseByNumero');
    Route::get('liste-ventes-caisse-by-date/{date}/{caisse}', 'VenteController@listeVenteCaisseByDate');
    Route::get('ticket-caisse-pdf/{id}', 'VenteController@ticketCaissePdf');

    //Billetage
    Route::get('billetage-pdf/{caisse_ouverte}', 'BilletageController@billetagePdf');

    //Article vente liste-articles-vente
    Route::resource('articles-vente', 'ArticleVenteController');
    Route::get('liste-articles-vente/{idVente}', 'ArticleVenteController@listeArticlesVente');

    //Gestion des factures proformas et devis
    Route::resource('devis', 'DevisController');
    Route::post('update-devis', 'DevisController@updateDevis')->name('update-devis');
    Route::get('liste-devis/{numero_facture?}', 'DevisController@listeDevis');
    Route::get('liste-devis-by-client/{client}', 'DevisController@listeDevisByClient');
    Route::get('liste-devis-by-date/{date}', 'DevisController@listeDevisByDate');
    Route::get('devis-pdf/{id}', 'DevisController@devisPdf');

    //Article devis liste-articles-vente
    Route::resource('articles-devis', 'ArticleDevisController');
    Route::get('liste-articles-devis/{idDevis}', 'ArticleDevisController@listeArticlesDevis');

    //Gestion des reglements
    Route::resource('reglements', 'ReglementController');
    Route::post('update-reglement', 'ReglementController@updateReglement')->name('update-reglement');
    Route::get('reglements_client', 'ReglementController@reglementClient')->name('reglements_client');
    Route::get('reglements_fournisseur', 'ReglementController@reglementFournisseur')->name('reglements_fournisseur');
    Route::get('liste-reglements/{type}', 'ReglementController@listeReglement');
    Route::get('liste-reglements-by-date/{type}/{date}', 'ReglementController@listeReglementByDate');
    Route::get('liste-reglements-by-client/{client}', 'ReglementController@listeReglementByClient');
    Route::get('liste-reglements-by-moyen-reglement-client/{moyen}/{client}', 'ReglementController@listeReglementByMoyenReglementClient');
    Route::get('liste-reglements-by-facture/{facture}', 'ReglementController@listeReglementByFacture');
    Route::get('liste-reglements-by-fournisseur/{fournisseur}', 'ReglementController@listeReglementByFournisseur');
    Route::get('liste-reglements-by-moyen-reglement-fournisseur/{moyen}/{fournisseur}', 'ReglementController@listeReglementByMoyenReglementFournisseur');
    Route::get('liste-reglements-by-bon/{bon}', 'ReglementController@listeReglementByBonCommande');

    Route::get('recu-reglement-pdf/{id}', 'ReglementController@recuReglementPdf');
});

//Les routes du module Comptabilité
Route::namespace('Comptabilite')->middleware('auth')->name('comptabilite.')->prefix('comptabilite')->group(function () {
    //** Ecrans **//
        //Comptabilite
    Route::get('soldes-clients', 'ComptabiliteController@vuSoldeClient')->name('soldes-clients');
    Route::get('soldes-fournisseurs', 'ComptabiliteController@vuSoldeFournisseur')->name('soldes-fournisseurs');
    Route::get('marge-sur-ventes', 'ComptabiliteController@vuMargeSurVente')->name('marge-sur-ventes');
    Route::get('points-caisses-clotures', 'ComptabiliteController@vuPointCaisseCloture')->name('points-caisses-clotures');

        //Fiscalite
    Route::get('declaration-fiscale', 'FiscaliteController@vuDeclarationFiscal')->name('declaration-fiscale');
    Route::get('ticket-declare-tva', 'FiscaliteController@vuTicketDeclareTva')->name('ticket-declare-tva');
    Route::get('timbre-fiscal', 'FiscaliteController@vuTimbreFiscal')->name('timbre-fiscal');
    Route::get('ticket-declare-timbre', 'FiscaliteController@vuTicketDeclareTimbre')->name('ticket-declare-timbre');

    Route::get('listes-declarations-fiscales', 'FiscaliteController@listeDeclarationFiscale');
    Route::get('listes-declarations-fiscales-by-depot/{depot}', 'FiscaliteController@listeDeclarationFiscaleByDepot');
    Route::get('listes-declarations-fiscales-by-periode/{debut}/{fin}', 'FiscaliteController@listeDeclarationFiscaleByPeriode');
    Route::get('listes-declarations-fiscales-by-depot-periode/{depot}/{debut}/{fin}', 'FiscaliteController@listeDeclarationFiscaleByDepotPeriode');

    Route::get('listes-declarations-timbres', 'FiscaliteController@listeDeclarationTimbre');
    Route::get('listes-declarations-timbres-by-depot/{depot}', 'FiscaliteController@listeDeclarationTimbreByDepot');
    Route::get('listes-declarations-timbres-by-periode/{debut}/{fin}', 'FiscaliteController@listeDeclarationTimbreByPeriode');
    Route::get('listes-declarations-timbres-by-depot-periode/{depot}/{debut}/{fin}', 'FiscaliteController@listeDeclarationTimbreByDepotPeriode');

    Route::resource('declarations-tva', 'TvaDeclareeController');
    Route::get('liste-declarations-tva', 'TvaDeclareeController@listeDeclarationTva');
    Route::get('liste-declarations-tva-by-depot/{depot}', 'TvaDeclareeController@listeDeclarationTvaByDepot');
    Route::get('liste-declarations-tva-by-periode/{debut}/{fin}', 'TvaDeclareeController@listeDeclarationTvaByPeriode');
    Route::get('liste-declarations-tva-by-depot-periode/{depot}/{debut}/{fin}', 'TvaDeclareeController@listeDeclarationTvaByDepotPeriode');

    Route::get('liste-tickets-declares/{idDeclaration}', 'TicketInTvaController@listeTicketDeclare');
    Route::get('tickets-declares-pdf/{idDeclaration}', 'TicketInTvaController@ticketDeclarePdf');

    Route::resource('timbre-declarares', 'TimbreDeclareController');
    Route::get('liste-timbre-declarares', 'TimbreDeclareController@listeTimbreDeclarare');
    Route::get('liste-timbre-declarares-by-depot/{depot}', 'TimbreDeclareController@listeTimbreDeclarareByDepot');
    Route::get('liste-dtimbre-declarares-by-periode/{debut}/{fin}', 'TimbreDeclareController@listeTimbreDeclarareByPeriode');
    Route::get('liste-timbre-declarares-by-depot-periode/{depot}/{debut}/{fin}', 'TimbreDeclareController@listeTimbreDeclarareByDepotPeriode');

    Route::get('liste-timbre-tiket-declares/{idDeclaration}', 'TimbreTiketDeclareController@listeTimbreTiketDeclare');
    Route::get('timbres-declares-pdf/{idDeclaration}', 'TimbreTiketDeclareController@timbreDeclarePdf');

        //Statistique des ventes
    Route::get('liste-marges-sur-ventes', 'ComptabiliteController@listeMargeSurVente');
    Route::get('liste-marges-sur-ventes-by-depot/{depot}', 'ComptabiliteController@listeMargeSurVenteByDepot');
    Route::get('liste-marges-sur-ventes-by-periode/{debut}/{fin}', 'ComptabiliteController@listeMargeSurVenteByPeriode');
    Route::get('liste-marges-sur-ventes-by-periode-depot/{debut}/{fin}/{depot}', 'ComptabiliteController@listeMargeSurVenteByPeriodeDepot');

        //Caisse ouverte
    Route::resource('caisses-ouvertes', 'CaisseOuverteController');
    Route::post('ouverture-caisses', 'CaisseOuverteController@ouvertureCaisse')->name('ouverture-caisse');
    Route::post('fermeture-caisses', 'CaisseOuverteController@fermetureCaisse')->name('fermeture-caisse');
    Route::get('get-caisse-ouverte-by-caisse/{caisse}', 'CaisseOuverteController@getCaisseOuverteByCaisse');
    Route::get('liste-caisses-cloturees', 'CaisseOuverteController@listeCaisseCloturee');
    Route::get('liste-caisses-cloturees-by-depot/{depot}', 'CaisseOuverteController@listeCaisseClotureeByDepot');
    Route::get('liste-caisses-cloturees-by-periode/{debut}/{fin}', 'CaisseOuverteController@listeCaisseClotureeByPeriode');
    Route::get('liste-caisses-cloturees-by-periode-depot/{debut}/{fin}/{depot}', 'CaisseOuverteController@listeCaisseClotureeByPeriodeDepot');

        //Etat Caisse
    Route::get('liste-caisses-cloturees-pdf', 'CaisseOuverteController@listeCaisseClotureePdf');
    Route::get('liste-caisses-cloturees-by-depot-pdf/{depot}', 'CaisseOuverteController@listeCaisseClotureeByDepotPdf');
    Route::get('liste-caisses-cloturees-by-periode-pdf/{debut}/{fin}', 'CaisseOuverteController@listeCaisseClotureeByPeriodePdf');
    Route::get('liste-caisses-cloturees-by-periode-depot-pdf/{debut}/{fin}/{depot}', 'CaisseOuverteController@listeCaisseClotureeByPeriodeDepotPdf');

        //Depenses
    Route::resource('depenses', 'DepenseController');
    Route::get('liste-depenses/{categorie?}', 'DepenseController@listeDepense');
    Route::get('liste-depenses-by-periode/{debut}/{fin}', 'DepenseController@listeDepenseByPeriode');
    Route::get('liste-depenses-by-categorie-periode/{categorie}/{debut}/{fin}', 'DepenseController@listeDepenseByCategoriePeriode');

      //Etat Depense
    Route::get('liste-depenses-pdf', 'DepenseController@listeDepensePdf');
    Route::get('liste-depenses-by-categorie-pdf/{categorie}', 'DepenseController@listeDepenseByCategoriePdf');
    Route::get('liste-depenses-by-periode-pdf/{debut}/{fin}', 'DepenseController@listeDepenseByPeriodePdf');
    Route::get('liste-depenses-by-periode-categorie-pdf/{debut}/{fin}/{categorie}', 'DepenseController@listeDepenseByPeriodeCategoriePdf');
});

//Etats
Route::namespace('Etat')->middleware('auth')->name('etat.')->prefix('etat')->group(function () {

    //Ecrans
    Route::get('etat-approvisionnements', 'EtatController@vuApprovisionnement')->name('etat-approvisionnements');
    Route::get('etat-articles', 'EtatController@vuArticle')->name('etat-articles');
    Route::get('etat-fournisseurs', 'EtatController@vuFournisseur')->name('etat-fournisseurs');
    Route::get('etat-clients', 'EtatController@vuClient')->name('etat-clients');
    Route::get('etat-depots', 'EtatController@vuDepot')->name('etat-depots');
    Route::get('etat-inventaires', 'EtatController@vuInventaire')->name('etat-inventaires');
    Route::get('etat-destockages', 'EtatController@vuDestockage')->name('etat-destockages');
    Route::get('etat-transfert-stock', 'EtatController@vuTransfertStock')->name('etat-transfert-stock');
    Route::get('etat-bon-commande', 'EtatController@vuBonCommande')->name('etat-bon-commande');


    /**Fichiers PDF**/
    //Approvisionnement
    Route::get('liste-approvisionnements-pdf', 'EtatController@listeApprovisionnementPdf');
    Route::get('liste-approvisionnements-by-periode-pdf/{debut}/{fin}', 'EtatController@listeApprovisionnementByPeriodePdf');
    Route::get('liste-approvisionnements-by-fournisseur-pdf/{fournisseur}', 'EtatController@listeApprovisionnementByFournisseurPdf');
    Route::get('liste-approvisionnements-by-periode-fournisseur-pdf/{debut}/{fin}/{fournisseur}', 'EtatController@listeApprovisionnementByPeriodeFournisseurPdf');

    //Inventaire
    Route::get('liste-inventaires-pdf', 'EtatController@listeInventairePdf');
    Route::get('liste-inventaires-by-depot-pdf/{depot}', 'EtatController@listeInventaireByDepotPdf');
    Route::get('liste-inventaires-by-periode-pdf/{debut}/{fin}', 'EtatController@listeInventaireByPeriodePdf');
    Route::get('liste-inventaires-by-periode-depot-pdf/{debut}/{fin}/{depot}', 'EtatController@listeInventaireByPeriodeDepotPdf');

    //Déstockage
    Route::get('liste-destockages-pdf', 'EtatController@listeDestockagePdf');
    Route::get('liste-destockages-by-depot-pdf/{depot}', 'EtatController@listeDestockageByDepotPdf');
    Route::get('liste-destockages-by-periode-pdf/{debut}/{fin}', 'EtatController@listeDestockageByPeriodePdf');
    Route::get('liste-destockages-by-periode-depot-pdf/{debut}/{fin}/{depot}', 'EtatController@listeDestockageByPeriodeDepotPdf');

    //Transferts de stocks
    Route::get('liste-transferts-stocks-pdf', 'EtatController@listeTransfertStockPdf');
    Route::get('liste-transferts-stocks-by-periode-pdf/{debut}/{fin}', 'EtatController@listeTransfertStockByPeriodePdf');

    //Articles
    Route::get('liste-articles-pdf', 'EtatController@listeArticlePdf');
    Route::get('liste-articles-by-categorie-pdf/{categorie}', 'EtatController@listeArticleByCategoriePdf');

    //Dépôts
    Route::get('liste-depots-pdf', 'EtatController@listeDepotPdf');

    //Clients
    Route::get('liste-clients-pdf', 'EtatController@listeClientPdf');
    Route::get('liste-clients-by-nation-pdf/{nation}', 'EtatController@listeClientByNationPdf');
    Route::get('liste-depots-pdf', 'EtatController@listeDepotPdf');
    Route::get('liste-transferts-stocks-pdf', 'EtatController@listeTransfertStockPdf');
    Route::get('liste-soldes-clients-pdf', 'EtatComptabiliteController@listeSoldeClientPdf');
    Route::get('liste-soldes-by-clients-pdf/{client}', 'EtatComptabiliteController@listeSoldeByClientPdf');
    Route::get('liste-factures-client-pdf/{client}', 'EtatComptabiliteController@listeFactureClientPdf');
    Route::get('liste-rglements-client-pdf/{client}', 'EtatComptabiliteController@listeRglementClientPdf');
    Route::get('liste-articles-achetes-by-client-pdf/{client}', 'EtatComptabiliteController@listeArticleAcheteByClientPdf');

    //Fournisseurs
    Route::get('liste-fournisseurs-pdf', 'EtatController@listeFournisseurPdf');
    Route::get('liste-fournisseurs-by-nation-pdf/{nation}', 'EtatController@listeFournisseurByNationPdf');
    Route::get('liste-soldes-fournisseurs-pdf', 'EtatComptabiliteController@listeSoldeFournisseurPdf');
    Route::get('liste-soldes-by-fournisseurs-pdf/{nation}', 'EtatComptabiliteController@listeSoldeByFournisseurPdf');
    Route::get('liste-bons-fournisseur-pdf/{fournisseur}', 'EtatComptabiliteController@listeBonFournisseurPdf');
    Route::get('liste-rglements-fournisseur-pdf/{fournisseur}', 'EtatComptabiliteController@listeRglementFournisseurPdf');
    Route::get('liste-articles-commandes-by-fournisseur-pdf/{fournisseur}', 'EtatComptabiliteController@listeArticleCommandeByFournisseurPdf');

    //Vente
    Route::get('liste-marges-sur-ventes-pdf', 'EtatComptabiliteController@listeMargeSurVentePdf');
    Route::get('liste-marges-sur-ventes-by-depot-pdf/{depot}', 'EtatComptabiliteController@listeMargeSurVenteByDepotPdf');
    Route::get('liste-marges-sur-ventes-by-periode-pdf/{debut}/{fin}', 'EtatComptabiliteController@listeMargeSurVenteByPeriodePdf');
    Route::get('liste-marges-sur-ventes-by-periode-depot-pdf/{debut}/{fin}/{depot}', 'EtatComptabiliteController@listeMargeSurVenteByPeriodeDepotPdf');

    //Fiscalités et timbre pdf
    Route::get('declaration-tva-pdf', 'EtatComptabiliteController@declarationTvaPdf');
    Route::get('timbre-fiscal-pdf', 'EtatComptabiliteController@timbreFiscalPdf');
});


//les routes du module Auth
Route::namespace('Auth')->middleware('auth')->name('auth.')->prefix('auth')->group(function () {
    //Route resources
    Route::resource('users', 'UserController');
    Route::resource('restaurages', 'RestaurageDataController');

    //Route pour les listes dans boostrap table
    Route::get('liste_users', 'UserController@listeUser')->name('liste_users');
    Route::get('liste_all_tables', 'RestaurageDataController@listeAllTable')->name('liste_all_tables');
    Route::get('liste-users-agences', 'UserController@listeUserAgence')->name('liste-users-agences');

    //Autres routes pour le profil
    Route::get('profil-informations', 'UserController@profil')->name('profil-informations');
    Route::get('infos-profil-to-update', 'UserController@infosProfiTolUpdate')->name('infos-profil-to-update');
    Route::put('update-profil/{id}', 'UserController@updateProfil');
    Route::get('update-password-page', 'UserController@updatePasswordPage');
    Route::post('update-password', 'UserController@updatePasswordProfil')->name('update-password');

    //Réinitialisation du mot de passe manuellement par l'administrateur
    Route::delete('/reset_password_manualy/{user}', 'UserController@resetPasswordManualy');

    //Routes particulières
    Route::post('verification-access', 'UserController@verificationAccess')->name('verification-access');
    Route::get('users-agence-canal', 'UserController@userAgenceCanal')->name('users-agence-canal');
    Route::post('add-user-agence', 'UserController@addUserAgence')->name('add-user-agence');
    Route::put('update-user-agence/{id}', 'UserController@updateUserAgence')->name('update-user-agence');
});

/*
|--------------------------------------------------------------------------
| ROUTE TELECHARGEMENT DES MODELES
|--------------------------------------------------------------------------
*/
Route::get('/modele/{nom_fichier}', function($nom_fichier){
    // return 'Bonsoir';
    return response()->download(storage_path('modeles/'.$nom_fichier));
})->name('modele.download');
