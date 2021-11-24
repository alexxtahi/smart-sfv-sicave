<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'auth'], function () {

    // Routes sans authentification
    Route::post('login', 'Api\AuthController@login');
    Route::post('reset-password', 'Auth\UserController@resetPasswordAuto');
  
    Route::group(['middleware' => 'auth:api'], function() {

        //Route user
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\AuthController@user');
        
        //Route home
        Route::get('articles-en-voie-peremption', 'Api\HomeController@articlesEnvoiePeremption');
        Route::get('articles-en-voie-rupture', 'Api\HomeController@articleEnvoiRupture');
        Route::get('commande-en-cours', 'Api\HomeController@commandeEnCours');
        Route::get('beste-clients', 'Api\HomeController@besteClients');
        Route::get('clients-plus-endettes', 'Api\HomeController@clientsPlusEndettes');

        //Route parametre 
        Route::get('regimes', 'Api\ParametreController@listeRegime');
        Route::get('nations', 'Api\ParametreController@listeNation');

        //Route articles 
        Route::get('articles', 'Api\ArticleController@listeArticle');

        //Route client 
        Route::post('client/store', 'Api\ClientController@store');
        Route::get('clients', 'Api\ClientController@listeClient');
        Route::put('client/update/{id}', 'Api\ClientController@update');
        Route::delete('client/delete/{id}', 'Api\ClientController@destroy');
        Route::get('liste-achats-client/{id}', 'Api\ClientController@listeAchatsClient');

        //Route fournisseur
        Route::post('fournisseur/store', 'Api\FournisseurController@store');
        Route::get('fournisseurs', 'Api\FournisseurController@listeFournisseur');
        Route::put('fournisseur/update/{id}', 'Api\FournisseurController@update');
        Route::delete('fournisseur/delete/{id}', 'Api\FournisseurController@destroy');
    });

});
