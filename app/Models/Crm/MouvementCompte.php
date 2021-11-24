<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MouvementCompte extends Model
{
    protected $table = 'mouvement_comptes';

    protected $fillable = ['compte_id', 'initiale', 'entree', 'sortie'];

    protected $dates = ['date_mouvement'];

    public function compte() {
        return $this->belongsTo('App\Models\Crm\Compte');
    }
}
