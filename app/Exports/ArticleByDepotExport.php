<?php

namespace App\Exports;

use App\Models\Boutique\DepotArticle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArticleByDepotExport implements FromCollection, WithMapping, WithHeadings
{
    protected $id;
    function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * @return Collection
     */
    public function collection()
    {
        $articles = DepotArticle::where('depot_articles.depot_id', $this->id)
            ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
            ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
            ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
            ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
            ->get();
        return $articles;
    }
    public function map($article): array
    {
        $article->param_tva_id != null ? $tva = $article->montant_tva : $tva = 0;
        $prix_vente_ttc = $article->prix_vente;
        $prix_achat_ttc = $article->prix_achat_ttc;

        $prix_vente_ht = ($prix_vente_ttc / ($tva + 1));
        $prixVHT = round($prix_vente_ht, 0);

        $prix_achat_ht = ($prix_achat_ttc / ($tva + 1));
        $prixAHT = round($prix_achat_ht, 0);

        return [
            $article->code_barre,
            $article->description_article,
            $article->libelle_unite,
            $article->quantite_disponible,
            $prixAHT,
            $article->prix_achat_ttc,
            $prixVHT,
            $article->prix_vente,
            $article->prix_vente * $article->quantite_disponible,
        ];
    }

    public function headings(): array
    {
        return [
            'Code barre',
            'Article',
            'Lot',
            'En stock',
            'Prix Achat HT',
            'Prix Achat TTC',
            'Prix Vente HT',
            'Prix Vente TTC',
            'Montant TTC vente ',
        ];
    }
}
