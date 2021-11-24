@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant' or Auth::user()->role == 'Logistic')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-transferts-stocks'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="id" data-formatter="printFormatter" data-width="60px" data-align="center"><i class="fa fa-print"></i></th>
            <th data-field="date_transferts">Date</th>
            <th data-field="depot_depart.libelle_depot">D&eacute;p&ocirc;t du d&eacute;part</th>
            <th data-field="depot_arrivee.libelle_depot">D&eacute;p&ocirc;t d'arriv&eacute;e</th>
            @if(Auth::user()->role != 'Logistic')
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
            @endif
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 80%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-map-signs fa-2x"></i>
                        Gestion de transfert de stock
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idTransfertStockModifier" name="idTransfertStockModifier" ng-hide="true" ng-model="transfertStock.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date du transf&egrave;rt *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="transfertStock.date_transferts" id="date_transfert" name="date_transfert" value="<?= date('d-m-Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t du d&eacute;part *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_depart_id" id="depot_depart_id" ng-model="transfertStock.depot_depart_id" class="form-control select2" required>
                                        <option value="" ng-show="false">-- Selectionner le d&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t d'arriv&eacute;e *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_arrivee_id" id="depot_arrivee_id" ng-model="transfertStock.depot_arrivee_id" class="form-control select2" required>
                                        <option value="" ng-show="false">-- Selectionner le d&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
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
                                        <option value="">-- Selectionner l'article --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>En Stock</label>
                                    <input type="number" class="form-control" id="en_stock" placeholder="0" readonly>
                                </div>
                            </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; transf&eacute;rer *</label>
                                    <input type="number" min="1" class="form-control" id="quantite" placeholder="Qté / Btle">
                                </div>
                            </div>
                            <!--
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; la reception *</label>-->
                                    <input type="hidden" min="1" class="form-control" id="quantite_reception" placeholder="Qté / Btle">
                                <!--</div>
                            </div>
                            -->
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label></label>
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
                                            <th data-field="id">Id</th>
                                            <th data-field="code_barre">Code barre</th>
                                            <th data-field="libelle_article">Article</th>
                                            <th data-field="quantite">Qt&eacute; &agrave; transf&eacute;rer </th>
                                            <!--<th data-field="quantite_reception">Qt&eacute; &agrave; la reception </th>-->
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
                                    <th data-field="article.code_barre">Code</th>
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-field="quantite_depart" data-align="center">Quantit&eacute; transfer&eacute;e </th>
                                    <!--<th data-field="quantite_reception" data-align="center">Quantit&eacute; en reception</th>-->
                                    <th data-field="id" data-formatter="optionAArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="sendButton" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
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
                   <input type="text" class="hidden" id="transfert_stock"  name="transfert_stock_id"/>
                   <input type="text" class="hidden" id="depot1"  name="depot_depart_id"/>
                   <input type="text" class="hidden" id="depot2"  name="depot_arrivee_id"/>
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
                               <select name="article_id" class="form-control" id="article_add" required ng-model="article.article_id">
                                   <option value="">-- Selcetionner l'article --</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>En Stock</label>
                               <input type="number" class="form-control" id="en_stock_add" placeholder="Qté / Btle en stock" readonly>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Qt&eacute; &agrave; transf&eacute;rer *</label>
                               <input type="number" min="0" name="quantite" ng-model="article.quantite_depart" class="form-control" id="quantite_add" placeholder="Qté / Btle à transférer" required>
                           </div>
                       </div>
                        <!--<div class="col-md-2">
                            <div class="form-group">
                                <label>Qt&eacute; &agrave; la reception *</label>-->
                                <input type="hidden" min="1" class="form-control" id="quantite_reception_add" name="quantite_reception" ng-model="article.quantite_reception" placeholder="Qté / Btle à recevoir">
                            <!--</div>
                        </div>-->
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
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
                    <input type="text" class="hidden" id="idTransfertStockSupprimer"  ng-model="transfertStock.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le transf&egrave;rt du stock du <br/><b>@{{transfertStock.depot_depart.libelle_depot}}</b> dans <b>@{{transfertStock.depot_arrivee.libelle_depot}}</b> survenu le <b>@{{transfertStock.date_transferts}}</b></div>
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

