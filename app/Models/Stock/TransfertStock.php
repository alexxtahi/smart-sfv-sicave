<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransfertStock extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['depot_depart_id','depot_arrivee_id','updated_by','deleted_by','created_by'];
    
    protected $dates = ['deleted_at','date_transfert'];
 
    public function depot_depart() {
        return $this->belongsTo('App\Models\Stock\Depot','depot_depart_id');
    }
    public function depot_arrivee() {
        return $this->belongsTo('App\Models\Stock\Depot','depot_arrivee_id');
    }
}
