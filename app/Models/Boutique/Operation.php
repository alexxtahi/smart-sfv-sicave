<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operation extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['objet_operation','montant_operation','type_operation','concerne','vente_id','bon_commande_id','caisse_ouverte_id', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_operation'];
    
    public function caisse_ouverte() {
        return $this->belongsTo('App\Models\Boutique\CaisseOuverte');
    }
    public function vente() {
        return $this->belongsTo('App\Models\Boutique\Vente');
    }
    public function bon_commande() {
        return $this->belongsTo('App\Models\Boutique\BonCommande');
    }
}
