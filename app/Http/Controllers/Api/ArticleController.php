<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parametre\Article;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function listeArticle()
    {
        $articles = Article::with('categorie', 'sous_categorie', 'taille', 'unite', 'rayon', 'rangee', 'param_tva', 'airsi_achat', 'airsi_vente', 'fournisseurs')
            ->leftjoin('depot_articles', 'depot_articles.article_id', '=', 'articles.id')
            ->select('articles.*', 'depot_articles.quantite_disponible as qteEnStock', DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
            ->Where('articles.deleted_at', null)
            ->orderBy('description_article', 'ASC')
            ->groupBy('depot_articles.article_id')
            ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
}
