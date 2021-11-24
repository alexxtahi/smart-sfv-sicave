<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Regime extends Model
{
    use SoftDeletes;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */

    protected $fillable = ['libelle_regime','updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at'];
}
