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
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date d'inventaire">
    </div>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByDepot">
        <option value="0">-- Tous les d&eacute;p&ocirc;ts --</option>
        @foreach($depots as $depot)
        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
        @endforeach
    </select>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-inventaires'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="ficheInventaireFormatter" data-width="60px">Fiche</th>
            <th data-field="date_inventaires">Date</th>
            <th data-field="libelle_inventaire">P&eacute;riode</th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
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
                        <i class="fa fa-calendar-plus-o fa-2x"></i>
                        Gestion des inventaires
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idInventaireModifier" name="idInventaireModifier" ng-hide="true" ng-model="inventaire.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>P&eacute;riode de l'inventaire *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="inventaire.libelle_inventaire" id="libelle_inventaire" name="libelle_inventaire" placeholder="Inventaire du 01-05-2020 au 25-06-2020" value="Inventaire du {{ date('d-m-Y à H:i:s') }} au {{ date('d-m-Y à H:i:s') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_id" id="depot_id" ng-model="inventaire.depot_id" ng-init="inventaire.depot_id=''" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le d&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date de l'inventaire *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="inventaire.date_inventaires" id="date_inventaire" name="date_inventaire" value="<?= date('d-m-Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Code barre</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-edit"></i>
                                        </div>
                                        <input type="text" id="code_barre" class="form-control" placeholder="Recherche">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Article</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-list"></i>
                                        </div>
                                        <select id="article" class="form-control">
                                            <option value="">-- Article --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date de p&eacute;remption</label>
                                    <input type="text" class="form-control" id="date_peremption" placeholder="Date de péremption">
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
                                    <label>Qt&eacute; d&eacute;nombr&eacute;e *</label>
                                    <input type="number" min="1" class="form-control" id="quantite_denombree" placeholder="Qté / Btle dénombrée">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-sm  add-row"><i class="fa fa-plus">Ajouter</i></button>
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
                                            <th data-field="date_peremption">Date de p&eacute;remption</th>
                                            <th data-field="quantite_en_stock">En stock</th>
                                            <th data-field="quantite_denombree">Qt&eacute; d&eacute;nombr&eacute;e</th>
                                            <th data-field="ecart">Ecart</th>
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
                                     <th data-field="date_peremptions">Date de p&eacute;remption</th>
                                    <th data-field="quantite_en_stocke" data-align="center">Qt&eacute; en stock </th>
                                    <th data-field="quantite_denombree" data-align="center">Qt&eacute; d&eacute;nombr&eacute;e</th>
                                    <th data-formatter="ecartModFormatter" data-align="center">Ecart</th>
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
                    <input type="text" class="hidden" id="depot_add"  name="depot_id"/>
                    <input type="text" class="hidden" id="inventaire"  name="inventaire_id"/>
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
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Date de p&eacute;remption</label>
                               <input type="text" class="form-control" name="date_peremption" ng-model="article.date_peremptions" id="date_peremption_add" placeholder="Date de péremption">
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>En Stock</label>
                               <input type="number" class="form-control" name="quantite_en_stocke" ng-model="article.quantite_en_stocke" id="en_stock_add" placeholder="Qté / Btle en stock" readonly>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Qt&eacute; d&eacute;nombr&eacute;e *</label>
                               <input type="number" min="0" name="quantite_denombree" ng-model="article.quantite_denombree" class="form-control" placeholder="Qté / Btle dénombrée" required>
                           </div>
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
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
                    <input type="text" class="hidden" id="idInventaireSupprimer"  ng-model="inventaire.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'inventaire du d&eacute;p&ocirc;t <b>@{{inventaire.depot.libelle_depot}}</b><br/> le <b>@{{inventaire.date_inventaires}}</b></div>
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
<!-- Liste details inventaire -->
<div class="modal fade bs-modal-liste-detail-inventaire" id="listeDetailInventaire" ng-controller="listeDetailInventaireCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Inventaire <b>@{{inventaire.libelle_inventaire}}</b> du d&eacute;p&ocirc;t <b>@{{inventaire.depot.libelle_depot}}</b> effectu&eacute; le <b>@{{inventaire.date_inventaires}}</b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListeDetailInventaire" class="table table-success table-striped box box-success"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false">
                    <thead>
                        <tr>
                            <th data-field="article.code_barre">Code barre  </th>
                            <th data-field="article.libelle_article">Article  </th>
                            <th data-field="date_peremptions">Date de p&eacute;remption</th>
                            <th data-field="quantite_en_stocke">Quantit&eacute; en stock </th>
                            <th data-field="quantite_denombree">Qt&eacute; d&eacute;nombr&eacute;e</th>
                            <th data-formatter="ecartFormatter">Ecart</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
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
<script type="text/javascript">
    var ajout = true;
    var ajoutArticle = false;
    var $table = jQuery("#table"), rows = [],$tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle"), $tableListeDetailInventaire = jQuery("#tableListeDetailInventaire");
    var lotArticle = [];
    var idTablle =  0;

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (inventaire) {
            $scope.inventaire = inventaire;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.inventaire = {};
        };
    });
    appSmarty.controller('listeDetailInventaireCtrl', function ($scope) {
        $scope.populateDetailInventaireForm = function (inventaire) {
            $scope.inventaire = inventaire;
        };
    });
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (inventaire) {
            $scope.inventaire = inventaire;
        };
        $scope.initForm = function () {
            $scope.inventaire = {};
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
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
        });
        $("#depot_id").prop( "disabled", false);
        $("#article, #article_add").select2({width: '100%', allowClear: true});

        $('#searchByDate,#date_inventaire').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $('#date_peremption, #date_peremption_add').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            minDate : new Date()
        });

        $("#searchByDate").change(function (e) {
            $("#searchByDepot").val(0);
            var date = $("#searchByDate").val();
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-inventaires'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-inventaires-by-date/' + date});
            }
        });
        $("#searchByDepot").change(function (e) {
            $("#searchByDate").val("");
            var depot = $("#searchByDepot").val();
            if(depot == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-inventaires'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-inventaires-by-depot/' + depot});
            }
        });

        $("#div_enregistrement").show();
        $("#div_update").hide();
        $(".delete-row").hide();

        $('#btnModalAjout').click(function(){
            $("#depot_id").prop( "disabled", false);
            $("#article").select2("val", "");
            $('#code_barre').val('');
            $('#quantite_denombree').val('');
            $('#en_stock').val('');
            $('#article').html("<option value=''>-- Article --</option>");
            $tableAddRowArticle.bootstrapTable('removeAll');
            lotArticle = [];
            idTablle = 0;
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $(".delete-row").hide();
        });

        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var inventaire = $("#idInventaireModifier").val();
            document.forms["formAjoutArticle"].reset();
            $("#inventaire").val(inventaire);
            $('#code_barre_add').val("");
            var depot = $("#depot_id").val();
            $("#depot_add").val(depot);
            $("#article_add").select2("val", "");
            $.getJSON("../stock/liste-article-by-depot/" + depot, function (reponse) {
                $('#article_add').html("<option value=''>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, data) {
                    $('#article_add').append('<option value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                });
            })
            $(".bs-modal-add-article").modal("show");
        });

        $('#depot_id').change(function(){
            var depot = $("#depot_id").val();
            $("#article").select2("val", "");
            $('#article').html("<option value=''>-- Article --</option>");
            if(depot!=""){
                $('#quantite_denombree').val('');
                $('#en_stock').val('');
                $("#code_barre").val("");
                $.getJSON("../stock/liste-article-by-depot/" + depot , function (reponse) {
                        $('#article').html('<option value="">-- Article --</option>');
                        $.each(reponse.rows, function (index, data) {
                            $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                        });
                });
            }else{
                $("#code_barre").val("");
                $("#article").select2("val", "");
                $('#article').html("<option value=''>-- Article --</option>");
            }
        });

        $('#code_barre').keyup(function(e){
            if($("#depot_id").val()==""){
                alert('Selctionner un dépôt SVP!');
                $('#code_barre').val("");
                return;
            }
            if(e.which == '10' || e.which == '13') {
            var code_barre = $('#code_barre').val();
            var depot = $("#depot_id").val();
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
            var depot = $("#depot_id").val();
            $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot + "/" + code_barre , function (reponse) {
                $.each(reponse.rows, function (index, retour) {
                    $('#article_add').append('<option selected data-libellearticle= "' + retour.article.libelle_article + '" value=' + retour.article.id + '>' + retour.article.libelle_article + '</option>')
                    $("#article_add").select2("val",  retour.article.id);
                    $("#en_stock_add").val(retour.quantite_disponible);
                });
            })
        });

        $('#article').change(function(){
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();
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
            var depot_id = $("#depot_id").val();
            $('#code_barre_add').val("");
            $.getJSON("../stock/liste-article-by-article-depot/" + article_id + "/" + depot_id, function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre_add").val(articles_trouver.article.code_barre);
                    $("#en_stock_add").val(articles_trouver.quantite_disponible);
                });
            })
        });

         //Add row on table
        $(".add-row").click(function () {
            if($("#article").val() != '' && $("#quantite_denombree").val() != '') {
                var code_barre = $("#code_barre").val();
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var articleId = $("#article").val();
                var quantite_denombree = $("#quantite_denombree").val();
                var quantite_en_stock = $("#en_stock").val();
                var date_peremption = $("#date_peremption").val();

                    //Vérification Si la ligne existe déja dans le tableau
                    var articleTrouver = _.findWhere(lotArticle, {articles: articleId})
                    if(articleTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        idElementLigne = articleTrouver.id;

                            //MAJ de la ligne
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite_denombree:quantite_denombree,
                                    ecart : quantite_en_stock - quantite_denombree,
                                }
                            });
                            articleTrouver.quantite_denombrees = quantite_denombree;
                            $("#quantite_denombree").val("");
                            $("#en_stock").val("");
                            $("#code_barre").val("");
                            $("#article").select2("val", "");
                            $("#date_peremption").val("");
                            var depot_id = $('#depot_id').val();
                            $.getJSON("../stock/liste-article-by-depot/" + depot_id, function (reponse) {
                                $('#article').html("<option value=''>-- Selectionner l'article --</option>");
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
                          quantite_denombree: quantite_denombree,
                          quantite_en_stock: quantite_en_stock,
                          article: articleId,
                          date_peremption:date_peremption,
                          ecart : quantite_en_stock- quantite_denombree,
                        }
                    })

                    //Creation de l'article dans le tableau virtuel (lot de transfert)
                    var DataArticle = {'id':idTablle, 'articles':articleId,'quantite_en_stocks':quantite_en_stock,'quantite_denombrees':quantite_denombree,'date_peremptions':date_peremption};
                    lotArticle.push(DataArticle);
                    $("#quantite_denombree").val("");
                    $("#en_stock").val("");
                    $("#code_barre").val("");
                    $("#article").select2("val", "");
                    $("#date_peremption").val("");
                    var depot_id = $('#depot_id').val();
                    $.getJSON("../stock/liste-article-by-depot/" + depot_id, function (reponse) {
                        $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                            $.each(reponse.rows, function (index, data) {
                            $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                        });
                    })
                    if(idTablle>0){
                        $(".delete-row").show();
                    }else{
                        $(".delete-row").hide();
                    }

            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs article, colis et quantité dénombrée ne doivent pas être vides.",
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
                    var articleTrouver = _.findWhere(lotArticle, {id: value.id})
                    lotArticle = _.reject(lotArticle, function (article) {
                        return article.id == value.id;
                    });
                });

                if(lotArticle.length==0){
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
                var formData = new FormData($(this)[0]);
                createFormData(formData, 'lotArticle', lotArticle);
                var url = "{{route('stock.inventaires.store')}}";
             }else{
                var methode = 'POST';
                var url = "{{route('stock.update-inventaire')}}";
                var formData = new FormData($(this)[0]);
             }
            editerInventaireAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
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
                var url = "{{route('stock.detail-inventaires.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'detail-inventaires/' + id;
             }
            editerArticleInventaireAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle, ajoutArticle);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idInventaireSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('inventaires/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('detail-inventaires/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle);
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
    function updateRow(idInventaire){
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var inventaire =_.findWhere(rows, {id: idInventaire});
        $("#idInventaireModifier").val(inventaire.id);
        $tableArticle.bootstrapTable('refreshOptions', {url: "../stock/liste-details-inventaire/" + idInventaire});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $scope.$apply(function () {
            $scope.populateForm(inventaire);
        });
        $("#depot_id").prop( "disabled", true);
        $(".bs-modal-ajout").modal("show");
    }
    function updateArticleRow(idArticle){
        ajoutArticle = false;
        var $scope = angular.element($("#formAjoutArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
        var inventaire = $("#idInventaireModifier").val();
        $("#inventaire").val(inventaire);
        var depot = $("#depot_id").val();
        $("#depot_add").val(depot);
        $('#code_barre_add').val("");
        $.getJSON("../stock/liste-article-by-depot/" + depot, function (reponse) {
            $('#article_add').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, articles_trouver) {
                $('#article_add').append('<option value=' + articles_trouver.article.id + '>' + articles_trouver.article.libelle_article + '</option>')
            });
            $("#article_add").select2("val", article.article_id);
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


        $scope.$apply(function () {
            $scope.populateArticleForm(article);
        });
       $(".bs-modal-add-article").modal("show");
    }
    function deleteRow(idInventaire) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var inventaire =_.findWhere(rows, {id: idInventaire});
           $scope.$apply(function () {
              $scope.populateForm(inventaire);
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
    function detailInventaireRow(idInventaire){
        var $scope = angular.element($("#listeDetailInventaire")).scope();
        var inventaire =_.findWhere(rows, {id: idInventaire});
        $scope.$apply(function () {
            $scope.populateDetailInventaireForm(inventaire);
        });
        $tableListeDetailInventaire.bootstrapTable('refreshOptions', {url: "../stock/liste-details-inventaire/" + idInventaire});
        $(".bs-modal-liste-detail-inventaire").modal("show");
    }

    function printRow(idInventaire){
        window.open("../stock/fiche-inventaire-pdf/" + idInventaire,'_blank');
    }

    function ecartFormatter(id,row){
         var ecart = row.quantite_en_stocke-row.quantite_denombree;
        return '<span class="text-bold">' + $.number(ecart)+ '</span>';
    }
    function ecartModFormatter(id, row){
        var ecart = row.quantite_en_stocke-row.quantite_denombree;
        return '<span class="text-bold">' + $.number(ecart)+ '</span>';
    }
    function ficheInventaireFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
    }
    function optionFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Détails inventaire" onClick="javascript:detailInventaireRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
                    <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionAArticleFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function editerInventaireAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#depot_id").val("");
                    $('#code_barre, #en_stock').val("");
                    $("#div_enregistrement").show();
                    $("#div_update").hide();
                    $(".delete-row").hide();
                    $tableAddRowArticle.bootstrapTable('removeAll');
                    lotArticle = [];
                    idTablle =  0;
                    $("#depot_id").prop( "disabled", false);
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

function editerArticleInventaireAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajoutArticle = true) {
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
