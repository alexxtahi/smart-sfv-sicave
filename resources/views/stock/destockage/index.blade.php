@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
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
               data-url="{{url('stock',['action'=>'liste-destockages'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="id" data-formatter="printFormatter" data-width="60px" data-align="center"><i class="fa fa-print"></i></th>
            <th data-field="date_destockages">Date</th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="motif">Motif</th>
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
                        <i class="fa fa-download fa-2x"></i>
                        Gestion des d&eacute;stockages
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idDestockageModifier" name="idDestockageModifier" ng-hide="true" ng-model="destockage.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date du d&eacute;stockage *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="destockage.date_destockages" id="date_destockage" name="date_destockage" value="<?= date('d-m-Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_id" id="depot_id" ng-model="destockage.depot_id" class="form-control select2" required>
                                        <option value="" ng-show="false">-- Selectionner le d&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Motif *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <select class="form-control" ng-model="destockage.motif" id="motif" name="motif" required>
                                        <option value="">-- Sélectionnez un motif de sortie --</option>
                                        <option value="Rectification du stock">Rectification du stock</option>
                                        <option value="Article périmé">Article périmé</option>
                                        <option value="Divers">Divers</option>
                                    </select>
                                    <!--<input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="destockage.motif" id="motif" name="motif" placeholder="Motif du déstockage" required>-->
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
                                        <option value="">-- Selcetionner l'article --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>En Stock</label>
                                    <input type="number" class="form-control" id="en_stock" placeholder="Qté / Btle" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; d&eacute;stocker *</label>
                                    <input type="number" min="1" class="form-control" id="quantite" placeholder="Qté / Btle">
                                </div>
                            </div>
                            <div class="col-md-1">
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
                                            <th data-field="id">Id</th>
                                            <th data-field="code_barre">Code barre</th>
                                            <th data-field="libelle_article">Article</th>
                                            <th data-field="quantite">Qt&eacute; / Btle</th>
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
                                    <th data-field="quantite_destocker" data-align="center">Quantit&eacute; </th>
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
                   <input type="text" class="hidden" id="depot1"  name="depot_id"/>
                    <input type="text" class="hidden" id="destockage"  name="destockage_id"/>
                   <div class="row">
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Code Barre</label>
                               <input type="text" class="form-control" id="code_barre_add">
                           </div>
                       </div>
                       <div class="col-md-5">
                           <div class="form-group">
                               <label>Article *</label>
                               <select name="article_id" class="form-control" id="article_add" required ng-model="article.article_id">
                                   <option value="">-- Selcetionner l'article --</option>
                               </select>
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
                               <label>Qt&eacute; &agrave; d&eacute;stocker*</label>
                               <input type="number" min="0" name="quantite" ng-model="article.quantite_destocker" class="form-control" id="quantite_add" placeholder="Qté / Btle à déstocker" required>
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
                    <input type="text" class="hidden" id="idDestockageSupprimer"  ng-model="destockage.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le d&eacute;stockage du <br/><b>@{{destockage.date_destockages}}</b> de motif <b>@{{destockage.motif}}</b></div>
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

