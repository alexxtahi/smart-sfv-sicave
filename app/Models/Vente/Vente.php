<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['numero_facture','numero_ticket','caisse_ouverte_id','moyen_reglement_id','depot_id','montant_a_payer','montant_payer','montant_carte_fidelite','client_id', 'acompte_facture', 'remise_id','proformat','attente', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at','date_vente'];

    public function moyen_reglement() {
        return $this->belongsTo('App\Models\Parametre\MoyenReglement');
    }
    public function client() {
        return $this->belongsTo('App\Models\Crm\Client');
    }
    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }
    public function caisse_ouverte() {
        return $this->belongsTo('App\Models\Comptabilite\CaisseOuverte');
    }
}
