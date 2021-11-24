<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destockage extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['motif','depot_id','updated_by','deleted_by','created_by'];
    
    protected $dates = ['deleted_at','date_destockage'];
    
    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }
}
