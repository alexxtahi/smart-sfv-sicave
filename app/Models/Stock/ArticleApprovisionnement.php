<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleApprovisionnement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['quantite','article_id','approvisionnement_id','unite_id', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_peremption'];
    
    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }
}
