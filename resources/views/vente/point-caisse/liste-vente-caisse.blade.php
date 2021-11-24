@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="row">
    @if(Auth::user()->role == 'Caissier' && $caisse)
        <div class="col-md-2">
            <a class="btn btn-sm btn-danger pull-left" id="btnFermerCaisse"><i class="fa fa-lock"></i> Fermer la caisse</a>
        </div>
        <div class="col-md-3">
            <div class="form-group">
            <input type="text" class="form-control" id="searchByTicket" placeholder="Rechercher par N° du ticket">
            </div>
        </div>
    @endif
    @if(Auth::user()->role != 'Caissier' && $caisse)
        <div class="col-md-3">
            <div class="form-group">
            <input type="text" class="form-control" id="searchByTicket" placeholder="Rechercher par N° du ticket">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
            </div>
        </div>
    @endif
    <div class="col-md-5">
        <div class="form-group">
            <label class="text-bold h4"> Total espece : <span class="text-bold text-green" id="total_caisse_caissier">0</span> F CFA</label><br/>
        </div>
    </div><br/>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
        data-pagination="true"
        data-search="false"
        data-toggle="table"
        data-unique-id="id"
        data-show-toggle="false"
        data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="tiketFormatter" data-width="100px" data-align="center">Ticket</th>
            <th data-field="numero_ticket">N° Ticket</th>
            <th data-field="date_ventes">Date</th>
            <th data-field="montantTTC" data-formatter="montantFormatter">Montant TTC</th>
            <th data-field="montant_payer" data-formatter="montantFormatter">Montant pay&eacute; </th>
            @if(Auth::user()->role == 'Caissier')
            <th data-field="id" data-formatter="panierFormatter" data-width="60px" data-align="center">Panier</th>
            @endif
            @if(Auth::user()->role != 'Caissier')
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
            @endif
        </tr>
    </thead>
</table>

