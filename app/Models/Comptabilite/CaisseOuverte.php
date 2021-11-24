<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaisseOuverte extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['montant_ouverture','solde_fermeture','entree','motif_non_conformite', 'sortie','caisse_id','user_id', 'updated_by','deleted_by','created_by'];

    protected $dates = ['deleted_at','date_ouverture', 'date_fermeture'];

    public function caisse() {
        return $this->belongsTo('App\Models\Parametre\Caisse');
    }
    public function user() {
        return $this->belongsTo('App\User');
    }
}
