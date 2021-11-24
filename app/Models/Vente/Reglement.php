<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reglement extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['montant','reste','moyen_reglement_id','bon_commande_id', 'vente_id','scan_cheque','numero_cheque','updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at','date_reglement'];

    public function moyen_reglement() {
        return $this->belongsTo('App\Models\Parametre\MoyenReglement');
    }

    public function vente() {
        return $this->belongsTo('App\Models\Vente\Vente');
    }

    public function bon_commande() {
        return $this->belongsTo('App\Models\Stock\BonCommande');
    }
}
