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
    <div class="form-group">
       <input type="text" class="form-control" id="searchByFacture" placeholder="Rechercher par N° facture">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-4">
    <select class="form-control" id="searchByClient">
        <option value="0">-- Tous les clients --</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
        @endforeach
    </select>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('vente',['action'=>'liste-ventes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="factureFormatter" data-align="center" data-width="60px"><i class="fa fa-print"></i></th>
            <th data-field="date_ventes">Date </th>
            <th data-field="numero_facture">Facture </th>
            <th data-field="client.full_name_client">Client </th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="montantTTC" data-formatter="montantFormatter">Montant TTC</th>
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
                        Gestion des factures
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idVente" name="idVente" ng-hide="true" ng-model="vente.id"/>
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
                                    <select name="depot_id" id="depot_id" ng-model="vente.depot_id" ng-init="vente.depot_id=''" class="form-control" required>
                                        <option value="">-- Sectionner le D&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value="">-- Selectionner le client --</option>
                                        @foreach($clients as $client)
                                        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                    <div class="row">
                        <div class="col-md-6"><br/>
                            <span id="plafond_client_aff" class="text-bold text-red h3"></span>
                            <input type="text" class="hidden" id="plafond_client">
                        </div>
                    </div>
                  <hr/>
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Code Barre</label>
                                    <input type="text" class="form-control" id="code_barre" autofocus>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Article *</label>
                                    <select class="form-control" id="article">
                                        <option value="">-- Selcetionner l'article --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type de prix </label>
                                    <select class="form-control" id="choix_prix">
                                        <option value=""> Choix du prix </option>
                                        <option data-libellechoixprix="Prix détail" value="prix_detail"> Prix d&eacute;tail </option>
                                        <option data-libellechoixprix="Prix démi gros" value="prix_demi_gros"> Prix d&eacute;mi gros </option>
                                        <option data-libellechoixprix="Prix en gros" value="prix_gros"> Prix en gros </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix</label>
                                    <input type="number" class="form-control" id="prix" placeholder="Prix" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>En Stock</label>
                                    <input type="number" class="form-control" id="en_stock" placeholder="Qté / Btle en stock" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; vendre *</label>
                                    <input type="number" min="0" class="form-control" id="quantite" placeholder="Qté / Btle à vendre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Montant</label>
                                    <input type="text" class="form-control" id="montant" placeholder="Montant" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-sm  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-xs delete-row">Supprimer ligne</button><br/><br/>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tableAddRowArticle" class="table table-success table-striped box box-success"
                                       data-toggle="table"
                                       data-id-field="id"
                                       data-unique-id="id"
                                       data-click-to-select="true"
                                       data-show-footer="false">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th data-field="id">ID</th>
                                            <th data-field="code_barre">Code barre</th>
                                            <th data-field="libelle_article">Article</th>
                                            <th data-field="choix_prix">Choix Prix </th>
                                            <th data-field="prix">Prix </th>
                                            <th data-field="quantite">Qt&eacute; / Btle</th>
                                            <th data-field="montant">Montant </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
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
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-field="choix_prix" data-formatter="choixPrixFormatter">Choix Prix</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant</th>
                                    <th data-field="id" data-formatter="optionArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6"><br/>
                                <ul class="nav nav-stacked" style="font-size: 20px;">
                                    <li><a class="text-bold">Montant TTC<span class="pull-right text-bold text-red montantTTC_add"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row" id="row_regle">
                        <div class="col-md-6"></div>
                        <div class="col-md-6"><br/>
                            <ul class="nav nav-stacked" style="font-size: 20px;">
                                <li><a class="text-bold">Montant TTC<span class="pull-right text-bold text-red montantTTC"></span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="sendButton" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal add article -->
<div class="modal fade bs-modal-add-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:75%">
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
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Code Barre</label>
                               <input type="text" class="form-control" id="code_barre_add" autofocus>
                           </div>
                       </div>
                       <div class="col-md-5">
                           <div class="form-group">
                               <label>Article *</label>
                               <select name="article_id" class="form-control" id="article_add" required>
                                   <option value="">-- Selcetionner l'article --</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-2">
                            <div class="form-group">
                                <label>Type de prix </label>
                                <select class="form-control" id="choix_prix_add" name="choix_prix" ng-model="article.choix_prix">
                                    <option value=""> Choix du prix </option>
                                    <option data-libellechoixprix="Prix détail" value="prix_detail"> Prix d&eacute;tail </option>
                                    <option data-libellechoixprix="Prix démi gros" value="prix_demi_gros"> Prix d&eacute;mi gros </option>
                                    <option data-libellechoixprix="Prix en gros" value="prix_gros"> Prix en gros </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix</label>
                                <input type="text" class="form-control" name="prix" ng-model="article.prix" id="prix_add" placeholder="Prix" readonly>
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
                               <input type="number" min="1" name="quantite" ng-model="article.quantite" class="form-control" id="quantite_add" placeholder="Qté / Btle à vendre" required>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Montant </label>
                               <input type="text" class="form-control" id="montant_add" placeholder="Montant" readonly>
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
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'article <br/><b>@{{article.article.libelle_article}}</b></div>
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
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer la facture <br/><b>@{{vente.numero_facture}}</b></div>
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

<!-- Modal panier -->
<div class="modal fade bs-modal-panier" id="panierArticle" ng-controller="panierArticleCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Panier du ticket <b>@{{vente.numero_ticket}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="tablePanierArticle" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-field="choix_prix" data-formatter="choixPrixFormatter">Choix Prix</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant</th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<!-- Modal liste règlements -->
<div class="modal fade bs-modal-liste-reglement" id="listeReglement" ng-controller="listeReglementCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Liste des r&egrave;glements de la facture N° <b>@{{vente.numero_facture}}</b> du client <b>@{{vente.client.full_name_client}}</b>
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
                            <th data-field="date_reglements">Date</th>
                            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de paiement </th>
                            <th data-field="montant" data-formatter="montantFormatter">Montant</th>
                            <th data-field="numero_cheque">N° virement ou ch&egrave;que</th>
                            <th data-formatter="imageFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ajout = false;
    var ajoutArticle = false;
    var montantTTC = 0;
    var $table = jQuery("#table"), rows = [], $tableListeReglement = jQuery("#tableListeReglement"), $tablePanierArticle = jQuery("#tablePanierArticle"),$tableListeArticle = jQuery("#tableListeArticle"), $tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle");
    var monPanier = [];
    var idTablle =  0;

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

    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
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

        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
            $(".montantTTC_add").html($.number(data.montantTTC) + 'F CFA');
            $("#plafond_client_aff").html('');
        });

        $('#searchByDate, #date_vente').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });

        $("#searchByClient,#article_add, #article, #client_id").select2({width: '100%'});


        $("#div_enregistrement").show();
        $("#div_update").hide();
        $("#row_regle").hide();
        $(".delete-row").hide();

        $("#searchByClient").change(function (e) {
            $("#searchByDate").val("");
            $("#searchByFacture").val("");
            var client = $("#searchByClient").val();

            if(client == 0){
                 $table.bootstrapTable('refreshOptions', {url: "{{url('vente', ['action' => 'liste-ventes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../vente/liste-ventes-by-client/' + client});
            }
        });

        $("#searchByDate").change(function (e) {
            $("#searchByFacture").val("");
            var date = $("#searchByDate").val();

            if(date == ""){
                 $table.bootstrapTable('refreshOptions', {url: "{{url('vente', ['action' => 'liste-ventes'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-ventes-by-date/' + date});
            }
        });

        $("#searchByFacture").keyup(function (e) {
            $("#searchByDate").val("");
            var numero_facture = $("#searchByFacture").val();

            if(numero_facture == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('vente', ['action' => 'liste-ventes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../vente/liste-ventes/' + numero_facture});
            }
        });

        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var vente = $("#idVente").val();
            var depot = $("#depot_id").val();
            $("#article").val('').trigger('change');
            document.forms["formAjoutArticle"].reset();
            $("#vente").val(vente);
            $.getJSON("../stock/liste-article-by-depot/" + depot, function (reponse) {
                $('#article_add').html("<option value=''>-- Selectionner l'article --</option>");
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, retour) {
                        $('#article_add').append('<option value=' + retour.article.id + '>' + retour.article.libelle_article + '</option>')
                    });
                }else{
                    $('#article_add').html("<option value=''>-- Aucun article trouvé --</option>");
                }
            })
            $(".bs-modal-add-article").modal("show");
        });

        $("#btnModalAjout").on("click", function () {
            ajout = true;
            document.getElementById("code_barre").focus();
            $("#row_regle").hide();
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $(".delete-row").hide();
            $("#client_id").select2("val","");
            $("#article").select2("val","");
            $('#plafond_client').val("");
            $("#plafond_client_aff").html('');
            $tableAddRowArticle.bootstrapTable('removeAll');
            monPanier = [];
            idTablle =  0;
            montantTTC = 0;
            $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
            $("#montant_a_payer").val(montantTTC);
        });

        $("#client_id").change(function (e) {
            var client_id = $("#client_id").val();
            $('#montant_achat').val(0);
            if(client_id!=""){
                $.getJSON("../crm/get-clients-by-id/" + client_id, function (reponse) {
                    $('#contact_client').val("");
                    if(reponse.total>0 && client_id!=""){
                        $.each(reponse.rows, function (index, client) {
                            $('#contact_client').val(client.contact_client)
                            $('#plafond_client').val(client.plafond_client)
                            $("#plafond_client_aff").html('Montant plafond ' + $.number(client.plafond_client)+' F CFA');
                        });
                    }else{
                        $('#plafond_client').val(0)
                        $("#plafond_client_aff").html('Doit ' + $.number(0)+' F CFA');
                    }
                });
            }else{
                $('#contact_client').val("");
                $('#plafond_client').val("");
                $("#plafond_client_aff").html('');
                $('#doit_client').val("")
                $("#client_doit_aff").html('');
            }
        });

        $("#depot_id").change(function (e) {
            var depot_id = $("#depot_id").val();

            $("#prix").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montant").val("");
            $("#code_barre").val("");

            $tableAddRowArticle.bootstrapTable('removeAll');
            monPanier = [];
            idTablle =  0;
            montantTTC = 0;

            $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
            $("#montant_a_payer").val(montantTTC);
            $("#row_regle").hide();
            $(".delete-row").hide();

            if(depot_id!=""){
                $.getJSON("../stock/liste-article-by-depot/" + depot_id, function (reponse) {
                    $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                    if(reponse.total>0){
                        $.each(reponse.rows, function (index, retour) {
                            $('#article').append('<option data-libellearticle= "' + retour.article.libelle_article + '" value=' + retour.article.id + '>' + retour.article.libelle_article + '</option>')
                        });
                    }else{
                       $('#article').html("<option value=''>-- Aucun article trouvé --</option>");
                    }
                })
            }
        });

        $('#code_barre').keyup(function(e){
            if($("#depot_id").val()==""){
                $('#code_barre').val("");
                alert('Veillez selectionenr un dépôt svp!');
                return;
            }
            var code_barre = $('#code_barre').val();
            var depot_id = $("#depot_id").val();

            if(e.which == '10' || e.which == '13') {
                $("#prix").val("");
                $("#en_stock").val("");
                $("#quantite").val("");
                $("#montant").val("");
                $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot_id + "/" + code_barre, function (reponse) {
                    if(reponse.total>0){
                        $.each(reponse.rows, function (index, retour) {
                            $("#article").select2("val",retour.article.id);
                            $("#en_stock").val(retour.quantite_disponible)
                        });
                    }
                })
                e.preventDefault();
                e.stopPropagation();
            }
        });

        $('#choix_prix').change(function(e){
            var choix_prix = $("#choix_prix").val();
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();

            if(choix_prix != "" && article_id!="" && depot_id!=""){
                $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id, function (reponse) {
                    $.each(reponse.rows, function (index, retour) {
                        if(choix_prix == "prix_detail"){
                            $("#prix").val(retour.prix_vente_detail)
                        }
                        if(choix_prix == "prix_demi_gros"){
                            $("#prix").val(retour.prix_vente_demi_gros)
                        }
                        if(choix_prix == "prix_gros"){
                            $("#prix").val(retour.prix_vente_gros)
                        }
                    });
                })
            }else{
                alert("Selectionner un article SVP !");
            }
        });

        $('#choix_prix_add').change(function(e){
            var choix_prix = $("#choix_prix_add").val();
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_id").val();

            if(choix_prix != "" && article_id!="" && depot_id!=""){
                $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id, function (reponse) {
                    $.each(reponse.rows, function (index, retour) {
                        if(choix_prix == "prix_detail"){
                            $("#prix_add").val(retour.prix_vente_detail)
                        }
                        if(choix_prix == "prix_demi_gros"){
                            $("#prix_add").val(retour.prix_vente_demi_gros)
                        }
                        if(choix_prix == "prix_gros"){
                            $("#prix_add").val(retour.prix_vente_gros)
                        }
                    });
                })
            }else{
                alert("Selectionner un article SVP !");
            }
        });

        $('#code_barre_add').keyup(function(){
            var code_barre = $('#code_barre_add').val();
            var depot_id = $("#depot_id").val();

            $("#en_stock_add").val("");
            $("#quantite_add").val("");
            $("#montant_add").val("");

            $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot_id + "/" + code_barre, function (reponse) {
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, retour) {
                        $("#article_add").select2("val",retour.article.id);
                        $("#en_stock_add").val(retour.quantite_disponible)
                    });
                }else{
                    $("#article_add").select2("val","");
                }
            })
        });

        $('#article').change(function(){
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();

            $("#prix").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montant").val("");

            $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id, function (reponse) {
                $.each(reponse.rows, function (index, retour) {
                    var id = retour.article.id;
                    $("#en_stock").val(retour.quantite_disponible)
                });
                $("#article").select2("val",id);
                return;
            })
        });

        $('#article_add').change(function(){
            $("#prix_add").val("");
            $("#en_stock_add").val("");
            $("#quantite_add").val("");
            $("#montant_add").val("");
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_id").val();

            $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id, function (reponse) {
                $.each(reponse.rows, function (index, retour) {
                    var id = retour.article.id;
                    $("#en_stock_add").val(retour.quantite_disponible)
                });
                $("#article_add").select2("val",id);
                return;
            })
        });

        $("#quantite").change(function (e) {
            var quantite = $("#quantite").val();
            var prix = $("#prix").val();
            $("#montant").val(quantite*prix);
        });
        $("#quantite").keyup(function (e) {
          var quantite = $("#quantite").val();
          var prix = $("#prix").val();
          $("#montant").val(quantite*prix);
        });
        $("#quantite_add").change(function (e) {
            var quantite = $("#quantite_add").val();
            var prix = $("#prix_add").val();
            $("#montant_add").val(quantite*prix);
        });
        $("#quantite_add").keyup(function (e) {
            var quantite = $("#quantite_add").val();
            var prix = $("#prix_add").val();
            $("#montant_add").val(quantite*prix);
        });


        //Add row on table
        $(".add-row").click(function () {
            if($("#article").val() != '' && $("#quantite").val() != '' && $("#choix_prix").val() != '' && $("#quantite").val()!=0) {
                if($("#client_id").val()==""){
                    alert("Choisissez un client svp!");
                    return;
                }choix_prix
                var code_barre = $("#code_barre").val();
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var libelle_choix_prix = $("#choix_prix").children(":selected").data("libellechoixprix");
                var choix_prix = $("#choix_prix").val();
                var articleId = $("#article").val();
                var quantite = $("#quantite").val();
                var stock = $("#en_stock").val();
                var prix = $("#prix").val();

                if(parseInt(quantite) > parseInt(stock)){
                    $.gritter.add({
                        title: "SMART-SFV",
                        text: "La quantité à vendre ne doit pas depasser la quantité disponible en stock",
                        sticky: false,
                        image: basePath + "/assets/img/gritter/confirm.png",
                    });
                    $("#quantite").val("");
                    return;
                }
                    //Vérification Si la ligne existe déja dans le tableau
                var articleTrouver = _.findWhere(monPanier, {articles: articleId, choix_prix:choix_prix})
                if(articleTrouver) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = articleTrouver.quantites;
                        idElementLigne = articleTrouver.id;

                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);
                        if(parseInt(sommeDeuxQtes)> parseInt(stock)){
                            $.gritter.add({
                                title: "SMART-SFV",
                                text: "Cet article existe dans votre panier, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle disponible en stock",
                                sticky: false,
                                image: basePath + "/assets/img/gritter/confirm.png",
                            });
                            $("#quantite").val("");
                            return;
                        }
                            //MAJ de la ligne
                            montantTTC = parseInt(montantTTC) - parseInt(oldQte*prix);
                            $("#montant_a_payer").val(montantTTC);
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite : sommeDeuxQtes,
                                    montant: $.number(prix*sommeDeuxQtes),
                                }
                            });
                            articleTrouver.quantites = sommeDeuxQtes;

                            montantTTC = parseInt(montantTTC) + parseInt(sommeDeuxQtes*prix);

                            $("#quantite").val("");
                            $("#en_stock").val("");
                            $("#prix").val("");
                            $("#choix_prix").val("");
                            $("#montantTC").val("");
                            $("#code_barre").val("");
                            $("#article").select2("val","");
                            $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                            $("#montant_a_payer").val(montantTTC);
                            return;
                    }
                    idTablle++;
                    $tableAddRowArticle.bootstrapTable('insertRow',{
                        index: idTablle,
                        row: {
                          id: idTablle,
                          code_barre: code_barre,
                          libelle_article: libelle_article,
                          prix: $.number(prix),
                          choix_prix: libelle_choix_prix,
                          quantite: quantite,
                          article: articleId,
                          montant: $.number(quantite*prix),
                        }
                    })
                    montantTTC = parseInt(montantTTC) + parseInt(quantite*prix);
                    //Creation de l'article dans le tableau virtuel (panier)
                    var DataArticle = {'id':idTablle, 'articles':articleId,'choix_prix':choix_prix, 'quantites':quantite,'prix':prix};
                    monPanier.push(DataArticle);
                    $("#quantite").val("");
                    $("#en_stock").val("");
                    $("#prix").val("");
                    $("#montant").val("");
                    $("#code_barre").val("");
                    $("#choix_prix").val("");
                    $("#article").select2("val","");
                    $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                    $("#montant_a_payer").val(montantTTC);

                    if(idTablle>0){
                        $("#row_regle").show();
                        $(".delete-row").show();
                    }else{
                        $("#row_regle").hide();
                        $(".delete-row").hide();
                    }

            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs article, type de prix et quantité ne doivent pas être vides et la quantité minimum à vendre doit être 1.",
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                return;
            }
        })
        // Find and remove selected table rows
        $(".delete-row").click(function () {
           var selecteds = $tableAddRowArticle.bootstrapTable('getSelections');
           var ids = $.map($tableAddRowArticle.bootstrapTable('getSelections'), function (row) {
                        return row.id
                    })
                $tableAddRowArticle.bootstrapTable('remove', {
                    field: 'id',
                    values: ids
                })

                $.each(selecteds, function (index, value) {
                    var articleTrouver = _.findWhere(monPanier, {id: value.id})
                    montantTTC = parseInt(montantTTC) - parseInt(articleTrouver.quantites*articleTrouver.prix);
                    monPanier = _.reject(monPanier, function (article) {
                        return article.id == value.id;
                    });
                });

                $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                $("#montant_a_payer").val(montantTTC);

                if(monPanier.length==0){
                    $("#row_regle").hide();
                    $(".delete-row").hide();
                    montantTTC = 0;
                    $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                    $("#montant_a_payer").val(montantTTC);
                    idTablle = 0;
                }
        });

        // Submit the add form
        $("#sendButton").click(function(){
            $("#formAjout").submit();
            $("#sendButton").prop("disabled", true);
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
                var url = "{{route('vente.vente.store')}}";
                var formData = new FormData($(this)[0]);
                createFormData(formData, 'monPanier', monPanier);
            }else{
               var methode = 'POST';
                var url = "{{route('vente.update-vente')}}";
                 var formData = new FormData($(this)[0]);
            }
            editerVenteAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
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
                var url = "{{route('vente.articles-vente.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'articles-vente/' + id;
             }
            editerVentesArticlesAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle,$table, ajoutArticle);
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
    function createFormData(formData, key, data) {
        if (data === Object(data) || Array.isArray(data)) {
            for (var i in data) {
                createFormData(formData, key + '[' + i + ']', data[i]);
            }
        } else {
            formData.append(key, data);
        }
    }
    function updateRow(idVente) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });

        $("#client_id").select2("val", vente.client_id);
        $("#idVente").val(vente.id);
        $("#depot_id").prop('disabled',true);
        $tableArticle.bootstrapTable('refreshOptions', {url: "../vente/liste-articles-vente/" + idVente});
        $("#div_enregistrement").hide();
        $("#div_update").show();
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
        var vente = $("#idVente").val();
        $("#vente").val(vente);
        var depot = $("#depot_id").val();

        $.getJSON("../stock/liste-article-by-depot/" + depot, function (reponse) {
            $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, data) {
                $('#article_add').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
            });
        })

        $.getJSON("../stock/liste-article-by-article-depot/" + article.article.id + "/" + depot, function (reponse) {
            $.each(reponse.rows, function (index, articles_trouver) {
                $("#code_barre_add").val(articles_trouver.article.code_barre);
                $("#article_add").select2("val",  articles_trouver.article.id);
                $("#prix_add").val(articles_trouver.prix_vente_detail);
                $("#en_stock_add").val(articles_trouver.quantite_disponible);
                return;
            });
        })
        var montant = parseInt(article.quantite)*parseInt(article.prix);
        $('#montant_add').val(montant);
        $("#article_add").select2("val",  article.article.id);
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
    function panierRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: "../vente/liste-articles-vente/" + idVente});

        $(".bs-modal-panier").modal("show");
    }
    function reglementRow(idVente) {
        var $scope = angular.element($("#listeReglement")).scope();
        var vente =_.findWhere(rows, {id: idVente});
        $scope.$apply(function () {
            $scope.populateListeReglementForm(vente);
        });
        $tableListeReglement.bootstrapTable('refreshOptions', {url: "../vente/liste-reglements-by-facture/" + idVente});
       $(".bs-modal-liste-reglement").modal("show");
    }
    function facturePrintRow(idVente){
        window.open("facture-vente-pdf/" + idVente ,'_blank')
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function choixPrixFormatter(choix){
        if(choix == "prix_detail"){
            return '<span class="text-bold">Prix détail</span>';
        }
        if(choix == "prix_demi_gros"){
            return '<span class="text-bold">Prix démi gros</span>';
        }
        if(choix == "prix_gros"){
            return '<span class="text-bold">Prix en gros</span>';
        }
    }



    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
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

    function optionArticleFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionFormatter(id, row) {
        if(row.acompte_facture>0){
            return '<button type="button" class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Liste des règlements" onClick="javascript:reglementRow(' + row.id + ');"><i class="fa fa-money"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:panierRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
        }else{
            return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:panierRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>\n\
                    <button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
        }
    }
    function factureFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Facture" onClick="javascript:facturePrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }

    function imageFormatter(id, row) {
        return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }

    function editerVenteAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                        $("#row_regle").hide();
                        $("#div_enregistrement").show();
                        $("#div_update").hide();
                        $("#article").select2("val","");
                        $(".delete-row").hide();
                        $tableAddRowArticle.bootstrapTable('removeAll');
                        monPanier = [];
                        idTablle =  0;
                        montantTTC = 0;
                        $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                    }else { //Modification
                        $table.bootstrapTable('updateByUniqueId', {
                            id: reponse.data.id,
                            row: reponse.data
                        });
                        $table.bootstrapTable('refresh');
                        $(".bs-modal-ajout").modal("hide");
                    }
                    $("#row_regle").hide();
                    window.open("facture-vente-pdf/" + reponse.data.id ,'_blank')
                    location.reload();
                    $formObject.trigger('eventAjouter', [reponse.data]);
                    $("#sendButton").prop("disabled", false);
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
                $("#sendButton").prop("disabled", false);
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

    function editerVentesArticlesAction(methode, url, $formObject, formData, $ajoutLoader, $table,$tableVente, ajoutArticle = true) {
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
                    if (ajoutArticle) { //creation
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


