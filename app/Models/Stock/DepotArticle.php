<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class DepotArticle extends Model
{
    protected $fillable = ['article_id', 'depot_id','unite_id', 'quantite_disponible', 'prix_vente_detail','prix_vente_gros','prix_vente_demi_gros','promotion', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at','date_peremption','date_debut_promotion','date_fin_promotion'];

    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }

    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }

    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
}