<!-- Modal lot Transfert Article -->
<div class="modal fade bs-modal-lot-transfert" id="lotTransfertForm" ng-controller="lotTransfertFormCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Liste des articles du transfert N° <b>@{{transfertStock.id}}</b> du <b>@{{transfertStock.depot_depart.libelle_depot}}</b> au <b>@{{transfertStock.depot_arrivee.libelle_depot}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="lotTransfertArticle" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.code_barre">Code</th>
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-field="quantite_depart" data-align="center">Quantit&eacute; transfer&eacute;e </th>
                                    <th data-field="quantite_reception" data-align="center">Quantit&eacute; en reception</th>
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
    var $table = jQuery("#table"), rows = [],$lotTransfertArticle = jQuery("#lotTransfertArticle"),$tableListeArticle = jQuery("#tableListeArticle"), $tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle");
    var lotTransfert = [];
    var idTablle =  0;
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (transfertStock) {
            $scope.transfertStock = transfertStock;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.transfertStock = {};
        };
    });
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (transfertStock) {
            $scope.transfertStock = transfertStock;
        };
        $scope.initForm = function () {
            $scope.transfertStock = {};
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
     appSmarty.controller('lotTransfertFormCtrl', function ($scope) {
        $scope.populateFormLotTransfert = function (transfertStock) {
        $scope.transfertStock = transfertStock;
        };
    });

    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
        });
        $("#depot_depart_id, #depot_arrivee_id, #article").select2({width: '100%', allowClear: true});
        $("#depot_depart_id, #depot_arrivee_id").prop("disabled", false);
        $('#date_transfert, #searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });

        // Modifier la qte à la reception automatiquement
        $("#quantite").change(function () {
            var qte = $("#quantite").val();
            $("#quantite_reception").val(qte);
        });

        $("#quantite_add").change(function () {
            var qte = $("#quantite_add").val();
            $("#quantite_reception_add").val(qte);
        });

        $("#searchByDate").change(function (e) {
            $("#searchByArticle").val(0);
            var date = $("#searchByDate").val();
            if(date == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-transferts-stocks'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-transferts-stocks-by-date/' + date});
            }
        });

        $("#div_enregistrement").show();
        $("#div_update").hide();
        $(".delete-row").hide();
        $("#btnModalAjout").on("click", function () {
            $("#depot_depart_id, #depot_arrivee_id").prop("disabled", false);
            $('#article_id').html("<option>-- Selectionner l'article --</option>");
            $("#depot_depart_id").select2("val","");
            $("#article").select2("val","");
            $('#code_barre').val("");
            $tableAddRowArticle.bootstrapTable('removeAll');
            lotTransfert = [];
            idTablle = 0;
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $(".delete-row").hide();
        });
        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var transfert_stock = $("#idTransfertStockModifier").val();
            document.forms["formAjoutArticle"].reset();
            $("#transfert_stock").val(transfert_stock);
            $('#code_barre_add').val("");
            $("#article_add").val("");
            var depot = $("#depot_depart_id").val();
            var depot2 = $("#depot_arrivee_id").val();
            $("#depot1").val(depot);
            $("#depot2").val(depot2);
            var depot_depart_id = $('#depot_depart_id').val();
            $.getJSON("../stock/liste-article-by-depot/" + depot_depart_id, function (reponse) {
                $('#article_add').html("<option>-- Selectionner l'article --</option>");
                    $.each(reponse.rows, function (index, data) {
                    $('#article_add').append('<option value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                });
            })
            $(".bs-modal-add-article").modal("show");
        });

        $('#depot_arrivee_id').change(function(){
            if($("#depot_depart_id").val()!="" && $("#depot_depart_id").val()==$("#depot_arrivee_id").val()){
                $("#depot_arrivee_id").select2("val", "");
                alert('Attention vous avez choisir le même dépôt');
            }
        });

        $('#depot_depart_id').change(function(){
            $('#code_barre').val("");
            var depot_depart_id = $('#depot_depart_id').val();
            $.getJSON("../stock/liste-article-by-depot/" + depot_depart_id, function (reponse) {
                $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, data) {
                    $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                });
            })
        });
        $('#code_barre').keyup(function(e){
            if($("#depot_depart_id").val()==""){
                $('#code_barre').val("");
                alert('Selctionner le dépôt du depart');
                return;
            }
            if(e.which == '10' || e.which == '13') {
                var code_barre = $('#code_barre').val();
                var depot = $("#depot_depart_id").val();
                $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot + "/" + code_barre , function (reponse) {
                    $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                    $.each(reponse.rows, function (index, retour) {
                        $('#article').append('<option selected data-libellearticle= "' + retour.article.libelle_article + '" value=' + retour.article.id + '>' + retour.article.libelle_article + '</option>')
                        $("#article").select2("val",  retour.article.id);
                        $("#en_stock").val(retour.quantite_disponible);
                    });
                })

            e.preventDefault();
            e.stopPropagation();
            }
        });
        $('#code_barre_add').keyup(function(e){
            var code_barre = $('#code_barre_add').val();
            var depot = $("#depot_depart_id").val();
            $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot + '/' + code_barre , function (reponse) {
                $('#article_add').html("<option value=''>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, retour) {
                    $('#article_add').append('<option selected value=' + retour.article.id + '>' + retour.article.libelle_article + '</option>')
                    $("#article_add").select2("val",  retour.article.id);
                    $("#en_stock_add").val(retour.quantite_disponible);
                });
            })
        });
        $('#article').change(function(){
            var article_id = $("#article").val();
            var depot_id = $("#depot_depart_id").val();
            $('#code_barre').val("");
            $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id, function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre").val(articles_trouver.article.code_barre);
                    $("#en_stock").val(articles_trouver.quantite_disponible);
                });
            })
        });
        $('#article_add').change(function(){
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_depart_id").val();
             $('#code_barre_add').val("");
             $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre_add").val(articles_trouver.article.code_barre);
                    $("#en_stock_add").val(articles_trouver.quantite_disponible);
                });
            })
        });

        //Add row on table
        $(".add-row").click(function () {
            if($("#article").val() != '' && $("#quantite").val() != '' && $("#quantite").val()>0 && $("#quantite_reception").val()!="" && $("#quantite_reception").val()>0) {
                var code_barre = $("#code_barre").val();
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var articleId = $("#article").val();
                var quantite_reception = $("#quantite_reception").val();
                var quantite = $("#quantite").val();
                var stock = $("#en_stock").val();

                if(parseInt(quantite) > parseInt(stock)){
                    $.gritter.add({
                        title: "SMART-SFV",
                        text: "La quantité à transférer ne doit pas depasser la quantité disponible en stock",
                        sticky: false,
                        image: basePath + "/assets/img/gritter/confirm.png",
                    });
                    $("#quantite").val("");
                    return;
                }else{
                    //Vérification Si la ligne existe déja dans le tableau
                    var articleTrouver = _.findWhere(lotTransfert, {articles: articleId})
                    if(articleTrouver!=null) {
                        var qte = articleTrouver.quantites;
                        var somme = parseInt(qte)+parseInt(quantite);
                        if(parseInt(somme)> parseInt(stock)){
                                $.gritter.add({
                                    title: "SMART-SFV",
                                    text: "Cet article existe dans votre lot de transfert, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle disponible en stock",
                                    sticky: false,
                                    image: basePath + "/assets/img/gritter/confirm.png",
                                });
                                $("#quantite").val("");
                                return;
                        }
                    }
                    var ligneTrouver = _.findWhere(lotTransfert, {articles: articleId})
                    if(ligneTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = ligneTrouver.quantites;
                        oldQteRec = ligneTrouver.quantite_receptions;
                        idElementLigne = ligneTrouver.id;

                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);
                        var sommeDeuxQtesRec = parseInt(oldQteRec) + parseInt(quantite_reception);

                            //MAJ de la ligne
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite : sommeDeuxQtes,
                                    quantite_reception : sommeDeuxQtesRec,
                                }
                            });
                            ligneTrouver.quantites = sommeDeuxQtes;
                            ligneTrouver.quantite_receptions = sommeDeuxQtesRec;
                            console.log(lotTransfert);
                            $("#quantite").val("");
                            $("#en_stock").val("");
                            $("#code_barre").val("");
                            $("#article").select2("val","");
                            $("#quantite_reception").val("");
                            var depot_depart_id = $('#depot_depart_id').val();
                            $.getJSON("../stock/liste-article-by-depot/" + depot_depart_id, function (reponse) {
                                $('#article').html("<option>-- Selectionner l'article --</option>");
                                    $.each(reponse.rows, function (index, data) {
                                    $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                                });
                            })
                            return;
                    }
                    idTablle++;
                    $tableAddRowArticle.bootstrapTable('insertRow',{
                        index: idTablle,
                        row: {
                          id: idTablle,
                          code_barre: code_barre,
                          libelle_article: libelle_article,
                          quantite: quantite,
                          article: articleId,
                          quantite_reception: quantite_reception,
                        }
                    })

                    //Creation de l'article dans le tableau virtuel (lot de transfert)
                    var DataArticle = {'id':idTablle, 'articles':articleId,'quantites':quantite,'quantite_receptions':quantite_reception};
                    lotTransfert.push(DataArticle);
                    $("#quantite").val("");
                    $("#en_stock").val("");
                    $("#code_barre").val("");
                    $("#article").select2("val","");
                    $("#quantite_reception").val("");

                    var depot_depart_id = $('#depot_depart_id').val();
                            $.getJSON("../stock/liste-article-by-depot/" + depot_depart_id, function (reponse) {
                                $('#article').html("<option>-- Selectionner l'article --</option>");
                                    $.each(reponse.rows, function (index, data) {
                                    $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                                });
                            })
                    if(idTablle>0){
                        $(".delete-row").show();
                    }else{
                        $(".delete-row").hide();
                    }
                }
            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs article, colis et quantité ne doivent pas être vides et la quantité minimum à transférer doit être 1.",
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
                    var articleTrouver = _.findWhere(lotTransfert, {id: value.id})
                    lotTransfert = _.reject(lotTransfert, function (article) {
                        return article.id == value.id;
                    });
                });

                if(lotTransfert.length==0){
                    $(".delete-row").hide();
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
                var url = "{{route('stock.transfert-stocks.store')}}";
                 var formData = new FormData($(this)[0]);
                createFormData(formData, 'lotTransfert', lotTransfert);
             }else{
                var methode = 'POST';
                var url = "{{route('stock.update-transfert-stocks')}}";
                var formData = new FormData($(this)[0]);
             }
            editerTransfertStockAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
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
                var url = "{{route('stock.article-transferts.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'article-transferts/' + id;
             }
            editerArticleTransfertsAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle, ajoutArticle);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idTransfertStockSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('transfert-stocks/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });

        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('article-transferts/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle);
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
    function updateRow(idTransfertStock) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var transfertStock =_.findWhere(rows, {id: idTransfertStock});
         $scope.$apply(function () {
            $scope.populateForm(transfertStock);
        });
        $("#depot_depart_id, #depot_arrivee_id").prop("disabled", true);
        $("#idTransfertStockModifier").val(transfertStock.id);
        $("#depot_depart_id").select2("val", transfertStock.depot_depart_id);
        $("#depot_arrivee_id").select2("val", transfertStock.depot_arrivee_id);
        $tableArticle.bootstrapTable('refreshOptions', {url: "../stock/liste-articles-transferts/" + idTransfertStock});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idTransfertStock) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var transfertStock =_.findWhere(rows, {id: idTransfertStock});
           $scope.$apply(function () {
              $scope.populateForm(transfertStock);
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

        var transfertStock = $("#idTransfertStockModifier").val();
        $("#transfert_stock").val(transfertStock);
        var depot = $("#depot_depart_id").val();
        var depot2 = $("#depot_arrivee_id").val();
        $("#depot1").val(depot);
        $("#depot2").val(depot2);
        $.getJSON("../stock/liste-article-by-depot/" + depot, function (reponse) {
            $('#article_add').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, data) {
                $('#article_add').append('<option value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
            });
            $("#article_add").val(article.article_id);
        })

        $.getJSON("../stock/get-article-by-id/" + article.article_id , function (reponse) {
            $.each(reponse.rows, function (index, articles_trouver) {
                $("#code_barre_add").val(articles_trouver.code_barre);
            });
        })
        $.getJSON("../stock/liste-article-by-article-depot/"+ article.article_id + "/" + depot  , function (reponse) {
                $.each(reponse.rows, function (index, articles) {
                    $("#en_stock_add").val(articles.quantite_disponible);
                });
        })
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

    function printRow(idTransfertStock){
        window.open("transfert-stock-pdf/" + idTransfertStock ,'_blank')
    }

    function listeArticleRow(idTransfertStock){
        var $scope = angular.element($("#lotTransfertForm")).scope();
        var transfertStock =_.findWhere(rows, {id: idTransfertStock});
         $scope.$apply(function () {
            $scope.populateFormLotTransfert(transfertStock);
        });
        $lotTransfertArticle.bootstrapTable('refreshOptions', {url: "../stock/liste-articles-transferts/" + idTransfertStock});
        $(".bs-modal-lot-transfert").modal("show");
    }
    function printFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Articles transférés" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
                <button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionAArticleFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function editerTransfertStockAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#depot_depart_id, #depot_arrivee_id").select2("val", "");
                    $('#code_barre, #en_stock').val("");
                    $("#div_enregistrement").show();
                    $("#div_update").hide();
                    $(".delete-row").hide();
                    $tableAddRowArticle.bootstrapTable('removeAll');
                    lotTransfert = [];
                    idTablle =  0;
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
                $("#sendButton").prop("disabled", false);
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
            $("#sendButton").prop("disabled", false);
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
    function editerArticleTransfertsAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajoutArticle = true) {
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
                    $(".bs-modal-add-article").modal("hide");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
                ajout = false;
            }
            $("#montant_payer_add").val(0);
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
    function supprimerArticleAction(url, formData, $question, $ajaxLoader, $table) {
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
