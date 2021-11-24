<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Casier extends Model
{
   use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['libelle_casier', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

}
