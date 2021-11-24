<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;

class Promotions extends Model
{
    protected $fillable = ['article_id','depot_id','unite_id','prix_promotion','en_promotion', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_debut','date_fin'];
    
    public function article() {
        return $this->belongsTo('App\Models\Parametre\Article');
    }
    public function depot() {
        return $this->belongsTo('App\Models\Parametre\Depot');
    }
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }
}
