<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name','login', 'email','agence_id','localite_id', 'password', 'role','depot_id', 'contact', 'last_login_at','last_login_ip', 'confirmation_token','statut_compte','etat_user', 'updated_by', 'deleted_by', 'created_by',
    ];

    protected $dates = ['deleted_at','last_login_at'];

    public function depot() {
        return $this->belongsTo('App\Models\Stock\Depot');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