<!-- Modal recuperation et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 70%">
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
                    <input type="text" class="hidden" id="idVente" name="idVente" ng-hide="true" ng-model="vente.id"/>
                    <input type="text" class="hidden" id="depot_id" ng-hide="true" ng-model="vente.depot.id">
                    <input type="text" class="hidden" name="caisse_id" ng-hide="true" ng-model="vente.idCaisse">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="col-md-12">
                                    <div class="widget-user-header ">
                                        <h3 class="widget-user-username">D&eacute;p&ocirc;t : @{{vente.depot.libelle_depot}}</h3>
                                        <h5 class="widget-user-desc">Caisse : <b>@{{vente.libelle_caisse}}</b></h5>
                                        <h5 class="widget-user-desc">Caissier : <b>@{{vente.full_name}}</b></h5>
                                        <h5 class="widget-user-desc">Ticket : <b>@{{vente.numero_ticket}}</b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
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
                                <th data-field="prix" data-formatter="montantFormatter">Prix</th>
                                <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                <th data-formatter="montantTttcLigneFormatter">Montant TTC </th>
                                <th data-field="id" data-formatter="optionArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                            </tr>
                        </thead>
                    </table>
                    <hr/>
                    <div class="row">
                        <div class="col-md-3"><br/>
                            <ul class="nav nav-stacked" style="font-size: 20px;">
                                <li><a class="text-bold">Total <span class="pull-right text-bold" id="montantTTC">0 F CFA</span></a></li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Montant &agrave; payer *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" min="0" id="montant_a_payer" name="montant_a_payer" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Montant pay&eacute;</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" min="0" id="montant_paye" ng-model="vente.montant_payer" name="montant_paye" placeholder="Montant payé">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Moyen de paiement *</label>
                                <select class="form-control" id="moyen_reglement_id" name="moyen_reglement_id" ng-model="vente.moyen_reglement_id" required>
                                    <option value="">-- Moyen de paiement --</option>
                                    @foreach($moyenReglements as $moyenReglement)
                                        <option data-libellemoyen="{{ $moyenReglement->libelle_moyen_reglement }}" value="{{ $moyenReglement->id }}">{{ $moyenReglement->libelle_moyen_reglement }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="carteFideliteLine">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-5">
                            <label for="numero_carte_fidelite">Num&eacute;ro de la carte de fid&eacute;lit&eacute;</label>
                            <input type="text" class="form-control" name="numero_carte_fidelite" placeholder="Numero de la carte">
                        </div>
                        <div class="col-md-4">
                            <label for="numero_carte_fidelite">Montant</label>
                            <input type="text" pattern="[0-9]*" class="form-control" name="montant_carte" placeholder="Montant">
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
    <div class="modal-dialog" style="width:70%">
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
                               <input type="text" class="form-control" id="code_barre" autofocus>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Article *</label>
                               <select name="article_id" class="form-control" id="article" required ng-model="article.article.id">
                                   <option value="">-- Selcetionner l'article --</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Prix</label>
                               <input type="text" class="form-control" name="prix" ng-model="article.prix" id="prix" placeholder="Prix" readonly>
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
                               <input type="number" id="quantite" min="0" name="quantite" ng-model="article.quantite" class="form-control" placeholder="Qté / Btle à vendre" required>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Montant</label>
                               <input type="text" class="form-control" id="montant" placeholder="Montant TTC" readonly>
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
                                    <th data-field="prix" data-formatter="montantFormatter">Prix</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant Montant TTC</th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
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
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer cette vente<br/><b>@{{vente.numero_ticket}}</b></div>
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

<!-- Modal fermeture caisse-->
<div class="modal fade bs-modal-fermeture-caisse" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
        <form id="formFermetureCaisse" action="#" method="post">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="circle">
                        Fermeture de caisse
                    </span>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3 border-right">
                                    <div class="description-block">
                                        <span class="description-text text-black">Montant a l'ouverture</span>
                                        <h5 class="description-header"><p class="text-black" id="montant_ouverture"></p></h5>
                                    </div>
                                </div>
                                <div class="col-sm-3 border-right">
                                    <div class="description-block">
                                        <span class="description-text text-green">Total entree</span>
                                        <h5 class="description-header"><p class="text-green" id="total_entree"></p></h5>
                                    </div>
                                </div>
                                <div class="col-sm-3 border-right">
                                    <div class="description-block">
                                        <span class="description-text text-red">Total sortie</span>
                                        <h5 class="description-header"><p class="text-red" id="total_sortie"></p></h5>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="description-block">
                                        <span class="description-text text-orange">Solde</span>
                                        <h5 class="description-header"><p class="text-orange" id="total_solde"></p></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="h3">Montant &agrave; la fermeture : <span class="h3" id="solde_fermeture_aff"></span></label>
                                        <input type="text" class="hidden" name="solde_fermeture" id="solde_fermeture"/>
                                        <input type="text" class="hidden" name="caisses_fermeture" id="caisses_fermeture"/>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->role=='Administrateur' or Auth::user()->role=='Concepteur')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="motif_non_conformite" placeholder="Motif de non confirmité de la caisse"/>
                                    </div>
                                 </div>
                            </div>
                            @endif
                            <label class="text-center text-red">Assurez-vous du montant r&eacute;el de votre caisse. Contacter l'administrateur en cas d'anomalie.</label><br/>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Billet *</label>
                                    <select class="form-control" id="billet">
                                        <option value="">-- Selcetionner un element --</option>
                                        <option value="10000"> 10000</option>
                                        <option value="5000"> 5000</option>
                                        <option value="2000"> 2000</option>
                                        <option value="1000"> 1000</option>
                                        <option value="500"> 500</option>
                                        <option value="250"> 250</option>
                                        <option value="200"> 200</option>
                                        <option value="100"> 100</option>
                                        <option value="50"> 50</option>
                                        <option value="25"> 25</option>
                                        <option value="10"> 10</option>
                                        <option value="5"> 5</option>
                                        <option value="0"> 0</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantit&eacute; </label>
                                    <input type="number" min="0" class="form-control" id="quantite_billet" placeholder="Quantité">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Montant</label>
                                    <input type="text" class="form-control" id="montant_billet" placeholder="Montant" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-sm  add-billetage-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-xs delete-billetage-row">Supprimer ligne</button><br/><br/>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tableBilletage" class="table table-success table-striped box box-warning"
                                        data-toggle="table"
                                        data-id-field="id"
                                        data-unique-id="id"
                                        data-click-to-select="true"
                                        data-show-footer="false">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th data-field="id">ID</th>
                                            <th data-field="billet">Billet</th>
                                            <th data-field="quantite_billet">Quantit&eacute;</th>
                                            <th data-field="montant_billet">Montant</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="sendCaisseFermetureButton" class="btn btn-sm btn-danger"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span> Fermer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if($caisse)
    <input type="text" class="hidden" id="role_user" value="{{ Auth::user()->role }}"/>
    <input type="text" class="hidden" id="caisse" value="{{ $caisse->id }}"/>
@endif

