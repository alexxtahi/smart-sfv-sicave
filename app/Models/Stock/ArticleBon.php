<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class ArticleBon extends Model
{
    protected $fillable = ['quantite_demande','quantite_recu','article_id','bon_commande_id','prix_article','updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
}
