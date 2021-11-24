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
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-5">
    <select class="form-control" id="searchByVente">
        <option value="0">-- Tous les retours --</option>
        @foreach($retours as $vente)
            @if($vente->numero_ticket!=null)
                <option value="{{$vente->id}}"> {{$vente->numero_ticket}}</option>
            @else
                <option value="{{$vente->id}}"> {{'FACT '.$vente->numero_facture}}</option>
            @endif
        @endforeach
    </select>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-retour-articles'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="ficheFormatter" data-width="60px">Fiche</th>
            <th data-field="date_retours">Date</th>
            <th data-formatter="ticketFormatter">N° Ticket</th>
            <th data-field="libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant total </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
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
                        <i class="fa fa-mail-forward fa-2x"></i>
                        Gestion des retours d'articles
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idRetourArticleModifier" name="idRetourArticleModifier" ng-hide="true" ng-model="retourArticle.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date de retour *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="retourArticle.date_retours" id="date_retour" name="date_retour" value="<?= date('d-m-Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-2">
                            <div class="form-group">
                                <label>Date d'achat</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" id="date_achat" placeholder="23-02-2021" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>N° du ticket ou de la facture *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="vente_id" id="vente_id" class="form-control" required>
                                        <option value="">-- Selectionner --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <input type="text"  class="form-control" id="libelle_depot" readonly>
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
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Article *</label>
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
                                    <input type="hidden" class="form-control" id="unite">
                                    <input type="hidden" id="unite_value">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Prix </label>
                                    <input type="text" class="form-control" id="prix" placeholder="Prix" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Qt&eacute; / Btle</label>
                                    <input type="text" class="form-control" id="qte_vendu" placeholder="Qté / Btle" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Retour *</label>
                                    <input type="number" min="1" class="form-control" id="qte_retour" placeholder="Qté / Btle">
                                </div>
                            </div>
                            <div class="col-md-1">
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
                                            <th data-field="libelle_unite">Colis</th>
                                            <th data-field="prix">Prix</th>
                                            <th data-field="quantite_vendue">Qt&eacute; vendue</th>
                                            <th data-field="quantite_retournee">Qt&eacute; retourn&eacute;e</th>
                                            <th data-field="montant">Montant</th>
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
                                    <th data-field="article.description_article">Article</th>
                                    <th data-field="unite.libelle_unite">Colis</th>
                                    <th data-field="prix_unitaire">Prix</th>
                                    <th data-field="quantite_vendue">Qt&eacute; vendue</th>
                                    <th data-field="quantite" data-align="center">Qt&eacute; retourn&eacute;e </th>
                                    <th data-formatter="montantRetourFormatter" data-align="center">Montant</th>
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
                    <input type="text" class="hidden" id="retour_article_id"  name="retour_article_id"/>
                    <input type="text" class="hidden" id="vente_add"  name="vente_id"/>
                   <div class="row">
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Code Barre</label>
                               <input type="text" class="form-control" id="code_barre_add">
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
                       <input type="hidden" id="unite_value_add" name="unite_id">
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Prix *</label>
                               <input type="text" class="form-control" name="prix" ng-model="article.prix_unitaire" id="prix_add" readonly>
                           </div>
                       </div>
                       <div class="col-md-1">
                           <div class="form-group">
                               <label>Qt&eacute; / Btle</label>
                               <input type="text" class="form-control" id="qte_vendu_add" name="quantite_vendue" ng-model="article.quantite_vendue" placeholder="Qté / Btle" readonly>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Retour *</label>
                               <input type="number" min="1" name="quantite" class="form-control" ng-model="article.quantite" id="qte_retour_add" placeholder="Qté / Btle retorunée" required>
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
                    <input type="text" class="hidden" id="idRetourArticleSupprimer"  ng-model="retourArticle.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le retour d'article du <b>@{{retourArticle.date_retours}}</b><br/> concernant <b>@{{retourArticle.numero_ticket!=null ? 'le ticket' + retourArticle.numero_ticket: 'la facture' + retourArticle.numero_facture}}</b></div>
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

