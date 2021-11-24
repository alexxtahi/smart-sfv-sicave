<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;

class Remise extends Model
{
    protected $fillable = ['libelle_remise','vente_id','montan_remise','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];

}