<!-- Modal lot Article à déstocker-->
<div class="modal fade bs-modal-lot-destockage" id="lotDestockageForm" ng-controller="lotDestockageFormCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Liste des articles du d&eacute;stockage N° <b>@{{destockage.id}}</b> du <b>@{{destockage.depot.libelle_depot}}</b> le <b>@{{destockage.date_destockages}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="lotDestockageArticle" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.code_barre">Code</th>
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-field="quantite_destocker" data-align="center">Quantit&eacute; </th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<script type="text/javascript">
    var ajout = true;
    var ajoutArticle = false;
    var $table = jQuery("#table"), rows = [], $lotDestockageArticle = jQuery("#lotDestockageArticle"), $tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle");
    var lotDestockage = [];
    var idTablle =  0;

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (destockage) {
            $scope.destockage = destockage;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.destockage = {};
        };
    });
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (destockage) {
            $scope.destockage = destockage;
        };
        $scope.initForm = function () {
            $scope.destockage = {};
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
    appSmarty.controller('lotDestockageFormCtrl', function ($scope) {
        $scope.populateFormLotDestockage = function (destockage) {
        $scope.destockage = destockage;
        };
    });

    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
        });
        $("#depot_id, #article, #article_add").select2({width: '100%', allowClear: true});

        $('#date_destockage, #searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $("#depot_id").prop( "disabled", false);
        $("#searchByDate").change(function (e) {
            $("#searchByDepot").val(0);
            var date = $("#searchByDate").val();
            if(date == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-destockages'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-destockages-by-date/' + date});
            }
        });
        $("#searchByDepot").change(function (e) {
            $("#searchByDate").val("");
            var depot = $("#searchByDepot").val();
            if(depot== 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-destockages'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-destockages-by-depot/' + depot});
            }
        });

        $("#div_enregistrement").show();
        $("#div_update").hide();
        $(".delete-row").hide();
        $("#btnModalAjout").on("click", function () {
           $("#depot_id").prop( "disabled", false);
           $('#article').html("<option value=''>-- Selectionner l'article --</option>");
           $("#depot_id").select2("val", "");
           $("#article").select2("val", "");
           $('#code_barre').val('');
           $tableAddRowArticle.bootstrapTable('removeAll');
            lotTransfert = [];
            idTablle = 0;
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $(".delete-row").hide();
        });

        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var destockage = $("#idDestockageModifier").val();
            document.forms["formAjoutArticle"].reset();
            $("#destockage").val(destockage);
            $('#code_barre_add').val("");
            var depot = $("#depot_id").val();
            $("#depot1").val(depot);
            $("#article_add").select2("val", "");
            var depot_id = $('#depot_id').val();
            $.getJSON("../stock/liste-article-by-depot/" + depot_id, function (reponse) {
                $('#article_add').html("<option>-- Selectionner l'article --</option>");
                    $.each(reponse.rows, function (index, data) {
                    $('#article_add').append('<option value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                });
            })
            $(".bs-modal-add-article").modal("show");
        });

        $('#depot_id').change(function(){
            var depot_id = $('#depot_id').val();
            $('#code_barre').val("");
            $.getJSON("../stock/liste-article-by-depot/" + depot_id, function (reponse) {
                $('#article').html("<option value=''>-- Selectionner l'article --</option>");
                    $.each(reponse.rows, function (index, data) {
                    $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                });
            })
        });

        $('#code_barre').keyup(function(e){
            if($("#depot_id").val()==""){
                $('#code_barre').val("");
                alert('Selctionner un dépôt SVP!');
                return;
            }
            if(e.which == '10' || e.which == '13') {
            var code_barre = $('#code_barre').val();
            var depot = $("#depot_id").val();
            $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot + "/" + code_barre, function (reponse) {
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
            $.getJSON("../stock/liste-article-by-depot-code-barre/" + depot + "/" + code_barre, function (reponse) {
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
            if($("#article").val() != '' && $("#quantite").val() != '' && $("#quantite").val()!=0) {
                var code_barre = $("#code_barre").val();
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var articleId = $("#article").val();
                var quantite = $("#quantite").val();
                var stock = $("#en_stock").val();
                if(parseInt(quantite) > parseInt(stock)){
                    $.gritter.add({
                        title: "SMART-SFV",
                        text: "La quantité à déstocker ne doit pas depasser la quantité disponible en stock",
                        sticky: false,
                        image: basePath + "/assets/img/gritter/confirm.png",
                    });
                    $("#quantite").val("");
                    return;
                }else{
                    //Vérification Si la ligne existe déja dans le tableau
                    var articleTrouver = _.findWhere(lotDestockage, {articles: articleId})
                    if(articleTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = articleTrouver.quantites;
                        idElementLigne = articleTrouver.id;

                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);
                        if(parseInt(sommeDeuxQtes)> parseInt(stock)){
                            $.gritter.add({
                                title: "SMART-SFV",
                                text: "Cet article existe dans votre lot de transfert, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle disponible en stock",
                                sticky: false,
                                image: basePath + "/assets/img/gritter/confirm.png",
                            });
                            $("#quantite").val("");
                            return;
                        }else{
                            //MAJ de la ligne
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite : sommeDeuxQtes,
                                }
                            });
                            articleTrouver.quantites = sommeDeuxQtes;
                            $("#quantite").val("");
                            $("#en_stock").val("");
                            $("#code_barre").val("");
                             $("#article").select2("val", "");
                            var depot_depart_id = $('#depot_id').val();
                            $.getJSON("../stock/liste-article-by-depot/" + depot_depart_id, function (reponse) {
                                $('#article').html("<option>-- Selectionner l'article --</option>");
                                    $.each(reponse.rows, function (index, data) {
                                    $('#article').append('<option data-libellearticle= "' + data.article.libelle_article + '" value=' + data.article.id + '>' + data.article.libelle_article + '</option>')
                                });
                            })
                            return;
                        }
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
                        }
                    })

                    //Creation de l'article dans le tableau virtuel (lot de transfert)
                    var DataArticle = {'id':idTablle, 'articles':articleId,'quantites':quantite};
                    lotDestockage.push(DataArticle);
                    $("#quantite").val("");
                    $("#en_stock").val("");
                    $("#code_barre").val("");
                    $("#article").select2("val", "");
                    var depot_depart_id = $('#depot_id').val();
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
                    var articleTrouver = _.findWhere(lotDestockage, {id: value.id})
                    lotDestockage = _.reject(lotDestockage, function (article) {
                        return article.id == value.id;
                    });
                });

                if(lotDestockage.length==0){
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
                createFormData(formData, 'lotDestockage', lotDestockage);
                var url = "{{route('stock.destockages.store')}}";
             }else{
               var methode = 'POST';
                var url = "{{route('stock.update-destockage')}}";
                var formData = new FormData($(this)[0]);
             }
            editerDestockageAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
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
                var url = "{{route('stock.article-destockers.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'article-destockers/' + id;
             }
            editerArticleDestockersAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle, ajoutArticle);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idDestockageSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('destockages/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('article-destockers/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle);
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
    function updateRow(idDestockage) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var destockage =_.findWhere(rows, {id: idDestockage});
         $scope.$apply(function () {
            $scope.populateForm(destockage);
        });
        $("#depot_id").prop( "disabled", true);
        $("#depot_id").select2("val", destockage.depot_id);
        $("#idDestockageModifier").val(destockage.id);
        $tableArticle.bootstrapTable('refreshOptions', {url: "../stock/liste-article-destockers/" + idDestockage});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $(".bs-modal-ajout").modal("show");
    }
    function deleteRow(idDestockage) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var destockage =_.findWhere(rows, {id: idDestockage});
           $scope.$apply(function () {
              $scope.populateForm(destockage);
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

        var destockage = $("#idDestockageModifier").val();
        $("#destockage").val(destockage);
        var depot = $("#depot_id").val();
        $("#depot1").val(depot);
        $.getJSON("../stock/liste-article-by-depot/" + depot, function (reponse) {
            $('#article_add').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, articles_trouver) {
                $('#article_add').append('<option value=' + articles_trouver.article.id + '>' + articles_trouver.article.libelle_article + '</option>')
            });
            $("#article_add").select2("val", article.article_id);

        })
        $('#code_barre_add').val("");
         $.getJSON("../stock/liste-article-by-article-depot" + article.article_id + "/" + depot_id, function (reponse) {
            $.each(reponse.rows, function (index, articles_trouver) {
                $("#code_barre_add").val(articles_trouver.article.code_barre);
                $("#en_stock_add").val(articles_trouver.quantite_disponible);
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
    function printRow(idDestockage){
        window.open("destockage-pdf/" + idDestockage ,'_blank')
    }
    function listeArticleRow(idDestockage){
        var $scope = angular.element($("#lotDestockageForm")).scope();
        var destockage =_.findWhere(rows, {id: idDestockage});
         $scope.$apply(function () {
            $scope.populateFormLotDestockage(destockage);
        });
        $lotDestockageArticle.bootstrapTable('refreshOptions', {url: "../stock/liste-article-destockers/" + idDestockage});
        $(".bs-modal-lot-destockage").modal("show");
    }
    function printFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Articles déstockés" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
               <button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionAArticleFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function editerDestockageAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#depot_id").select2("val", "");
                    $('#code_barre, #en_stock').val("");
                    $("#div_enregistrement").show();
                    $("#div_update").hide();
                    $(".delete-row").hide();
                    $tableAddRowArticle.bootstrapTable('removeAll');
                    lotDestockage = [];
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
    function editerArticleDestockersAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajoutArticle = true) {
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
