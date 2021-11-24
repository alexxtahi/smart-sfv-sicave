<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    protected $table = 'comptes';

    protected $fillable = ['client_id','fournisseur_id','carte_id','numero_compte','entree','sortie'];

    public function client() {
        return $this->belongsTo('App\Models\Crm\Client');
    }

    public function fournisseur() {
        return $this->belongsTo('App\Models\Crm\Fournisseur');
    }
    
    public function carte() {
        return $this->belongsTo('App\Models\Parametre\CarteFidelite','carte_id');
    }
}
