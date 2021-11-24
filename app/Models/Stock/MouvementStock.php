<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MouvementStock extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['article_id','depot_id','unite_id','quantite_initiale','quantite_approvisionnee','quantite_vendue','quantite_destocker','quantite_transferee','quantite_retoutnee','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_mouvement','date_peremption'];
    
    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }
}
