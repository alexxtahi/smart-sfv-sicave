<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;

class ArticleRetourne extends Model
{
    protected $fillable = ['retour_article_id','article_id','unite_id','quantite','quantite_vendue','prix_unitaire','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function article() {
        return $this->belongsTo('App\Models\Parametre\Article');
    }
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }
}
