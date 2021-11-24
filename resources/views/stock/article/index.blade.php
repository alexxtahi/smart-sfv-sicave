@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<!--
<div class="col-md-12" style="margin-bottom: 10px;padding-right:0">
    <h1>
        <button type="button" data-toggle="modal" data-target="#ModalImportationEnMasse" class="btn btn-sm btn-primary pull-right" style="margin-left:10px">
            <i class="fa fa-download"></i>&nbsp; Ajout en masse
        </button>
        <a href="{{route('stock.article.dowload_model')}}" role="button" class="btn btn-sm btn-secondary pull-right" style="background:#F9F9F9">
            <i class="fa fa-upload"></i>&nbsp; Télécharger le modèle
        </a>
    </h1>
</div>
-->
<div class="col-md-3">
    <select class="form-control" id="searchByCategorie">
        <option value="0">-- Toutes les cat&eacute;gories --</option>
        @foreach($categories as $categorie)
        <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByLibbele" placeholder="Rechercher par nom de l'article">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByCode" placeholder="Rech. par code barre">
    </div>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-articles'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="true">
    <thead>
        <tr>
            <th data-field="code_article">Code créé</th>
            <th data-field="libelle_article" data-searchable="true" data-sortable="true">Article</th>
            <th data-field="categorie.libelle_categorie">Cat&eacute;gorie </th>
            <th data-field="sous_categorie.libelle_categorie" data-visible="false">Sous cat&eacute;gorie </th>
            <th data-formatter="prixAchatHtFormatter">Prix Achat HT</th>
            <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix Achat TTC</th>
            <th data-field="prix_vente_ttc" data-formatter="montantFormatter">Prix Vente TTC</th>
            <th data-field="fournisseurs" data-formatter="fournisseursFormatter" data-visible="false">Fournisseur(s)</th>
            <!--<th data-formatter="tvaFormatter">TVA</th>
            <th data-field="rayon.libelle_rayon" data-visible="false">Rayon </th>
            <th data-field="rangee.libelle_rangee" data-visible="false">Rangee&eacute;e </th>-->
            <th data-align="center" data-formatter="stockMinFormatter">Stock Alerte</th>
            <th data-align="center" data-formatter="stockMaxFormatter" data-visible="false">Stock Max. </th>
            <th data-field="image_article" data-align="center" data-formatter="imageFormatter" data-visible="false">Image </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 75%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-cubes fa-2x"></i>
                        Gestion des articles
                    </span>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" id="idArticleModifier" name="idArticle" ng-hide="true" ng-model="article.id"/>
                    @csrf
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#article_info" data-toggle="tab" aria-expanded="true">D&eacute;tails de l'article</a>
                            </li>
                            <li class="">
                                <a href="#info_supl" data-toggle="tab" aria-expanded="true">Infos suplementaires</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="article_info">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Code crée</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-barcode"></i>
                                                </div>
                                                <input type="text" class="form-control" ng-model="article.code_article" id="code_article" name="code_article" placeholder="Code de l'article">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Code article</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-barcode"></i>
                                                </div>
                                                <input type="text" class="form-control" ng-model="article.code_barre" id="code_barre" name="code_barre[]" placeholder="Code barre de l'article">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Libell&eacute; de l'article *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-edit"></i>
                                                </div>
                                                <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="article.libelle_article" id="libelle_article" name="libelle_article" placeholder="Libellé de l'article" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Selectionner les fournisseurs de cet article *</label>
                                        <div class="form-group">
                                            <select name="fournisseurs[]" id="fournisseurs" class="form-control select2" multiple="multiple" required>
                                                @foreach($fournisseurs as $fournisseur)
                                                <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cat&eacute;gorie *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <select name="categorie_id" id="categorie_id" ng-model="article.categorie_id" class="form-control" required>
                                                    <option value="" ng-show="false"> Selectionner la cat&eacute;gorie </option>
                                                    @foreach($categories as $categorie)
                                                    <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Sous cat&eacute;gorie </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <select name="sous_categorie_id" id="sous_categorie_id" ng-model="article.sous_categorie_id" class="form-control">
                                                    <option value="" ng-show="false"> Selectionner la cat&eacute;gorie </option>
                                                    @foreach($sous_categories as $sous_categorie)
                                                    <option value="{{$sous_categorie->id}}"> {{$sous_categorie->libelle_categorie}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>TVA *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-text-height"></i>
                                                </div>
                                                <select name="param_tva_id" id="param_tva_id" ng-model="article.param_tva_id" class="form-control" required>
                                                    @foreach($param_tvas as $tva)
                                                    <option selected data-tva="{{$tva->tva}}" value="{{$tva->id}}"> {{$tva->tva*100}}%</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix d'achat HT </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" class="form-control"  id="prix_achat_ht" placeholder="Prix d'achat ht" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix d'achat TTC *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" pattern="[0-9]*" class="form-control" ng-model="article.prix_achat_ttc" id="prix_achat_ttc" name="prix_achat_ttc" placeholder="Prix d'achat ttc" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="div_enregistrement">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Prix de vente TTC *</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-money"></i>
                                                        </div>
                                                        <input type="number" min="0" class="form-control" id="prix_details" placeholder="Prix de vente TTC" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Marge</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-money"></i>
                                                        </div>
                                                        <input type="text" class="form-control"  id="marge" placeholder="Marge" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>D&eacute;p&ocirc;t *</label>
                                                    <select id="depot" class="form-control">
                                                        <option value="">Choisir un d&eacute;p&ocirc;t </option>
                                                        @foreach($depots as $depot)
                                                            @if ($depot->id == 2)
                                                                <option data-libelle="{{$depot->libelle_depot}}" value="{{$depot->id}}" selected> {{$depot->libelle_depot}}</option>
                                                            @else
                                                                <option data-libelle="{{$depot->libelle_depot}}" value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                                    <!--
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Prix de vente HT</label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-money"></i>
                                                                </div>
                                                                <input type="text" class="form-control"  id="prix_achat_ht" placeholder="Prix de vente HT" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                -->
                                            <!--<div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Prix d&eacute;tail *</label>-->
                                                    <input type="hidden" min="0" class="form-control" id="prix_detail" placeholder="prix détail">
                                                <!--</div>
                                            </div>-->
                                            <!--<div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Prix demi gros *</label>-->
                                                    <input type="hidden" min="0" class="form-control" id="prix_demis_gros" placeholder="prix demi gros">
                                                <!--</div>
                                            </div>-->
                                            <!--<div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Prix en gros *</label>-->
                                                    <input type="hidden" min="0" class="form-control" id="prix_gros" placeholder="prix en gros">
                                                <!--</div>
                                            </div>-->

                                            <div class="col-md-3">
                                                <h5 class="text-bold text-red">
                                                <label><br/>
                                                    <input type="checkbox" id="non_stockable" name="non_stockable" ng-model="article.non_stockable" ng-checked="article.non_stockable==1">&nbsp; Article non stockable
                                                </label>
                                                </h5>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="text-bold">
                                                <label><br/>
                                                    <input type="checkbox" id="tous_les_depots" name="tous_les_depots"> Ajouter dans tous les dépôts
                                                </label>
                                                </h5>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group"><br/>
                                                    <button type="button" class="btn btn-success btn-xs  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                                </div>
                                            </div>
                                        </div><br/>
                                        <table class="table table-info table-striped box box-success">
                                            <thead>
                                                <tr>
                                                    <th>Cochez</th>
                                                    <th>D&eacute;p&ocirc;t</th>
                                                    <th>Prix de vente</th>
                                                    <!--<th>Prix d&eacute;tail</th>
                                                    <th>Prix demi gros</th>
                                                    <th>Prix en gros</th>-->
                                                </tr>
                                            </thead>
                                            <tbody class="articles-depot-info">

                                            </tbody>
                                        </table>
                                        <button type="button" class="delete-row">Supprimer ligne</button>
                                    </div>
                                    <!--
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Stock d'alerte </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-battery-quarter"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.stock_mini" id="stock_mini" name="stock_mini" placeholder="Stock minimum">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-photo"></i>
                                                </div>
                                                <input type="file" class="form-control" name="image_article">
                                            </div>
                                        </div>
                                    </div>
                                -->
                                </div>
                                <div class="row">
                                    <!--
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Stock maximum</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-battery-full"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.stock_max" id="stock_max" name="stock_max" placeholder="Stock maximum">
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>
                            <div class="tab-pane" id="info_supl">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Poids net</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-balance-scale"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.poids_net" id="poids_net" name="poids_net" placeholder="Poids net">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Poids brut</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-balance-scale"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.poids_brut" id="poids_brut" name="poids_brut" placeholder="Poids brut">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Rayon</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-arrows-h"></i>
                                                </div>
                                                <select name="rayon_id" id="rayon_id" ng-model="article.rayon_id" class="form-control">
                                                    <option value="" ng-show="false">Selectionner le rayon </option>
                                                    @foreach($rayons as $rayon)
                                                    <option value="{{$rayon->id}}"> {{$rayon->libelle_rayon}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Rang&eacute;e</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-bars"></i>
                                                </div>
                                                <select name="rangee_id" id="rangee_id" ng-model="article.rangee_id" class="form-control">
                                                    <option value="" ng-show="false">Selectionner la rang&eacute;e </option>
                                                    @foreach($rangees as $rangee)
                                                    <option value="{{$rangee->id}}"> {{$rangee->libelle_rangee}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Taille</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-text-height"></i>
                                                </div>
                                                <select name="taille_id" id="taille_id" ng-model="article.taille_id" class="form-control">
                                                    <option value=""> Selectionner la taille</option>
                                                    @foreach($tailles as $taille)
                                                    <option value="{{$taille->id}}"> {{$taille->libelle_taille}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Volume</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-wifi"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.volume" id="volume" name="volume" placeholder="Volume">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div id="div_update">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="btnModalAjoutArticleDepot" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus">Ajouter un enregistrement</i></button>
                                </div>
                            </div>
                        </div><br/>
                        <table id="tableArticleDepot" class="table table-success table-striped box box-success"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t</th>
                                    <th data-field="prix_vente_detail" data-formatter="montantFormatter">Prix de vente TTC</th>
                                    <!--<th data-field="prix_vente_demi_gros" data-formatter="montantFormatter">Prix demi gros </th>
                                    <th data-field="prix_vente_gros" data-formatter="montantFormatter">Prix en gros </th>-->
                                    <th data-field="id" data-formatter="optionArticleDepotFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal add depot-article -->
<div class="modal fade bs-modal-add-article-depot" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:70%">
        <form id="formAjoutArticleDepot" ng-controller="formAjoutArticleDepotCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Ajout d'un enregistrement
                </div>
                @csrf
                <div class="modal-body ">
                   <input type="text" class="hidden" id="idDepotArticleModifier"  ng-model="depotArticle.id"/>
                   <input type="text" class="hidden" id="article_id"  name="article_id"/>
                   <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                               <label>D&eacute;p&ocirc;t *</label>
                               <select name="depot_id" id="depot_id_add" ng-model="depotArticle.depot_id" ng-init="deporArticle.depot_id=''" class="form-control" required>
                                   <option value="" ng-show="false">Choisir un d&eacute;p&ocirc;t </option>
                                   @foreach($depots as $depot)
                                   <option data-libelle="{{$depot->libelle_depot}}" value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                   @endforeach
                               </select>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                               <label>Prix de vente TTC *</label>
                               <input type="number" class="form-control" min="0" id="prix_vente_detail_add" name="prix_vente_detail" ng-model="depotArticle.prix_vente_detail" placeholder="Prix de vente TTC" required>
                           </div>
                        </div>
                        <!--<div class="col-md-2">
                           <div class="form-group">
                               <label>Prix demi gros *</label>-->
                               <input type="hidden" class="form-control" min="0" id="prix_vente_demi_gros_add" name="prix_vente_demi_gros" ng-model="depotArticle.prix_vente_demi_gros" placeholder="prix demi gros" required>
                           <!--</div>
                        </div>-->
                        <!--<div class="col-md-2">
                           <div class="form-group">
                               <label>Prix en gros *</label>-->
                               <input type="hidden" class="form-control" min="0" id="prix_vente_gros_add" name="prix_vente_gros" ng-model="depotArticle.prix_vente_gros" placeholder="prix en gros" required>
                           <!--</div>
                        </div>-->
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion -->
<div class="modal fade bs-modal-suppression" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimer" ng-controller="formSupprimerCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idArticleSupprimer"  ng-model="article.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'article <br/><b>@{{article.libelle_article}}</b></div>
                        <div class="text-center vertical processing">Suppression en cours</div>
                        <div class="pull-right">
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Non</button>
                            <button type="submit" class="btn btn-danger btn-sm ">Oui</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion depot-article-->
<div class="modal fade bs-modal-supprimer-depot-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimerDepotArticle" ng-controller="formSupprimerDepotArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idDepotArticleSupprimer"  ng-model="depotArticle.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le d&eacute;p&ocirc;t <br/><b>@{{depotArticle.depot.libelle_depot}}</b></div>
                        <div class="text-center vertical processing">Suppression en cours</div>
                        <div class="pull-right">
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Non</button>
                            <button type="submit" class="btn btn-danger btn-sm ">Oui</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<x-modal-creation-en-masse action="{{route('stock.article.store_from_upload')}}"/>

<script type="text/javascript">
    var ajout = true;
    var ajoutArticleDepot = true;
    var $table = jQuery("#table"), rows = [], $tableArticleDepot = jQuery("#tableArticleDepot"), rowsArticleDepot = [];
    var fournisseurs = {!! json_encode($fournisseurs) !!};

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (article) {
            $scope.article = article;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.article = {};
        };
    });
    appSmarty.controller('formAjoutArticleDepotCtrl', function ($scope) {
        $scope.populateFormModif = function (depotArticle) {
            $scope.depotArticle = depotArticle;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.depotArticle = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (article) {
            $scope.article = article;
        };
        $scope.initForm = function () {
            $scope.article = {};
        };
    });
     appSmarty.controller('formSupprimerDepotArticleCtrl', function ($scope) {
        $scope.populateFormSup = function (depotArticle) {
            $scope.depotArticle = depotArticle;
        };
        $scope.initForm = function () {
            $scope.depotArticle = {};
        };
    });

    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $tableArticleDepot.on('load-success.bs.table', function (e, data) {
            rowsArticleDepot = data.rows;
        });
        $("#fournisseurs").select2({width: '100%'});
        $("#div_enregistrement").show();
        $("#div_update").hide();

        $("#prix_details").change(function () {
            var prix_vente_ttc = $("#prix_details").val();
            var prix_achat_ttc = $("#prix_achat_ttc").val();
            if (prix_vente_ttc != "" && prix_achat_ttc != "" && prix_vente_ttc != null && prix_achat_ttc != null) {
                if (prix_vente_ttc > prix_achat_ttc) {
                    var marge = prix_vente_ttc - prix_achat_ttc;
                    $("#marge").val(marge);
                } else {
                    $("#marge").val("erreur");
                }
            }
        });

        $("#prix_details").keyup(function (e) {
            var prix_vente_ttc = $("#prix_details").val();
            var prix_achat_ttc = $("#prix_achat_ttc").val();
            if (prix_vente_ttc != "" && prix_achat_ttc != "" && prix_vente_ttc != null && prix_achat_ttc != null) {
                //if (prix_vente_ttc > prix_achat_ttc) {
                    var marge = prix_vente_ttc - prix_achat_ttc;
                    $("#marge").val(marge);
                /*} else {
                    $("#marge").val("erreur");
                }*/
            }
        });

        $("#prix_achat_ttc").keyup(function (e) {
            var prix_vente_ttc = $("#prix_details").val();
            var prix_achat_ttc = $("#prix_achat_ttc").val();
            if (prix_vente_ttc != "" && prix_achat_ttc != "" && prix_vente_ttc != null && prix_achat_ttc != null) {
                //if (prix_vente_ttc > prix_achat_ttc) {
                    var marge = prix_vente_ttc - prix_achat_ttc;
                    $("#marge").val(marge);
                /*} else {
                    $("#marge").val("erreur");
                }*/
            }
        });

        $("#tous_les_depots").change(function () {
            var depotCheckbox = $("#tous_les_depots");
            if (depotCheckbox.is(':checked')) {
                // alert('Coché'); // test
                $("#depot").val("13");
            } else {
                // alert('Décoché'); // test
                $("#depot").val("");
            }
        });

        $("#prix_vente_detail_add").change(function () {
            var prixVente = $("#prix_vente_detail_add").val();
            $("#prix_vente_demi_gros_add").val(prixVente);
            $("#prix_vente_gros_add").val(prixVente);
        });

        $("#prix_achat_ttc").keyup(function (e) {
            var prix_achat_ttc = $("#prix_achat_ttc").val();
            var tva = $("#param_tva_id").children(":selected").data("tva");
            var prix_achat_ht = (prix_achat_ttc/(tva + 1));
            var prix = Math.round(prix_achat_ht);
            $("#prix_achat_ht").val(prix);
            //Calcule de marge et de marque
            if($("#prix_vente_ht").val()!=""){
                var prix_vente = $("#prix_vente_ht").val();
                var prix_achat = $("#prix_achat_ht").val();
                var marge_commercial = parseInt(prix_vente) - parseInt(prix_achat);
                var taux_marge = (marge_commercial/prix_achat)*100;
                var taux_marque = (marge_commercial/prix_vente)*100;
                var taux_marg = Math.round(taux_marge);
                var taux_marq = Math.round(taux_marque);
                $("#taux_marge").val(taux_marg);
                $("#taux_marque").val(taux_marq);
            }
        });

        $("#param_tva_id").change(function (e) {
            var tva = $("#param_tva_id").children(":selected").data("tva");
            if($("#prix_achat_ttc").val()!=""){
                var prix_achat_ttc = $("#prix_achat_ttc").val();
                var tva = $("#param_tva_id").children(":selected").data("tva");
                var prix_achat_ht = (prix_achat_ttc/(tva + 1));
                var prix = Math.round(prix_achat_ht);
                $("#prix_achat_ht").val(prix);
                //Calcule de marge et de marque
                if($("#prix_vente_ht").val()!=""){
                    var prix_vente = $("#prix_vente_ht").val();
                    var prix_achat = $("#prix_achat_ht").val();
                    var marge_commercial = parseInt(prix_vente) - parseInt(prix_achat);
                    var taux_marge = (marge_commercial/prix_achat)*100;
                    var taux_marque = (marge_commercial/prix_vente)*100;
                    var taux_marg = Math.round(taux_marge);
                    var taux_marq = Math.round(taux_marque);
                    $("#taux_marge").val(taux_marg);
                    $("#taux_marque").val(taux_marq);
                }
            }
        });

        $("#searchByCategorie").change(function (e) {
            var categorie = $("#searchByCategorie").val();
            if(categorie == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-articles'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-articles-by-categorie/' + categorie});
            }
        });

        $("#searchBySousCategorie").change(function (e) {
            var sous_categorie = $("#searchBySousCategorie").val();
            if(sous_categorie == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-articles'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-articles-by-sous-categorie/' + sous_categorie});
            }
        });

        $("#searchByLibbele").keyup(function (e) {
            var libelle = $("#searchByLibbele").val();
            if(libelle == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-articles'])}}"});
            } else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-articles-by-libelle/' + libelle});
            }
        });
        $("#searchByCode").keyup(function (e) {
            var code = $("#searchByCode").val();
            if(code == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-articles'])}}"});
            } else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-articles-by-code/' + code});
            }
        });

        $("#btnModalAjoutArticleDepot").on("click", function () {
            ajoutArticleDepot = true;
            var article = $("#idArticleModifier").val();
            document.forms["formAjoutArticleDepot"].reset();
            $("#article_id").val(article);
            $(".bs-modal-add-article-depot").modal("show");
        });

        $("#btnModalAjout").on("click", function () {
            $("#fournisseurs").select2("val", "");
            $("#prix_achat_ht").val("");
            $("#div_enregistrement").show();
            $("#div_update").hide();
        });

        $(".add-row").click(function (e) {
            //alert('Cest djinzin'); // test
            if($("#depot").val() != '' && $("#prix_details").val() != '') {
                // if ($("#depot").val() == 'tous_les_depots') {
                //     var libelle_depot = 'Tous les dépôts';
                // } else {
                //     var libelle_depot = $("#depot").children(":selected").data("libelle");
                // }
                var libelle_depot = $("#depot").children(":selected").data("libelle");
                var depot = $("#depot").val();
                var prix_detail = $("#prix_details").val();
                var prix_demi_gros = $("#prix_details").val();
                var prix_gros = $("#prix_details").val();

                // var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='hidden' name='depots[]' value='" + depot + "'>" + libelle_depot + "</td><td><input type='hidden' name='prix_details[]' value='" + prix_detail + "'>" + prix_detail + "</td><td><input type='hidden' name='prix_demis_gros[]' value='" + prix_demi_gros + "'>" + prix_demi_gros + "</td><td><input type='hidden' name='prix_gros[]' value='" + prix_gros + "'>" + prix_gros + "</td></tr>";
                var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='hidden' name='depots[]' value='" + depot + "'>" + libelle_depot + "</td><td><input type='hidden' name='prix_details[]' value='" + prix_detail + "'>" + prix_detail + "</td></tr>";
                $(".articles-depot-info").append(markup);
                $("#depot").val("");
                $("#prix_detail").val("");
                $("#prix_demis_gros").val("");
                $("#prix_gros").val("");
            }else{
                alert("Les champs dépôt et le prix de vente ne doivent pas être vide!");
            }
        });

         // Find and remove selected table rows
        $(".delete-row").click(function () {
            $(".articles-depot-info").find('input[name="record"]').each(function () {
                if ($(this).is(":checked")) {
                    $(this).parents("tr").remove();
                }else{
                   alert("Cochez la ligne que vous souhaitez supprimer !");
                }
            });
        });

        $("#formAjout").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjout .loader-overlay");

             if (ajout==true) {
                var methode = 'POST';
                var url = "{{route('stock.articles.store')}}";
             }else{
                var methode = 'POST';
                var url = "{{route('stock.update-article')}}";
             }
             var formData = new FormData($(this)[0]);
            editerArticleAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });

        $("#formAjoutArticleDepot").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutArticleDepot .loader-overlay");

             if (ajoutArticleDepot==true) {
                var methode = 'POST';
                var url = "{{route('stock.depot-articles.store')}}";
             }else{
                var id = $("#idDepotArticleModifier").val();
                var methode = 'PUT';
                var url = 'depot-articles/' + id;
             }
            editerArticleDepotAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticleDepot, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('articles/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });

         $("#formSupprimerDepotArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idDepotArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerDepotArticle .question");
            var $ajaxLoader = $("#formSupprimerDepotArticle .processing");
            supprimerDepotArticleAction('depot-articles/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticleDepot);
        });
    });

    function updateRow(idArticle) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var article =_.findWhere(rows, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateForm(article);
        });
        $("#fournisseurs").val(article.fournisseurs).trigger('change');
        $("#code_barre").val(article.code_barre).trigger('change');

        $tableArticleDepot.bootstrapTable('refreshOptions', {url: "../stock/liste-depot-by-article/" + idArticle});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        //TVA
        tva = article.param_tva.tva;
        $("#param_tva_id").val(article.param_tva_id);

        //Achat HT
        var prix_achat_ttc = article.prix_achat_ttc;
        var prix_achat_ht = (prix_achat_ttc/(tva + 1));
        var prixA = Math.round(prix_achat_ht);
        $("#prix_achat_ht").val(prixA);

        $(".bs-modal-ajout").modal("show");
    }
    function updateDepoArticleRow(idArticleDepot) {
        ajoutArticleDepot = false;
        var $scope = angular.element($("#formAjoutArticleDepot")).scope();
        var depotArticle =_.findWhere(rowsArticleDepot, {id: idArticleDepot});
         $scope.$apply(function () {
            $scope.populateFormModif(depotArticle);
        });
         $(".bs-modal-add-article-depot").modal("show");
    }
    function deleteRow(idArticle) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var article =_.findWhere(rows, {id: idArticle});
           $scope.$apply(function () {
              $scope.populateForm(article);
          });
       $(".bs-modal-suppression").modal("show");
    }
    function deleteDepoArticleRow(idArticleDepot) {
          var $scope = angular.element($("#formSupprimerDepotArticle")).scope();
          var depotArticle =_.findWhere(rowsArticleDepot, {id: idArticleDepot});
           $scope.$apply(function () {
              $scope.populateFormSup(depotArticle);
          });
       $(".bs-modal-supprimer-depot-article").modal("show");
    }


    function fournisseursFormatter(items){
            var strFournisseurs = " ";
            $.each(fournisseurs, function(key, item) {
                if(items.includes(item.id)){
                    strFournisseurs += "<b>" + item.full_name_fournisseur + "<b><br/>";
                }
            })
            return strFournisseurs;
    }

    function tvaFormatter(id, row){
        var tva = row.param_tva.tva*100;
        return '<span class="text-bold">' + tva.toFixed(2) + ' %' + '</span>';
    }

    function prixAchatHtFormatter(id, row){
        var prix_achat = row.prix_achat_ttc;
        tva = row.param_tva.tva;
        var prix_achat_ht = (prix_achat/(tva + 1));
        var prix = Math.round(prix_achat_ht);
        return '<span class="text-bold">' + prix + '</span>';
    }

    function codeBarreFormatter(code){
        var codes = JSON.parse(code.toString())
        var strCode= " ";
        $.each(codes, function(key, item) {
            strCode += "<b> -- " + item + "<b/>";
        })
        return strCode;
    }

    function imageFormatter(image) {
          return image ? "<a target='_blank' href='" + basePath + '/' + image + "'>Voir le l'image</a>" : "";
    }
    function montantFormatter(montant){
        return montant ? '<span class="text-bold">' + $.number(montant)+ '</span>' : "--";
    }

    function stockMinFormatter(id, row){
        return !row.non_stockable ? '<span class="text-bold">' + $.number(row.stock_mini)+ '</span>' : '<span class="text-bold"> Non stockable </span>';
    }
    function stockMaxFormatter(id, row){
        return !row.non_stockable ? '<span class="text-bold">' + $.number(row.stock_max)+ '</span>' : '<span class="text-bold"> Non stockable </span>';
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function optionArticleDepotFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateDepoArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteDepoArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function editerArticleAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                    $("#fournisseurs").select2("val", "");
                    $("#prix_achat_ht").val("");
                    $(".articles-depot-info").find('input[name="record"]').each(function () {
                        $(this).parents("tr").remove();
                    });

                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
         },
          error: function (err) {
            var res = eval('('+err.responseText+')');
            var messageErreur = res.message;

            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: messageErreur,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
         beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        },
    });
};

    function editerArticleDepotAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajoutArt = true) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-add-article-depot").modal("hide");
                    ajout = false;
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-add-article-depot").modal("hide");
                    ajout = false;
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
         },
          error: function (err) {
            var res = eval('('+err.responseText+')');
            var messageErreur = res.message;

            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: messageErreur,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
         beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        },
    });
};

 //Supprimer un dépôt
    function supprimerDepotArticleAction(url, formData, $question, $ajaxLoader, $table) {
    jQuery.ajax({
        type: 'DELETE',
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                 $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [reponse.data.id]
                });
                $table.bootstrapTable('refresh');
                $(".bs-modal-supprimer-depot-article").modal("hide");
                ajout = false;
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
        },
        error: function (err) {
            var res = eval('('+err.responseText+')');
            //alert(res.message);
            //alert(Object.getOwnPropertyNames(res));
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: res.message,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png"
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


