<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\BonCommande;
use App\Models\Parametre\Article;
use App\Models\Boutique\Vente;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function articlesEnvoiePeremption()
    {
        $now = date("Y-m-d");
        $articles = DepotArticle::where([['depot_articles.deleted_at', null], ['depot_articles.date_peremption', '>', $now]])
            ->join('depots', 'depots.id', '=', 'depot_articles.depot_id')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')->where('articles.stockable', 1)
            ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
            ->select('depot_articles.date_peremption', 'articles.description_article', 'depots.libelle_depot', 'unites.libelle_unite', DB::raw('DATE_FORMAT(depot_articles.date_peremption, "%d-%m-%Y") as date_peremptions'))
            ->orderBy('depot_articles.date_peremption', 'ASC')->get();

        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function articleEnvoiRupture()
    {
        $articles = Article::with('categorie', 'sous_categorie')
            ->join('depot_articles', 'depot_articles.article_id', '=', 'articles.id')
            ->join('depots', 'depots.id', '=', 'depot_articles.depot_id')
            ->select('articles.*', 'depots.libelle_depot', 'depot_articles.quantite_disponible as totalStock')
            ->Where([['articles.deleted_at', null], ['articles.stockable', 1]])
            ->whereRaw('articles.stock_mini >= depot_articles.quantite_disponible')
            ->orderBy('totalStock', 'ASC')->get();

        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public  function commandeEnCours()
    {
        $commandes = BonCommande::with('fournisseur')
            ->join('article_bons', 'article_bons.bon_commande_id', '=', 'bon_commandes.id')
            ->select('bon_commandes.*', DB::raw('sum(article_bons.quantite_demande*article_bons.prix_article) as montantBon'), DB::raw('DATE_FORMAT(bon_commandes.date_bon_commande, "%d-%m-%Y") as date_bon_commandes'))
            ->Where([['bon_commandes.deleted_at', null], ['livrer', 0]])
            ->orderBy('bon_commandes.date_bon_commande', 'DESC')
            ->groupBy('bon_commandes.id')->get();
        $jsonData["rows"] = $commandes->toArray();
        $jsonData["total"] = $commandes->count();
        return response()->json($jsonData);
    }

    public function besteClients()
    {
        $clients = Vente::where([['ventes.deleted_at', null], ['ventes.proformat', 0]])
            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', null], ['article_ventes.retourne', 0]])
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->select('clients.full_name_client', 'clients.contact_client', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
            ->groupBy('ventes.client_id')
            ->orderBy('sommeTotale', 'DESC')->get();
        $jsonData["rows"] = $clients->toArray();
        $jsonData["total"] = $clients->count();
        return response()->json($jsonData);
    }

    public function clientsPlusEndettes()
    {
        $clients = Vente::where([['ventes.deleted_at', null], ['ventes.proformat', 0]])
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', null], ['article_ventes.retourne', 0]])
            ->select('clients.full_name_client', 'clients.contact_client', 'clients.adresse_client', DB::raw('sum(ventes.acompte_facture) as accompteTotale'), DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
            ->groupBy('ventes.client_id')
            ->orderBy(DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne)-sum(ventes.acompte_facture)'), 'DESC')
            ->take(5)->get();
        $jsonData["rows"] = $clients->toArray();
        $jsonData["total"] = $clients->count();
        return response()->json($jsonData);
    }
}
