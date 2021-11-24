<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;

class AvoirDoit extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['montant','vente_id', 'bon_commande_id','regler'];

    protected $dates = ['date_operation'];

    public function vente() {
        return $this->belongsTo('App\Models\Vente\Vente');
    }

    public function bon_commande() {
        return $this->belongsTo('App\Models\Stock\BonCommande');
    }
}
