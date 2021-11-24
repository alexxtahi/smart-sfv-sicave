<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;

class ArticleDevis extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['quantite','prix','devis_id','article_id','depot_id','choix_prix'];

    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
}
