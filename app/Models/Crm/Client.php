<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['full_name_client','code_client','plafond_client','compte_contribuable_client','contact_client','email_client','boite_postale_client','adresse_client','fax_client','nation_id','regime_id', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

    public function nation() {
        return $this->belongsTo('App\Models\Parametre\Nation');
    }
    public function regime() {
        return $this->belongsTo('App\Models\Crm\Regime');
    }
}
