<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['full_name_fournisseur','code_fournisseur','compte_banque_fournisseur','banque_id','compte_contribuable_fournisseur','contact_fournisseur','email_fournisseur','boite_postale_fournisseur','adresse_fournisseur','fax_fournisseur','nation_id', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

    public function nation() {
        return $this->belongsTo('App\Models\Parametre\Nation');
    }
    public function banque() {
        return $this->belongsTo('App\Models\Parametre\Banque');
    }
}
