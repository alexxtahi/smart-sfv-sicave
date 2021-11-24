<?php

namespace App\Models\Vente;

use Illuminate\Database\Eloquent\Model;

class Benefice extends Model
{
    protected $fillable = ['caisse_ouvert_id','montant'];
}
