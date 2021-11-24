<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaire extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['libelle_inventaire','depot_id','categorie_id','sous_categorie_id','article_id', 'updated_by', 'deleted_by', 'created_by'];

    protected $dates = ['deleted_at','date_inventaire'];

    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }
}
