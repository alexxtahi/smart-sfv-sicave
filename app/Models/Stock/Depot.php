<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depot extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['libelle_depot','adresse_depot','contact_depot','updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];

}
