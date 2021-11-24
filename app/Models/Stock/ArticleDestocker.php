<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleDestocker extends Model
{
      use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['quantite_destocker','article_id','unite_id','destockage_id', 'updated_by','deleted_by','created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }
}
