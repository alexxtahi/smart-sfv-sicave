<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoyenReglement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['libelle_moyen_reglement', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

}
