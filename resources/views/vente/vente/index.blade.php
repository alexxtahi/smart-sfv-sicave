@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="col-md-3">
    <select class="form-control" id="searchByClient">
        <option value="tous">-- Tous les clients --</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByDepot">
        <option value="0">-- Tous les d&eacute;p&ocirc;ts --</option>
        @foreach($depots as $depot)
        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByFacture" placeholder="Rechercher par N° facture">
    </div>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-ventes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="factureFormatter" data-align="center" data-width="60px">Facture </th>
            <th data-field="date_ventes">Date </th>
            <th data-formatter="typeFactureFormatter">Facture </th>
            <th data-field="client.full_name_client">Client </th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="caisse.libelle_caisse">Caisse </th>
            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant TTC</th>
            <th data-field="acompte_facture" data-formatter="montantFormatter">Acompte</th>
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
                        <i class="fa fa-credit-card fa-2x"></i>
                        Gestion des ventes
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idVenteModifier" name="idVente" ng-hide="true" ng-model="vente.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="vente.date_ventes" id="date_vente" name="date_vente" placeholder="Ex: 01-01-1994" value="<?= date('d-m-Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_id" id="depot_id" ng-model="vente.depot_id" ng-init="vente.depot_id=''" class="form-control select2" required>
                                        <option value="" ng-show="false">-- Sectionner le D&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Caisse *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-hdd-o"></i>
                                    </div>
                                    <select name="caisse_id" id="caisse_id" class="form-control" required>
                                        <option value="">-- Sectionner la caisse --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                    <select name="client_id" id="client_id" ng-model="vente.client_id" class="form-control select2" required>
                                        <option value="">-- Selectionner le client --</option>
                                        @foreach($clients as $client)
                                        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-btn">
                                        <button title="Ajouter s'il n'y a pas dans la liste" type="button" class="btn btn-success btn-flat addClient"><i class="fa fa-plus"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5 class="text-bold text-red">
                                <label>
                                    <input type="checkbox" id="proformat" name="proformat" ng-model="vente.proformat" ng-checked="vente.proformat">&nbsp; Cochez cette case si c'est une facture proforma
                                </label>
                            </h5>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Montant plafond du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" id="plafond_client" placeholder="Montant plafond du client" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control" id="contact_client" placeholder="Contact client" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-bold text-green">
                                <label>
                                    Liste des articles
                                </label>
                            </h5>
                        </div>
                    </div>
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Code Barre</label>
                                    <input type="text" class="form-control" id="code_barre">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Article *</label>
                                    <select class="form-control" id="article">
                                        <option value="" ng-show="false">-- Selcetionner l'article --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Colis *</label>
                                    <select class="form-control" id="unite">
                                        <option value="" ng-show="false">-- Colis--</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix HT</label>
                                    <input type="text" class="form-control" id="prixHT" placeholder="Prix HT" readonly>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix TTC</label>
                                    <input type="text" class="form-control" id="prixTTC" placeholder="Prix TTC" readonly>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>En Stock</label>
                                    <input type="number" class="form-control" id="en_stock" placeholder="Qté / Btle en stock" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; vendre *</label>
                                    <input type="number" class="form-control" id="quantite" placeholder="Qté / Btle à vendre">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Montant TTC</label>
                                    <input type="text" class="form-control" id="montantTC" placeholder="Montant TTC" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-xs  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div><br/>
                        <table class="table table-info table-striped box box-success">
                            <thead>
                                <tr>
                                    <th>Cochez</th>
                                    <th>Article</th>
                                    <th>Colis</th>
                                    <th>Prix HT</th>
                                    <th>Prix TTC</th>
                                    <th>Quantit&eacute;</th>
                                    <th>Montant TTC</th>
                                </tr>
                            </thead>
                            <tbody class="articles-info">

                            </tbody>
                        </table>
                        <button type="button" class="delete-row">Supprimer ligne</button>
                    </div>
                    <div id="div_update">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="btnModalAjoutArticle" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus">Ajouter un article</i></button>
                                </div>
                            </div>
                        </div><br/>
                        <table id="tableArticle" class="table table-success table-striped box box-success"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.description_article">Article</th>
                                    <th data-field="unite.libelle_unite">Colis</th>
                                    <th data-formatter="montantHTFormatter">Prix HT</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix TTC</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTTCFormatter" data-align="center">Montant TTC </th>
                                    <th data-field="id" data-formatter="optionAArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                        <div class="row">
                            <div class="col-md-6"><br/><br/>
                                <ul class="nav nav-stacked" style="font-size: 15px;">
                                    <li><a class="text-bold" >Montant HT <span id="montantTHT_add" class="pull-right text-bold"></span></a></li>
                                    <li><a class="text-bold" >Montant TVA <span id="montantTTVA_add" class="pull-right text-bold"></span></a></li>
                                    <li><a class="text-bold" >Montant TTC<span id="montantTTTC_add" class="pull-right text-bold  text-red"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                     <br/>
                     <div class="row" id="row_regle">
                        <div class="col-md-6"><br/><br/>
                            <ul class="nav nav-stacked" style="font-size: 15px;">
                                <li><a class="text-bold">Montant HT <span class="pull-right text-bold montantHT"></span></a></li>
                                <li><a class="text-bold">Montant TVA <span class="pull-right text-bold montantTVA"></span></a></li>
                                <li><a class="text-bold">Montant TTC<span class="pull-right text-bold text-red montantTTC"></span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal liste règlements -->
