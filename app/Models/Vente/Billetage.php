<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billetage extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['caisse_ouverte_id','billet','quantite','updated_by','deleted_by','created_by'];

    protected $dates = ['deleted_at'];

}