<!-- Liste details retour -->
<div class="modal fade bs-modal-liste-detail-retour" id="listeDetailRetour" ng-controller="listeDetailRetourCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Retour d'articles du <b>@{{retourArticle.date_retours}}</b> concernant <b>@{{retourArticle.numero_ticket!=null ? 'le ticket' + retourArticle.numero_ticket: 'la facture' + retourArticle.numero_facture}}</b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListeDetailRetour" class="table table-success table-striped box box-success"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false">
                    <thead>
                        <tr>
                            <th data-field="article.code_barre">Code barre  </th>
                            <th data-field="article.description_article">Article  </th>
                            <th data-field="unite.libelle_unite">Colis</th>
                            <th data-field="prix_unitaire">Prix</th>
                            <th data-field="quantite_vendue">Qt&eacute; vendue</th>
                            <th data-field="quantite">Q&eacute; retourn&eacute;e </th>
                            <th data-formatter="montantRetourFormatter">Montant</th>
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

<script type="text/javascript">
    var ajout = true;
    var ajoutArticle = false;
    var $table = jQuery("#table"), rows = [],$tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle"), $tableListeDetailRetour = jQuery("#tableListeDetailRetour");
    var lotArticle = [];
    var idTablle =  0;

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (retourArticle) {
            $scope.retourArticle = retourArticle;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.retourArticle = {};
        };
    });
    appSmarty.controller('listeDetailRetourCtrl', function ($scope) {
        $scope.populateDetailRetourForm = function (retourArticle) {
            $scope.retourArticle = retourArticle;
        };
    });
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (retourArticle) {
            $scope.retourArticle = retourArticle;
        };
        $scope.initForm = function () {
            $scope.retourArticle = {};
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
        $("#article, #article_add, #vente_id, #searchByVente").select2({width: '100%', allowClear: true});

        $('#searchByDate,#date_retour, #date_achat').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });

        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-retour-articles'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-date/' + date});
            }
        });
        $("#searchByVente").change(function (e) {
            var vente = $("#searchByVente").val();
            if(vente== 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-retour-articles'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-vente/' + vente});
            }
        });

        $("#div_enregistrement").show();
        $("#div_update").hide();
        $(".delete-row").hide();

        $('#btnModalAjout').click(function(){
            $("#article").select2("val", "");
            $("#vente_id").val('').trigger('change');
            $('#code_barre').val('');
            $('#unite').val('');
            $('#unite_value').val('');
            $('#qte_retour').val('');
            $('#qte_vendu').val('');
            $('#prix').val('');
            $('#libelle_depot').val('');
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
            var retourArticle = $("#idRetourArticleModifier").val();
            document.forms["formAjoutArticle"].reset();
            $("#retour_article_id").val(retourArticle);
            var vente_add = $("#vente_id").val();
            $("#vente_add").val(vente_add);
            $('#code_barre_add').val("");
            var vente = $("#vente_id").val();
            $("#article_add").select2("val", "");
            $.getJSON("../boutique/liste-articles-vente/" + vente , function (reponse) {
                $('#article_add').html('<option value="">-- Article --</option>');
                $.each(reponse.rows, function (index, article_vente) {
                    $('#article_add').append('<option data-libellearticle= "' + article_vente.article.description_article + '"  value=' + article_vente.article.id + '>' + article_vente.article.description_article + ' --- ' + article_vente.unite.libelle_unite + '</option>')
                });
            });
            $(".bs-modal-add-article").modal("show");
        });

         $("#date_achat").change(function (e) {
            var date_achat = $("#date_achat").val();
            if(date_achat != ''){
                $.getJSON("../boutique/liste-all-ventes-by-date/" + date_achat , function (reponse) {
                        $('#vente_id').html(' <option value="">-- Selectionner --</option>');
                        $.each(reponse.rows, function (index, vente) {
                            if(vente.numero_ticket != null){
                                $('#vente_id').append('<option value=' + vente.id + '>' + vente.numero_ticket +  '</option>')
                            }else{
                               $('#vente_id').append('<option value=' + vente.id + '>' + vente.numero_facture +  '</option>')
                            }
                        });
                });
            }
        });

        $('#vente_id').change(function(){
            var vente = $("#vente_id").val();
            if(vente!=""){
                $tableAddRowArticle.bootstrapTable('removeAll');
                lotArticle = [];
                idTablle = 0;
                $(".delete-row").hide();
                $('#code_barre').val('');
                $('#unite').val('');
                $('#unite_value').val('');
                $('#qte_retour').val('');
                $('#qte_vendu').val('');
                $('#libelle_depot').val('');
                $('#prix').val('');
                $.getJSON("../boutique/liste-articles-vente/" + vente , function (reponse) {
                        $('#article').html('<option value="">-- Article --</option>');
                        $.each(reponse.rows, function (index, article_vente) {
                            $('#article').append('<option data-libellearticle= "' + article_vente.article.description_article + '"  value=' + article_vente.article.id + '>' + article_vente.article.description_article + ' -- ' + article_vente.unite.libelle_unite +  '</option>')
                        });
                });
                $.getJSON("../boutique/find-one-vente/" + vente , function (reponse) {
                    $('#date_achat').val(''); $('#depot').val('');
                    $.each(reponse.rows, function (index, vente_trouver) {
                        $('#date_achat').val(vente_trouver.date_ventes);
                        $('#libelle_depot').val(vente_trouver.depot.libelle_depot);
                    });
                });
            }else{
                $("#article").select2("val", "");
                $('#code_barre').val('');
                $('#unite').val('');
                $('#unite_value').val('');
                $('#qte_retour').val('');
                $('#qte_vendu').val('');
                $('#libelle_depot').val('');
                $('#article').html("<option value=''>-- Article --</option>");
            }
        });

        $('#code_barre').keyup(function(e){
            if($("#vente_id").val()==""){
                alert('Selctionner un numéro de ticket ou de facture  SVP!');
                $('#code_barre').val("");
                return;
            }
            if(e.which == '10' || e.which == '13') {
                var code_barre = $('#code_barre').val();
                var vente = $("#vente_id").val();
                $.getJSON("../boutique/find-article-sur-vente-by-code-barre/" + code_barre + "/" + vente , function (reponse) {
                    $.each(reponse.rows, function (index, retour) {
                        $("#article").select2("val", retour.article_id);
                        $('#unite').val(retour.libelle_unite);
                        $('#unite_value').val(retour.id_unite);
                        $('#qte_vendu').val(retour.quantite);
                        $('#prix').val(retour.prix);
                    });
                })
                e.preventDefault();
                e.stopPropagation();
            }
        });

        $('#code_barre_add').keyup(function(e){
                var code_barre = $('#code_barre_add').val();
                var vente = $("#vente_id").val();
                $.getJSON("../boutique/find-article-sur-vente-by-code-barre/" + code_barre + "/" + vente , function (reponse) {
                    $.each(reponse.rows, function (index, retour) {
                        $("#article_add").select2("val", retour.article_id);
                        $('#unite_value_add').val(retour.id_unite);
                        $('#qte_vendu_add').val(retour.quantite);
                        $('#prix_add').val(retour.prix);
                    });
                })

        });

        $('#article').change(function(){
            var article_id = $("#article").val();
            var vente = $("#vente_id").val();
            $('#code_barre').val("");
            $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/find-one-article-on-vente/" + vente + "/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, retour) {
                        $('#unite').val(retour.libelle_unite);
                        $('#unite_value').val(retour.id_unite);
                        $('#qte_vendu').val(retour.quantite);
                        $('#prix').val(retour.prix);
                });
            })
        });

        $('#article_add').change(function(){
            var article_id = $("#article_add").val();
            var vente = $("#vente_id").val();
            $('#code_barre_add').val("");
            $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) {
                    $("#code_barre_add").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/find-one-article-on-vente/" + vente + "/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, retour) {
                        $('#unite_value_add').val(retour.id_unite);
                        $('#qte_vendu_add').val(retour.quantite);
                        $('#prix_add').val(retour.prix);
                });
            })
        });

         //Add row on table
        $(".add-row").click(function () {
            if($("#article").val() != '' && $("#qte_retour").val() != '' && $("#qte_retour").val()>0) {
                var code_barre = $("#code_barre").val();
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var libelle_unite = $("#unite").val();
                var articleId = $("#article").val();
                var uniteId = $("#unite_value").val();
                var qte_retour = $("#qte_retour").val();
                var qte_vendue = $("#qte_vendu").val();
                var prix = $("#prix").val();
                if(parseInt(qte_retour) > parseInt(qte_vendue)){
                    $.gritter.add({
                        title: "SMART-SFV",
                        text: "La quantité à retourner ne doit pas depasser la quantité vendue",
                        sticky: false,
                        image: basePath + "/assets/img/gritter/confirm.png",
                    });
                    $("#qte_retour").val("");
                    return;
                }
                //Vérification Si la ligne existe déja dans le tableau
                var articleTrouver = _.findWhere(lotArticle, {articles: articleId, unites:uniteId})
                if(articleTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        idElementLigne = articleTrouver.id;
                        oldQte = articleTrouver.quantites;
                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(qte_retour);
                        if(parseInt(sommeDeuxQtes)> parseInt(qte_vendue)){
                            $.gritter.add({
                                title: "SMART-SFV",
                                text: "Cet article existe dans votre lot, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle vendue",
                                sticky: false,
                                image: basePath + "/assets/img/gritter/confirm.png",
                            });
                            $("#qte_retour").val("");
                            return;
                        }else{
                            //MAJ de la ligne
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite_retournee:sommeDeuxQtes,
                                    montant:$.number(sommeDeuxQtes*prix),
                                }
                            });
                            articleTrouver.quantite_retournee = sommeDeuxQtes;
                            $('#unite').val("");
                            $('#unite_value').val("");
                            $("#qte_retour").val("");
                            $("#qte_vendu").val("");
                            $("#code_barre").val("");
                            $("#article").select2("val", "");
                            var vente = $('#vente_id').val();
                            $.getJSON("../boutique/liste-articles-vente/" + vente , function (reponse) {
                                $('#article').html('<option value="">-- Article --</option>');
                                $.each(reponse.rows, function (index, article_vente) {
                                    $('#article').append('<option data-libellearticle= "' + article_vente.article.description_article + '"  value=' + article_vente.article.id + '>' + article_vente.article.description_article + '</option>')
                                });
                            });
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
                          libelle_unite: libelle_unite,
                          quantite_retournee:qte_retour,
                          quantite_vendue:qte_vendue,
                          article: articleId,
                          unite: uniteId,
                          prix : prix,
                          montant : $.number(prix*qte_retour),
                        }
                    })

                    //Creation de l'article dans le tableau virtuel (lot de transfert)
                    var DataArticle = {'id':idTablle, 'articles':articleId, 'unites':uniteId,'quantites':qte_retour,'quantite_vendues':qte_vendue,'prix':prix};
                    lotArticle.push(DataArticle);
                    $('#unite').val("");
                    $('#unite_value').val("");
                    $("#qte_retour").val("");
                    $("#qte_vendu").val("");
                    $("#code_barre").val("");
                    $("#article").select2("val", "");
                    var vente = $('#vente_id').val();
                    $.getJSON("../boutique/liste-articles-vente/" + vente , function (reponse) {
                        $('#article').html('<option value="">-- Article --</option>');
                        $.each(reponse.rows, function (index, article_vente) {
                            $('#article').append('<option data-libellearticle= "' + article_vente.article.description_article + '"  value=' + article_vente.article.id + '>' + article_vente.article.description_article + '</option>')
                        });
                    });
                    if(idTablle>0){
                        $(".delete-row").show();
                    }else{
                        $(".delete-row").hide();
                    }
            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs article et quantité retournée ne doivent pas être vides.",
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
                var url = "{{route('boutique.retour-articles.store')}}";
             }else{
                var methode = 'POST';
                var url = "{{route('boutique.update-retour-article')}}";
                var formData = new FormData($(this)[0]);
             }
            editerRetourArticleAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
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
                var url = "{{route('boutique.article-retournes.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'article-retournes/' + id;
             }
            editerArticleRetourneAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle, ajoutArticle);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idRetourArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('retour-articles/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('article-retournes/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle);
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
    function updateRow(idRetourArticle){
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var retourArticle =_.findWhere(rows, {id: idRetourArticle});

        $tableArticle.bootstrapTable('refreshOptions', {url: "../boutique/liste-articles-retournes/" + idRetourArticle});
        $.getJSON("../boutique/find-one-vente/" + retourArticle.vente_id , function (reponse) {
            $.each(reponse.rows, function (index, vente_info) {
                $('#date_achat').val(vente_info.date_ventes);

                $.getJSON("../boutique/liste-all-ventes-by-date/" + vente_info.date_ventes , function (reponse) {
                        $('#vente_id').html(' <option value="">-- Selectionner --</option>');
                        $.each(reponse.rows, function (index, vente) {
                            if(vente.numero_ticket != null){
                                $('#vente_id').append('<option value=' + vente.id + '>' + vente.numero_ticket +  '</option>')
                            }else{
                               $('#vente_id').append('<option value=' + vente.id + '>' + vente.numero_facture +  '</option>')
                            }
                        });
                });

                 $("#vente_id").select2("val", vente_info.id);

            });
        })


        $("#div_enregistrement").hide();
        $("#div_update").show();
        $scope.$apply(function () {
            $scope.populateForm(retourArticle);
        });
        $(".bs-modal-ajout").modal("show");
    }
    function updateArticleRow(idArticle){
        ajoutArticle = false;
        var $scope = angular.element($("#formAjoutArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
        var retourArticle = $("#idRetourArticleModifier").val();
        $("#retour_article_id").val(retourArticle);
        var vente = $("#vente_id").val();
        $("#vente_add").val(vente);
        $('#code_barre_add').val("");
        $.getJSON("../boutique/liste-articles-retournes/" + retourArticle , function (reponse) {
            $('#article_add').html('<option value="">-- Article --</option>');
            $.each(reponse.rows, function (index, article_vente) {
                $('#article_add').append('<option data-libellearticle= "' + article_vente.article.description_article + '"  value=' + article_vente.article.id + '>' + article_vente.article.description_article + ' -- ' + article_vente.unite.libelle_unite + '</option>')
           });
           $("#article_add").select2("val", article.article_id);
        });

        $.getJSON("../parametre/find-article/" + article.article_id , function (reponse) {
            $.each(reponse.rows, function (index, articles_trouver) {
                $("#code_barre_add").val(articles_trouver.code_barre);
            });
        })
          $.getJSON("../boutique/find-one-article-on-vente/" + vente + "/" + article.article_id , function (reponse) {
                $.each(reponse.rows, function (index, retour) {
                        $('#unite').val(retour.libelle_unite);
                        $('#unite_value').val(retour.id_unite);
                       // $('#qte_vendu').val(retour.quantite);
                        $('#prix').val(retour.prix);
                });
            })

        $scope.$apply(function () {
            $scope.populateArticleForm(article);
        });
       $(".bs-modal-add-article").modal("show");
    }
    function deleteRow(idRetourArticle) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var retourArticle =_.findWhere(rows, {id: idRetourArticle});
           $scope.$apply(function () {
              $scope.populateForm(retourArticle);
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
    function articleRetourneRow(idRetourArticle){
        var $scope = angular.element($("#listeDetailRetour")).scope();
        var retourArticle =_.findWhere(rows, {id: idRetourArticle});
        $scope.$apply(function () {
            $scope.populateDetailRetourForm(retourArticle);
        });
        $tableListeDetailRetour.bootstrapTable('refreshOptions', {url: "../boutique/liste-articles-retournes/" + idRetourArticle});
        $(".bs-modal-liste-detail-retour").modal("show");
    }

    function printRow(idRetourArticle){
        window.open("../boutique/fiche-retour-article-pdf/" + idRetourArticle,'_blank');
    }

    function ticketFormatter(id,row){
        return row.numero_facture!=null ? '<span class="text-bold"> FACT' + row.numero_facture+ '</span>' : '<span class="text-bold">' + row.numero_ticket+ '</span>';
    }
    function montantRetourFormatter(id, row){
        return '<span class="text-bold">' + $.number(row.quantite*row.prix_unitaire)+'</span>';
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+'</span>';
    }
    function ficheFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + row.id_ligne + ');"><i class="fa fa-print"></i></button>';
    }
    function optionFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Détails inventaire" onClick="javascript:articleRetourneRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
                    <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionAArticleFormatter(id, row) {
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function editerRetourArticleAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#vente_id").select2("val","");
                    $('#code_barre, #qte_vendu, #qte_retour, #date_achat').val("");
                    $("#div_enregistrement").show();
                    $("#div_update").hide();
                    $(".delete-row").hide();
                    $tableAddRowArticle.bootstrapTable('removeAll');
                    lotArticle = [];
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

function editerArticleRetourneAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajoutArticle = true) {
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
