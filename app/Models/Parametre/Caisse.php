<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    protected $fillable = ['libelle_caisse','depot_id', 'ouvert', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];

    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }

     //Obtenir les caisses d'un dépôt
     public static function getCaissesByDepot($depot){
        $caisses = Caisse::where('depot_id',$depot)->get();
        return $caisses;
    }
}