<div class="modal fade bs-modal-liste-reglement" id="listeReglement" ng-controller="listeReglementCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Liste des r&egrave;glements de la facture N° <b>@{{vente.numero_facture}}</b> du client <b>@{{vente.client_id > 0 ? vente.client.full_name_client : ' Anonyme'}}</b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListeReglement" class="table table-success table-striped box box-success"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false">
                    <thead>
                        <tr>
                            <th data-field="date_reglements">Date  </th>
                            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de payement </th>
                            <th data-field="montant_reglement" data-formatter="montantFormatter">Montant</th>
                            <th data-formatter="imageFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal add article -->
<div class="modal fade bs-modal-add-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
        <form id="formAjoutArticle" ng-controller="formAjoutArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Ajout d'un article
                </div>
                @csrf
                <div class="modal-body ">
                   <input type="text" class="hidden" id="idArticleModifier"  ng-model="article.id"/>
                   <input type="text" class="hidden" id="vente"  name="vente_id"/>
                    <div class="row">
                        <div class="col-md-2">
                                <div class="form-group">
                                    <label>Code Barre</label>
                                    <input type="text" class="form-control" id="code_barre_add">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Article *</label>
                                    <select name="article_id" class="form-control" id="article_add" required>
                                        <option value="" ng-show="false">-- Selcetionner l'article --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Colis *</label>
                                    <select name="unite_id" class="form-control" id="unite_add" required>
                                        <option value="" ng-show="false">-- Colis--</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix HT</label>
                                    <input type="text" class="form-control" id="prixHT_add" placeholder="Prix HT" readonly>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix TTC</label>
                                    <input type="text" class="form-control" name="prix" ng-model="article.prix" id="prixTTC_add" placeholder="Prix TTC" readonly>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>En Stock</label>
                                    <input type="number" class="form-control" id="en_stock_add" placeholder="Qté / Btle en stock" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; vendre *</label>
                                    <input type="number" name="quantite" ng-model="article.quantite" class="form-control" id="quantite_add" placeholder="Qté / Btle à vendre">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Montant TTC</label>
                                    <input type="text" class="form-control" id="montantTC_add" placeholder="Montant TTC" readonly>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion article-->
<div class="modal fade bs-modal-supprimer-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimerArticle" ng-controller="formSupprimerArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idArticleSupprimer"  ng-model="article.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'article <br/><b>@{{article.article.description_article}}</b></div>
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

<!-- Modal suppresion-->
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
                    <input type="text" class="hidden" id="idVenteSupprimer"  ng-model="vente.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer cette vente<br/><b>@{{vente.numero_facture}}</b></div>
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

