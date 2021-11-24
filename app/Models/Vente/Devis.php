<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['numero_devis','client_id','proformat_devis','depot_id'];

    protected $dates = ['date_devis'];

    public function client() {
        return $this->belongsTo('App\Models\Crm\Client');
    }
    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }
}
