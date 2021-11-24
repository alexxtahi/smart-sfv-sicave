<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['libelle_categorie', 'categorie_id','updated_by', 'deleted_by', 'created_by', 'slug'];

    protected $dates = ['deleted_at'];

    public function categorie(){
        return $this->belongsTo('App\Models\Parametre\Categorie', 'categorie_id');
    }
}