<!-- Modal ajout client -->
<div class="modal fade bs-modal-ajout-client" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <form id="formAjoutClient" action="#">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-shopping-cart fa-2x"></i>
                        Gestion des clients
                    </span>
                </div>
                <div class="modal-body ">
                    @csrf
                   <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom complet ou raison sociale du client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" name="full_name_client" placeholder="Nom & prénom(s) ou raison sociale du client" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>R&eacute;gime *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-circle-o"></i>
                                    </div>
                                    <select name="regime_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le r&eacute;gime --</option>
                                        @foreach($regimes as $regime)
                                        <option value="{{$regime->id}}"> {{$regime->libelle_regime}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" name="contact_client" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact du client" required>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" name="email_client" placeholder="Adresse mail du client">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>Pays du client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-flag"></i>
                                    </div>
                                    <select name="nation_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le pays --</option>
                                        @foreach($nations as $nation)
                                            @if ($nation->id == 51) <!-- Sélectionner Côte d'ivoire par défaut -->
                                                <option value="{{$nation->id}}" selected> {{$nation->libelle_nation}}</option>
                                            @else
                                                <option value="{{$nation->id}}"> {{$nation->libelle_nation}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse postale du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" class="form-control" name="boite_postale_client" placeholder="Adresse du boite postale de client">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Fax </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-fax"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" name="fax_client" data-format="dd-dd-dd-dd" pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Numéro fax du client">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse g&eacute;ographique du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);"  class="form-control" name="adresse_client" placeholder="Adresse géographique du client">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Compte contribuable </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-copy"></i>
                                    </div>
                                    <input type="text" class="form-control" name="compte_contribuable_client" placeholder="Numéro du compte contribuable">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Montant plafond du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" name="plafond_client" placeholder="Montant du plafond">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var ajout = false;
    var montantHT = 0;
    var montantTTC = 0;
    var ajoutArticle = false;
    var $table = jQuery("#table"), rows = [],$tableListeReglement = jQuery("#tableListeReglement"),$tableListeArticle = jQuery("#tableListeArticle"), $tableArticle = jQuery("#tableArticle"), rowsArticle = [];

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (vente) {
        $scope.vente = vente;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.vente = {};
        };
    });

    appSmarty.controller('formAjoutArticleCtrl', function ($scope) {
        $scope.populateArticleForm = function (article) {
        $scope.article = article;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.article = {};
        };
    });

    appSmarty.controller('formSupprimerArticleCtrl', function ($scope) {
        $scope.populateSupArticleForm = function (article) {
        $scope.article = article;
        };
        $scope.initForm = function () {
        $scope.article = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (vente) {
        $scope.vente = vente;
        };
        $scope.initForm = function () {
        $scope.vente = {};
        };
    });

    appSmarty.controller('formReglementClientAnonymeCtrl', function ($scope) {
        $scope.populateClientAnonymeForm = function (vente) {
            $scope.vente = vente;
        };
        $scope.initForm = function () {
            $scope.vente = {};
        };
    });

    appSmarty.controller('listeArticleCtrl', function ($scope) {
        $scope.populateListeArticleForm = function (vente) {
            $scope.vente = vente;
        };
    });
    appSmarty.controller('listeReglementCtrl', function ($scope) {
        $scope.populateListeReglementForm = function (vente) {
            $scope.vente = vente;
        };
    });

    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $("#depot_id, #client_id, #article, #article_add").select2({width: '100%'});

        $(".addClient").on("click", function () {
            document.forms["formAjoutClient"].reset();
            $(".bs-modal-ajout-client").modal("show");
        });
        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
            $("#montantTHT_add").html($.number(data.montantTHT_add));
            $("#montantTTVA_add").html($.number(data.montantTTTC_add-data.montantTHT_add));
            $("#montantTTTC_add").html($.number(data.montantTTTC_add));
        });
        $("#div_enregistrement").show();
        $("#div_update").hide();

       $("#searchByClient").change(function (e) {
            var client = $("#searchByClient").val();
            if(client == "tous"){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-ventes'])}}"});
            }
            else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-client/' + client});
            }
        });
       $("#searchByDepot").change(function (e) {
            var depot = $("#searchByDepot").val();
            if(depot == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-ventes'])}}"});
            }
            else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-depot/' + depot});
            }
        });
       $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-ventes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-date/' + date});
            }
        });
       $("#searchByFacture").keyup(function (e) {
            var numero_facture = $("#searchByFacture").val();
            if(numero_facture == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-ventes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-numero-facture/' + numero_facture});
            }
        });
       $('#searchByDate,#date_reglement, #date_vente').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $('#searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr'
        });

        $("#row_regle").hide();

        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var vente = $("#idVenteModifier").val();
            var depot = $("#depot_id").val();
            document.forms["formAjoutArticle"].reset();
            $("#vente").val(vente);
            $.getJSON("../parametre/liste-caisses-by-depot/" + depot, function (reponse) {
                $('#caisse_id').html("<option>-- Selectionner la caisse --</option>");
                $.each(reponse.rows, function (index, caisse) {
                    $('#caisse_id').append('<option value=' + caisse.id + '>' + caisse.libelle_caisse+ '</option>')
                });
                $("#caisse_id").val(vente.caisse_id);
            });
            $.getJSON("../boutique/liste-article-by-unite-in-depot/" + depot, function (reponse) {
                $('#article_add').html("<option>-- Selectionner l'article --</option>");
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, article) {
                        $('#article_add').append('<option value=' + article.id_article + '>' + article.description_article + '</option>')
                    });
                }else{
                    alert('Aucun article disponible dans ce dépôt !')
                }
            })
            $(".bs-modal-add-article").modal("show");
        });
        $("#client_id").change(function (e) {
            var client_id = $("#client_id").val();
            $.getJSON("../parametre/find-client-by-id/" + client_id, function (reponse) {
                $('#contact_client').val("");
                $.each(reponse.rows, function (index, client) {
                    $('#contact_client').val(client.contact_client)
                    $('#plafond_client').val(client.plafond_client)
                });
           });
        });
        $("#btnModalAjout").on("click", function () {
            $("#row_regle").hide();
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $("#depot_id").select2("val","");
             $("#depot_id").prop('disabled',false);
             $('#caisse_id').html("<option>-- Selectionner la caisse --</option>");
        });

        $("#depot_id").change(function (e) {
            $("#prixHT").val("");
            $("#prixTTC").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montantTC").val("");
            $("#code_barre").val("");
            $(".articles-info").find('input[name="record"]').each(function () {
                $(this).parents("tr").remove();
            });
            var depot_id = $("#depot_id").val();
            $.getJSON("../parametre/liste-caisses-by-depot/" + depot_id, function (reponse) {
                    $('#caisse_id').html("<option>-- Selectionner la caisse --</option>");
                 if(reponse.total>0){
                    $.each(reponse.rows, function (index, caisse) {
                        $('#caisse_id').append('<option value=' + caisse.id + '>' + caisse.libelle_caisse+ '</option>')
                    });
                }else{
                    alert('Ce dépôt ne contient aucune caisse !')
                }
           });
           $.getJSON("../boutique/liste-article-by-unite-in-depot/" + depot_id, function (reponse) {
                $('#article').html("<option>-- Selectionner l'article --</option>");
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, article) {
                        $('#article').append('<option data-libellearticle= "' + article.description_article + '" value=' + article.id_article + '>' + article.description_article + '</option>')
                    });
                }else{
                    alert('Aucun article disponible dans ce dépôt !')
                }
            })
        });

        $('#code_barre').keyup(function(e){
             if (e.which == '10' || e.which == '13') {
            $("#prixHT").val("");
            $("#prixTTC").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montantTC").val("");
            var code_barre = $('#code_barre').val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function (reponse) {
                $('#article').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, retour) {
                    $('#article').append('<option data-libellearticle= "' + retour.article.description_article + '" selected value=' + retour.article.id + '>' + retour.article.description_article + '</option>')
                    $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + retour.article.id , function (reponse) {
                        $('#unite').html("<option>-- Colis --</option>");
                        $.each(reponse.rows, function (index, colis) {
                            $('#unite').append('<option data-libelleunite= "' + colis.unite.libelle_unite + '" value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                        });
                    })
                });
            })
              e.preventDefault();
                e.stopPropagation();
            }
        });
        $('#code_barre_add').keyup(function(){
            $("#prixHT_add").val("");
            $("#prixTTC_add").val("");
            $("#en_stock_add").val("");
            $("#quantite_add").val("");
            $("#montantTC_add").val("");
            var code_barre = $('#code_barre_add').val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function (reponse) {
                $('#article_add').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, retour) {
                    $('#article_add').append('<option selected value=' + retour.article.id + '>' + retour.article.description_article + '</option>')
                    $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + retour.article.id , function (reponse) {
                        $('#unite_add').html("<option>-- Colis --</option>");
                        $.each(reponse.rows, function (index, colis) {
                            $('#unite_add').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                        });
                    })
                });
            })
        });
        $('#article').change(function(){
            $("#prixHT").val("");
            $("#prixTTC").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montantTC").val("");
            $("#code_barre").val("");
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();
             $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + article_id , function (reponse) {
                $('#unite').html("<option>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) {
                    $('#unite').append('<option data-libelleunite= "' + colis.unite.libelle_unite + '" value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
            })
        });
        $('#article_add').change(function(){
            $("#prixHT_add").val("");
            $("#prixTTC_add").val("");
            $("#en_stock_add").val("");
            $("#quantite_add").val("");
            $("#montantTC_add").val("");
            $("#code_barre_add").val("");
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_id").val();
             $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre_add").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + article_id , function (reponse) {
                $('#unite_add').html("<option>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) {
                    $('#unite_add').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
            })
        });

        $('#unite').change(function(){
            $("#quantite").val("");
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();
            var unite_id = $("#unite").val();
            $.getJSON("../boutique/find-article-in-depot-by-unite/" + article_id + "/" + depot_id + "/" +  unite_id, function (reponse) {
                $.each(reponse.rows, function (index, article) {
                    $("#en_stock").val(article.quantite_disponible);
                    $("#prixTTC").val(article.prix_vente);
                    //Calcul du prix HT
                    var tva = 0;
                   if(article.article.param_tva_id!=null){
                       $.getJSON("../parametre/find-param-tva/" + article.article.param_tva_id, function (reponse) {
                            $.each(reponse.rows, function (index, tvas_infos) {
                                tva = tvas_infos.montant_tva;
                                var prix_ht_article = (article.prix_vente/(tva + 1));
                                var prix = Math.round(prix_ht_article);
                                $("#prixHT").val(prix);
                            });
                        })
                   }else{
                       $("#prixHT").val(article.prix_vente);
                   }

                });
            })
        });

        $('#unite_add').change(function(){
            $("#quantite_add").val("");
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_id").val();
            var unite_id = $("#unite_add").val();
            const type_ventes = document.querySelectorAll('input[name="gros_demi_gros"]');
            $.getJSON("../boutique/find-article-in-depot-by-unite/" + article_id + "/" + depot_id + "/" +  unite_id, function (reponse) {
                $.each(reponse.rows, function (index, article) {
                    $("#en_stock_add").val(article.quantite_disponible);
                    $("#prixTTC_add").val(article.prix_vente);
                    //Calcul du prix HT
                    var tva = 0;
                   if(article.article.param_tva_id!=null){
                       $.getJSON("../parametre/find-param-tva/" + article.article.param_tva_id, function (reponse) {
                            $.each(reponse.rows, function (index, tvas_infos) {
                                tva = tvas_infos.montant_tva;
                                var prix_ht_article = (article.prix_vente/(tva + 1));
                                var prix = Math.round(prix_ht_article);
                                $("#prixHT_add").val(prix);
                            });
                        })
                   }else{
                       $("#prixHT_add").val(article.prix_vente);
                   }

                });
            })
        });

        $("#quantite").change(function (e) {
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
          $("#montantTC").val(quantite*prix);
        });
        $("#quantite").keyup(function (e) {
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
          $("#montantTC").val(quantite*prix);
        });
        $("#quantite_add").change(function (e) {
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          $("#montantTC_add").val(quantite*prix);
        });
        $("#quantite_add").keyup(function (e) {
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          $("#montantTC_add").val(quantite*prix);
        });

        $(".add-row").click(function (e) {
            if($("#article").val() != '' && $("#quantite").val() != '' && $("#unite").val() != '') {
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var libelle_unite = $("#unite").children(":selected").data("libelleunite");
                var article = $("#article").val();
                var unite = $("#unite").val();
                var quantite = $("#quantite").val();
                var stock = $("#en_stock").val();
                var prixTTC = $("#prixTTC").val();
                var prixHT = $("#prixHT").val();
                var montantTC = $("#montantTC").val();
                montantHT = montantHT + (quantite*prixHT);
                montantTTC = montantTTC + (quantite*prixTTC);
                var depot_id = $("#depot_id").val();

                if(parseInt(quantite) > parseInt(stock)){
                    alert("La quantité saisie ne doit pas être supérieure à la quantité disponible en stock!");
                    $("#quantite").val("");
                }else{
                    var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='hidden' name='articles[]' value='" + article + "'>" + libelle_article + "</td><td><input type='hidden' name='unites[]' value='" + unite + "'>" + libelle_unite + "</td><td><input type='hidden'>" + prixHT + "</td><td><input type='hidden' name='prix[]' value='" + prixTTC + "'>" + prixTTC + "</td><td><input type='hidden' name='quantites[]' value='" + quantite + "'>" + quantite + "</td><td><input type='hidden' >" + quantite*prixTTC + "</td></tr>";
                    $(".articles-info").append(markup);
                    $("#unite").val("");
                    $("#quantite").val("");
                    $("#en_stock").val("");
                    $("#prixTTC").val("");
                    $("#prixHT").val("");
                    $("#montantTC").val("");
                    $("#code_barre").val("");
                    $("#row_regle").show();
                    $(".montantHT").html("<b>"+montantHT+"</b>");
                    $(".montantTVA").html("<b>" + (parseInt(montantTTC) - parseInt(montantHT)) + "</b>");
                    $(".montantTTC").html("<b>" + parseInt(montantTTC) +"</b>");

                    $.getJSON("../boutique/liste-article-by-unite-in-depot/" + depot_id, function (reponse) {
                        $('#article').html("<option>-- Selectionner l'article --</option>");
                            $.each(reponse.rows, function (index, article) {
                                $('#article').append('<option data-libellearticle= "' + article.description_article + '" value=' + article.id_article + '>' + article.description_article + '</option>')
                        });
                    })
                }
            }else{
                alert("Les champs article, colis et quantité ne doivent pas être vides!");
            }
        });
        // Find and remove selected table rows
        $(".delete-row").click(function () {
            $(".articles-info").find('input[name="record"]').each(function () {
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
                var url = "{{route('boutique.ventes.store')}}";
             }else{
                var id = $("#idVenteModifier").val();
                var methode = 'PUT';
                var url = 'ventes/' + id;
             }
            editerVenteAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });
        $("#formAjoutArticle").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutArticle .loader-overlay");

            if (ajoutArticle==true) {
                var methode = 'POST';
                var url = "{{route('boutique.articles-vente.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'articles-vente/' + id;
             }
            editerVentesArticlesAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle,$table, ajoutArticle);
        });
        $("#formAjoutClient").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutClient .loader-overlay");
            var methode = 'POST';
            var url = "{{route('parametre.clients.store')}}";
            editerClient(methode, url, $(this), $(this).serialize(), $ajaxLoader);
        });

        $("#formReglementClientAnonyme").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formReglementClientAnonyme .loader-overlay");
            var methode = 'POST';
            var url = "{{route('boutique.reglements.store')}}";
            var formData = new FormData($(this)[0]);
            editerReglementAction(methode, url, $(this), formData, $ajaxLoader, $table);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idVenteSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('ventes/' + id, $(this).serialize(), $question, $ajaxLoader,$table);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('articles-vente/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle, $table);
        });
    });
    function articleRow(idVente) {
        var $scope = angular.element($("#listeArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
        $scope.$apply(function () {
            $scope.populateListeArticleForm(vente);
        });
        $tableListeArticle.bootstrapTable('refreshOptions', {url: "../boutique/liste-articles-vente/" + idVente});
       $(".bs-modal-liste-article").modal("show");
    }
    function reglementRow(idVente) {
        var $scope = angular.element($("#listeReglement")).scope();
        var vente =_.findWhere(rows, {id: idVente});
        $scope.$apply(function () {
            $scope.populateListeReglementForm(vente);
        });
        $tableListeReglement.bootstrapTable('refreshOptions', {url: "../boutique/liste-reglements-by-vente/" + idVente});
       $(".bs-modal-liste-reglement").modal("show");
    }
    function updateRow(idVente) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $("#depot_id").select2("val", vente.depot_id);
        $("#client_id").select2("val", vente.client_id);

        $.getJSON("../parametre/liste-caisses-by-depot/" + vente.depot_id, function (reponse) {
            $('#caisse_id').html("<option>-- Selectionner la caisse --</option>");
            $.each(reponse.rows, function (index, caisse) {
                $('#caisse_id').append('<option value=' + caisse.id + '>' + caisse.libelle_caisse+ '</option>')
            });
            $("#caisse_id").val(vente.caisse_id);
        });
        $.getJSON("../boutique/liste-article-by-unite-in-depot/" + vente.depot_id, function (reponse) {
                $('#article_add').html("<option>-- Selectionner l'article --</option>");
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, article) {
                        $('#article_add').append('<option value=' + article.id_article + '>' + article.description_article + '</option>')
                    });
                }else{
                    alert('Aucun article disponible dans ce dépôt !')
                }
            })

        $tableArticle.bootstrapTable('refreshOptions', {url: "../boutique/liste-articles-vente/" + idVente});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $("#depot_id").prop('disabled',true);
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idVente){
        var $scope = angular.element($("#formSupprimer")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $(".bs-modal-suppression").modal("show");
    }

    function updateArticleRow(idArticle){
        ajoutArticle = false;
        var $scope = angular.element($("#formAjoutArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateArticleForm(article);
        });
        var vente = $("#idVenteModifier").val();
        $("#vente").val(vente);
        var depot = $("#depot_id").val();
        $.getJSON("../boutique/liste-article-by-unite-in-depot/" + depot, function (reponse) {
                $('#article_add').html("<option>-- Selectionner l'article --</option>");
                    $.each(reponse.rows, function (index, article) {
                        $('#article_add').append('<option value=' + article.id_article + '>' + article.description_article + '</option>')
                    });
                    $('#article_add').val(article.article_id)
                    $("#quantite_add").val(article.quantite);
        })
        $.getJSON("../boutique/liste-unites-by-depot-article/" + depot + "/" + article.article_id , function (reponse) {
                $('#unite_add').html("<option>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) {
                    $('#unite_add').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
                $("#unite_add").val(article.unite_id);
        })
        const type_vente = article.gros_demi_gros;
            $.getJSON("../boutique/find-article-in-depot-by-unite/" + article.article_id + "/" + depot + "/" +  article.unite_id, function (reponse) {
                $.each(reponse.rows, function (index, articles) {
                    $("#en_stock_add").val(articles.quantite_disponible);

                    //Calcul du prix HT
                    var tva = 0;
                   if(articles.article.param_tva_id!=null){
                       $.getJSON("../parametre/find-param-tva/" + articles.article.param_tva_id, function (reponse) {
                            $.each(reponse.rows, function (index, tvas_infos) {
                                tva = tvas_infos.montant_tva;
                                var prix_ht_article = (article.prix/(tva + 1));
                                var prix = Math.round(prix_ht_article);
                                $("#prixHT_add").val(prix);
                            });
                        })
                   }else{
                       $("#prixHT_add").val(article.prix);
                   }

                });
            })
        $("#prixTTC_add").val(article.prix);
        $("#montantTC_add").val(article.prix*article.quantite);
        $(".bs-modal-add-article").modal("show");
    }

    function deleteArticleRow(idArticle){
        var $scope = angular.element($("#formSupprimerArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateSupArticleForm(article);
        });
        $(".bs-modal-supprimer-article").modal("show");
    }

    function reglementClientAnonymeRow(idVente){
        var $scope = angular.element($("#formReglementClientAnonyme")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateClientAnonymeForm(vente);
        });
        $(".bs-modal-reglement-client-anonyme").modal("show");

    }

    function facturePrintRow(idVente){
        window.open("facture-vente-pdf/" + idVente ,'_blank')
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function optionFormatter(id, row) {
        if(row.acompte_facture>0){
            return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Liste des règlements" onClick="javascript:reglementRow(' + row.id + ');"><i class="fa fa-money"></i></button>';
        }else{
            return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
        }
    }

    function montantTTCFormatter(id, row){
        var montant = row.quantite*row.prix
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function montantHTFormatter(id, row){
        var prix_ttc = row.prix;
        if(row.article.param_tva_id!=null){
            var tva = row.montant_tva;
            var prix_ht_article = (prix_ttc/(tva + 1));
            var prix = Math.round(prix_ht_article);
            return '<span class="text-bold">' + $.number(prix)+ '</span>';
        }else{
           return '<span class="text-bold">' + $.number(prix_ttc)+ '</span>';
        }
    }


    function optionAArticleFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function imageFormatter(id, row) {
          return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir la facture</a>" : "";
    }

   function typeFactureFormatter(id, row){
       return row.proformat==0 ? row.numero_facture : "Proforma";
   }
   function factureFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Facture" onClick="javascript:facturePrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }
   function editerVenteAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#depot_id").select2("val", "");
                    $("#client_id").select2("val", "");
                    $("#caisse_id").val("");
                    $("#contact_client").val("");
                    $("#stock").val("");
                    $(".articles-info").find('input[name="record"]').each(function () {
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
                $("#row_regle").hide();
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
   function editerVentesArticlesAction(methode, url, $formObject, formData, $ajoutLoader, $table,$tableVente, ajout = true) {
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
                    $tableVente.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $tableVente.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
                ajout = false;
            }
            $.gritter.add({
                // heading of the notification
                title: "GESCTION STOCK",
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
                title: "GESCTION STOCK",
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
   function editerClient(methode, url, $formObject, formData, $ajoutLoader) {
        jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                $.getJSON("../parametre/last-client/", function (reponse) {
                    $('#client_id').html("<option value=''>-- Selectionner client --</option>");
                   $.each(reponse.rows, function (index, client) {
                       $('#client_id').append("<option value=" + client.id + ">" + client.full_name_client + "</option>")
                        $("#client_id").select2("val",client.id)
                        $('#plafond_client').val(client.plafond_client)
                    });
              });
            $(".bs-modal-ajout-client").modal("hide");
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
            if(res.message == "The given data was invalid."){
               // messageErreur = "Cet enregistrement existe dèjà";
                messageErreur = "Erreur survenue lors de l'enregistrement.";
            }
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
    }
   function editerReglementAction(methode, url, $formObject, formData, $ajoutLoader, $table) {
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
                $table.bootstrapTable('refresh');
                $(".bs-modal-reglement-client-anonyme").modal("hide");
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

    //Supprimer un article
    function supprimerArticleAction(url, formData, $question, $ajaxLoader, $table, $tableVente) {
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
                $tableVente.bootstrapTable('refresh');
                $(".bs-modal-supprimer-article").modal("hide");
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


