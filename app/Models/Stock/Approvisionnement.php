<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approvisionnement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['depot_id','fournisseur_id','scan_facture_fournisseur','acompte_approvisionnement','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_approvisionnement'];
    
    public function fournisseur() {
        return $this->belongsTo('App\Models\Crm\Fournisseur');
    }
    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }
}
