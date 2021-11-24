<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonCommande extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['numero_bon','scan_facture','fournisseur_id','accompte','etat', 'updated_by','deleted_by','created_by'];

    protected $dates = ['deleted_at','date_bon','date_reception'];

    public function fournisseur() {
        return $this->belongsTo('App\Models\Crm\Fournisseur');
    }
}
