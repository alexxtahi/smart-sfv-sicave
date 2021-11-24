<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleTransfert extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['article_id','quantite_depart','quantite_reception','unite_depart','unite_reception','transfert_stock_id','updated_by','deleted_by','created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function article() {
        return $this->belongsTo('App\Models\Stock\Article');
    }
    public function unite_depart() {
        return $this->belongsTo('App\Models\Parametre\Unite','unite_depart');
    }
    public function unite_reception() {
        return $this->belongsTo('App\Models\Parametre\Unite','unite_reception');
    }
}
