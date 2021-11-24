<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    protected $fillable = ['description','montant_depense','categorie_depense_id', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at','date_operation'];

    public function categorie_depense() {
        return $this->belongsTo('App\Models\Parametre\CategorieDepense');
    }
}