<script type="text/javascript">
    var ajoutArticle = false;
    var $table = jQuery("#table"), rows = [], $tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tablePanierArticle = jQuery("#tablePanierArticle"), $tableBilletage = jQuery("#tableBilletage");
    var panierBillet = [];
    var idTablleBillet =  0;
    var role_user = $("#role_user").val();
    var caisse_id = $('#caisse').val();

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

    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
            $scope.vente = vente;
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

    $(function () {
        $("#carteFideliteLine").hide();

        if(role_user == 'Caissier'){
            $table.bootstrapTable('refreshOptions', {url: "{{url('vente', ['action' => 'liste-ventes-caisse'])}}"});
        }else{
            $table.bootstrapTable('refreshOptions', {url: '../liste-ventes-caisse/' + caisse_id});
        }
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
            $("#total_caisse_caissier").html($.number(data.totalCaisse));
        });
        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
            $("#montantTTC").html($.number(data.montantTTC));
            $("#montant_a_payer").val(data.montantTTC);
        });



        $("#article").select2({width: '100%'});

        $("#moyen_reglement_id").change(function (e) {
            var libelle_moyen = $("#moyen_reglement_id").children(":selected").data("libellemoyen");
            if(libelle_moyen == "CARTE DE FIDELITE"){
                $("#carteFideliteLine").show();
            }else{
                $("#carteFideliteLine").hide();
            }
        });

        $("#searchByTicket").keyup(function (e) {
            var numero_ticke = $("#searchByTicket").val();
            $("#searchByDate").val("");
            if(role_user == 'Caissier'){
                if(numero_ticke == ""){
                    $table.bootstrapTable('refreshOptions', {url: "{{url('vente', ['action' => 'liste-ventes-caisse'])}}"});
                }else{
                   $table.bootstrapTable('refreshOptions', {url: '../vente/liste-ventes-caisse-by-numero/' + numero_ticke});
                }
            }else{
                if(numero_ticke == ""){
                    $table.bootstrapTable('refreshOptions', {url: '../liste-ventes-caisse/' + caisse_id});
                }else{
                   $table.bootstrapTable('refreshOptions', {url: '../liste-ventes-caisse-by-numero/' + numero_ticke + '/' + caisse_id});
                }
            }
        });

        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            $("#searchByTicket").val("");
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: '../liste-ventes-caisse/' + caisse_id});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../liste-ventes-caisse-by-date/' + date + '/' + caisse_id});
            }
        });

        $('#searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });

        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var depot = $("#depot_id").val();
            var vente = $("#idVente").val();
            $('#code_barre').val("");
            if(role_user == 'Caissier'){
                var url = "../stock/liste-article-by-depot/";
            }else{
                var url ="/stock/liste-article-by-depot/";
            }
            $.getJSON(url + depot, function (reponse) {
                $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                    $.each(reponse.rows, function (index, data) {
                    $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                });
            })
            document.forms["formAjoutArticle"].reset();
            $("#vente").val(vente);
            $(".bs-modal-add-article").modal("show");
        });

        $("#btnFermerCaisse").on("click", function () {
            var caisse_id = $('#caisse').val();
            $.getJSON("../comptabilite/get-caisse-ouverte-by-caisse/" + caisse_id, function (reponse) {
                $.each(reponse.rows, function (index, caisse_ouverte) {
                        var solde_fermeture = caisse_ouverte.montantTTC+caisse_ouverte.montant_ouverture + caisse_ouverte.entree - caisse_ouverte.sortie;
                        $("#montant_ouverture").html($.number(caisse_ouverte.montant_ouverture));
                        $("#total_entree").html($.number(caisse_ouverte.montantTTC+caisse_ouverte.entree));
                        $("#total_sortie").html($.number(caisse_ouverte.sortie));
                        $("#total_solde").html($.number(solde_fermeture));
                        $("#solde_fermeture_aff").html($.number(solde_fermeture));
                        $("#solde_fermeture").val(solde_fermeture);
                });
            });

           $(".bs-modal-fermeture-caisse").modal("show");
        });

        $("#quantite_billet").change(function (e) {
            var quantite_billet = $("#quantite_billet").val();
            var billet = $("#billet").val();
            if(billet!="" && quantite_billet!=""){
                var qte = parseInt(billet)*parseInt(quantite_billet);
                $("#montant_billet").val(qte);
            }else{
                $("#montant_billet").val("");
            }
        });
        $("#quantite_billet").keyup(function (e) {
            var quantite_billet = $("#quantite_billet").val();
            var billet = $("#billet").val();
            if(billet!="" && quantite_billet!=""){
                var qte = parseInt(billet)*parseInt(quantite_billet);
                $("#montant_billet").val(qte);
            }else{
                $("#montant_billet").val("");
            }
        });

        $('#code_barre').keyup(function(e){
            var code_barre = $('#code_barre').val();
            var depot = $("#depot_id").val();
            if(role_user == 'Caissier'){
                var url = "../stock/liste-article-by-depot-code-barre/";
            }else{
                var url ="/stock/liste-article-by-depot-code-barre/";
            }
            $.getJSON(url + depot + "/" + code_barre, function (reponse) {
                $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, retour) {
                    $('#article').append('<option selected value=' + retour.article.id + '>' + retour.article.libelle_article + '</option>')
                    $("#article").select2("val",  retour.article.id);
                    $("#prix").val(retour.prix_vente_detail);
                    $("#en_stock").val(retour.quantite_disponible);
                    return;
                });
            })
        });
        $('#article').change(function(){
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();
            $('#code_barre').val("");
            if(role_user == 'Caissier'){
                var url = "../stock/liste-article-by-article-depot/";
            }else{
                var url ="/stock/liste-article-by-article-depot/";
            }

            $.getJSON(url + article_id + "/" + depot_id, function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                   // $("#code_barre").val(articles_trouver.article.code_barre);
                   var id = articles_trouver.article.id
                    $("#prix").val(articles_trouver.prix_vente_detail);
                    $("#en_stock").val(articles_trouver.quantite_disponible);
                    return;
                });
                $("#article").select2("val",id);
            })
        });

        $('#quantite').keyup(function(){
            var quantite = $('#quantite').val();
            var prix = $("#prix").val();
            var montant = parseInt(quantite)*parseInt(prix);
            $('#montant').val(montant);
        });

         //Add billet row on table
         $(".add-billetage-row").click(function () {
            if($("#billet").val() != '' && $("#quantite_billet").val() != '' && $("#quantite_billet").val()!=0) {
                var billet = $("#billet").val();
                var quantite_billet = $("#quantite_billet").val();

                //Vérification Si la ligne existe déja dans le tableau
                var ligneBilletTrouver = _.findWhere(panierBillet, {billets: billet})
                if(ligneBilletTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = ligneBilletTrouver.quantite_billets;
                        idElementLigne = ligneBilletTrouver.id;

                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite_billet);
                            //MAJ de la ligne
                            $tableBilletage.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite_billet : sommeDeuxQtes,
                                    montant_billet: $.number(billet*sommeDeuxQtes),
                                }
                            });
                            ligneBilletTrouver.quantite_billets = sommeDeuxQtes;

                            $("#quantite_billet").val("");
                            $("#billet").val("");
                            $("#montant_billet").val("");
                            return;
                    }
                    idTablleBillet++;
                    $tableBilletage.bootstrapTable('insertRow',{
                        index: idTablleBillet,
                        row: {
                          id: idTablleBillet,
                          billet: billet,
                          quantite_billet: quantite_billet,
                          montant_billet: $.number(quantite_billet*billet)
                        }
                    })

                    //Creation de l'article dans le tableau virtuel (panier)
                    var DataBillet= {'id':idTablleBillet, 'billets':billet, 'quantite_billets':quantite_billet};
                    panierBillet.push(DataBillet);

                    $("#quantite_billet").val("");
                    $("#billet").val("");
                    $("#montant_billet").val("");
                    if(idTablleBillet>0){
                        $(".delete-billetage-row").show();
                    }else{
                        $(".delete-billetage-row").hide();
                    }

            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs billet et quantité ne doivent pas être vides et la quantité minimum doit être 1.",
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                return;
            }
        })
        // Find and remove selected table rows
        $(".delete-billetage-row").click(function () {
           var selecteds = $tableBilletage.bootstrapTable('getSelections');
           var ids = $.map($tableBilletage.bootstrapTable('getSelections'), function (row) {
                        return row.id
                    })
                $tableBilletage.bootstrapTable('remove', {
                    field: 'id',
                    values: ids
                })

                $.each(selecteds, function (index, value) {
                    var ligneTrouver = _.findWhere(panierBillet, {id: value.id})
                    panierBillet = _.reject(panierBillet, function (article) {
                        return article.id == value.id;
                    });
                });

                if(panierBillet.length==0){
                    $(".delete-billetage-row").hide();
                    idTablleBillet = 0;
                }
        });

        // Submit the add form
        $("#sendButton").click(function(){
            $("#formAjout").submit();
            $("#sendButton").prop("disabled", true);
        });

        //Submit the caisse fermeture formr
        $("#sendCaisseFermetureButton").click(function(){
            $("#formFermetureCaisse").submit();
            $("#sendCaisseFermetureButton").prop("disabled", true);
        });

        $("#formAjout").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjout .loader-overlay");

            var methode = 'POST';
            var url = "{{route('vente.caisse-update')}}";

            editerVenteCaisseAction(methode, url, $(this), $(this).serialize(), $ajaxLoader);
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

        $("#formFermetureCaisse").submit(function(e){
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var caisse = $('#caisse').val();
            $("#caisses_fermeture").val(caisse);
            var $ajaxLoader = $("#formFermetureCaisse .loader-overlay");
            var methode = 'POST';
            var url = "{{route('comptabilite.fermeture-caisse')}}";
            var formData = new FormData($(this)[0]);
            createFormData(formData, 'panierBillet', panierBillet);
            fermetureCaisseAction(methode, url, $(this), formData, $ajaxLoader);
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
        var $scope = angular.element($("#formAjout")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $tableArticle.bootstrapTable('refreshOptions', {url: "../liste-articles-vente/" + idVente});

        $(".bs-modal-ajout").modal("show");
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
        $("#quantite").val(article.quantite);
        if(role_user == 'Caissier'){
            var url = "../stock/liste-article-by-depot/";
        }else{
            var url ="/stock/liste-article-by-depot/";
        }
        $.getJSON(url + depot, function (reponse) {
            $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, data) {
                $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
            });
        })
        if(role_user == 'Caissier'){
            var url = "../stock/liste-article-by-article-depot/";
        }else{
            var url ="/stock/liste-article-by-article-depot/";
        }
        $.getJSON(url + article.article.id + "/" + depot_id, function (reponse) {
            $.each(reponse.rows, function (index, articles_trouver) {
                $("#code_barre").val(articles_trouver.article.code_barre);
                $("#article").select2("val",  articles_trouver.article.id);
                $("#prix").val(articles_trouver.prix_vente_detail);
                $("#en_stock").val(articles_trouver.quantite_disponible);
                return;
            });
        })
        var montant = parseInt(article.quantite)*parseInt(article.prix);
        $('#montant').val(montant);
        $("#article").select2("val",  article.article.id);
        $(".bs-modal-add-article").modal("show");
    }
    function deleteRow(idVente){
        var $scope = angular.element($("#formSupprimer")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $(".bs-modal-suppression").modal("show");
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
        if(role_user == 'Caissier'){
            var urls = "../vente/liste-articles-vente/";
        }else{
            var urls ="/vente/liste-articles-vente/";
        }
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: urls + idVente});
        $(".bs-modal-panier").modal("show");
    }

    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function ticketPrintRow(idVente){
        window.open("../ticket-caisse-pdf/" + idVente ,'_blank')
    }
    function tiketFormatter(id, row){
        if(row.attente){
            return '<button type="button" class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Recupérer" onClick="javascript:updateRow(' + row.id + ');">Recupérer</button>';
        }else{
            return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Ticket" onClick="javascript:ticketPrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
        }
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function carteFormatter(montant){
        if(montant==0){
            return '<span class="text-bold">' + $.number(montant)+ '</span>';
        }
        return montant > 0 ? '<span class="text-bold text-green">' + $.number(montant)+ '</span>' : '<span class="text-bold text-red">' + $.number(montant)+ '</span>';
    }
    function panierFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:panierRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
    }
    function optionFormatter(id){
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:panierRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>\n\
                <button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionArticleFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function editerVenteCaisseAction(methode, url, $formObject, formData, $ajoutLoader) {
        jQuery.ajax({
            type: methode,
            url: url,
            cache: false,
            data: formData,
            success:function (reponse, textStatus, xhr){
                if (reponse.code === 1) {
                    if(reponse.data.attente!=1){
                        window.open("ticket-caisse-pdf/" + reponse.data.id ,'_blank')
                    }
                    location.reload();
                }else{
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

    function editerVentesArticlesAction(methode, url, $formObject, formData, $ajoutLoader, $table, $tableVente, ajoutArticle = true) {
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
                    ajoutArticle = false;
                    $("#article").select2("val", "");
                }
                $("#montant_payer").val(0);
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

    function fermetureCaisseAction(methode, url, $formObject, formData, $ajoutLoader) {
        jQuery.ajax({
            type: methode,
            url: url,
            cache: false,
            data: formData,
            contentType: false,
            processData: false,
            success:function (reponse, textStatus, xhr){
                if (reponse.code === 1) {
                    //Si la caisse est fermée on génère l'etat et on recharge la page
                    window.open("../vente/billetage-pdf/" + reponse.data.id ,'_blank')
                    location.reload();
                }else{
                    $("#sendCaisseFermetureButton").prop("disabled", false);
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
                $("#sendCaisseFermetureButton").prop("disabled", false);
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
@endsection
