<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unite extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['libelle_unite','quantite_unite', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

}
