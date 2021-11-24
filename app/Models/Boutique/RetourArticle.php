<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RetourArticle extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['vente_id','vente_materiel_id','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_retour'];
    
    public function vente() {
        return $this->belongsTo('App\Models\Boutique\Vente');
    }
    
    public function vente_materiel() {
        return $this->belongsTo('App\Models\Canal\VenteMateriel');
    }
}
