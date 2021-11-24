@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant' or Auth::user()->role == 'Comptable' or Auth::user()->role == 'Logistic')
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
       <input type="text" class="form-control" id="searchByBonCommande" placeholder="Rechercher par N° de bon">
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-4">
    <select class="form-control" id="searchByFournisseur">
        <option value="0">-- Tous les fournisseurs --</option>
        @foreach($fournisseurs as $fournisseur)
        <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByEtat">
        <option value="0">-- Tous les etats--</option>
        <option value="1">Brouillon</option>
        <option value="2">Enregistr&eacute;</option>
        <option value="3">Refus&eacute;</option>
        <option value="4">Receptionn&eacute;</option>
    </select>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-bon-commandes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="id" data-width="60px" data-formatter="bonFormatter" data-align="center">Bon </th>
            <th data-field="date_bon_commandes">Date </th>
            <th data-field="numero_bon">N° bon de commande </th>
            <th data-field="fournisseur.full_name_fournisseur">Fournisseur </th>
            <th data-field="etat" data-formatter="etatFormatter">Etat </th>
            <th data-field="montantBon" data-formatter="montantFormatter">Montant Total</th>
            <th data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:70%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-file-text fa-2x"></i>
                        Gestion des bons de commande
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idBonCommandeModifier" ng-hide="true" ng-model="bonCommande.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date du bon *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="bonCommande.date_bon_commandes" id="date_bon_commande" name="date_bon_commande" placeholder="Ex: 01-01-1994" value="<?=date('d-m-Y');?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fournisseur *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <select name="fournisseur_id" id="fournisseur_id" class="form-control select2" required>
                                        <option value="" ng-show="false">-- Selectionner le fournisseur --</option>
                                        @foreach($fournisseurs as $fournisseur)
                                        <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Etat du bon </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-align-justify"></i>
                                    </div>
                                    <select name="etat" id="etat" ng-model="bonCommande.etat" ng-init="bonCommande.etat=1" class="form-control select2">
                                        <option value="1">Brouillon</option>
                                        <option value="2">Enregistr&eacute;</option>
                                        <option value="3">Refus&eacute;</option>
                                        <option value="4">Receptionn&eacute;</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-bold text-green">
                                <label>
                                    Liste des articles du bon
                                </label>
                            </h5>
                        </div>
                    </div>
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Code barre </label>
                                    <input type="text" class="form-control" id="code_barre">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Article *</label>
                                    <select class="form-control" id="article">
                                    <option value="" ng-show="false">-- Selectionner l'article --</option>
                                    @foreach($articles as $article)
                                    <option data-libellearticle="{{$article->libelle_article}}" value="{{$article->id}}"> {{$article->libelle_article}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix achat TTC </label>
                                    <input type="text" class="form-control" id="prix_achat_ttc" placeholder="Prix achat TTC" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qté / Btle *</label>
                                    <input type="number" class="form-control" id="quantite" min="0" placeholder="Qté / Btle" value="0">
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
                                    <th>Prix achat TTC</th>
                                    <th>Qt&eacute; / Btle</th>
                                    <th>Montant</th>
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
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix achat TTC</th>
                                    <th data-field="quantite_demande" data-align="center">Qt&eacute; </th>
                                    <th data-formatter="montantTtcFormatter">Montant </th>
                                    <th data-field="id" data-formatter="optionArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
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
                   <input type="text" class="hidden" id="bon_commande_id"  name="bon_commande_id"/>
                    <div class="row">
                        <div class="col-md-2">
                                <div class="form-group">
                                    <label>Code B.</label>
                                    <input type="text" class="form-control" id="code_barre_add">
                                </div>
                            </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Article *</label>
                                <select class="form-control" name="article_id" id="article_add" required>
                                    <option value="" ng-show="false">-- Selectionner l'article --</option>
                                    @foreach($articles as $article)
                                    <option value="{{$article->id}}"> {{$article->libelle_article}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Quantit&eacute; *</label>
                                <input type="number" class="form-control" name="quantite" id="quantite_add" min="0" placeholder="Qté / Btle" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix achat TTC</label>
                                <input type="number" class="form-control" name="prix_article" id="prix_achat_ttc_add" min="0" placeholder="Prix achat TTC" readonly>
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
                    <input type="text" class="hidden" id="idBonCommandeSupprimer"  ng-model="bonCommande.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le bon N° <br/><b>@{{bonCommande.numero_bon}}</b></div>
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
    var ajout = false;
    var ajoutArticle = false;
    var $table = jQuery("#table"), rows = [],$tableArticle = jQuery("#tableArticle"), rowsArticle = [];

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (bonCommande) {
        $scope.bonCommande = bonCommande;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.bonCommande = {};
        };
    });
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (bonCommande) {
        $scope.bonCommande = bonCommande;
        };
        $scope.initForm = function () {
        $scope.bonCommande = {};
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
        $("#fournisseur_id, #article, #article_add, #searchByFournisseur").select2({width: '100%'});
        $("#div_enregistrement").show();
        $("#div_update").hide();
        $('#searchByDate, #date_bon_commande').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $("#searchByBonCommande").keyup(function (e) {
            var numero_bon = $("#searchByBonCommande").val();
            if(numero_bon == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-bon-commandes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../stock/liste-bon-commandes-by-numero-bon/' + numero_bon});
            }
        });
        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-bon-commandes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../stock/liste-bon-commandes-by-date/' + date});
            }
        });

        $("#searchByFournisseur").change(function (e) {
            var fournisseur = $("#searchByFournisseur").val();
            if(fournisseur == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-bon-commandes'])}}"});
            }
            else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-bon-commandes-by-fournisseur/' + fournisseur});
            }
        });
        $("#searchByEtat").change(function (e) {
            var etat = $("#searchByEtat").val();
            if(etat == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-bon-commandes'])}}"});
            }
            else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-bon-commandes-by-etat/' + etat});
            }
        });


        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var bonCommande = $("#idBonCommandeModifier").val();
            document.forms["formAjoutArticle"].reset();
            $("#bon_commande_id").val(bonCommande);
            $("#article_add").select2("val","");
            $(".bs-modal-add-article").modal("show");
        });
        $("#btnModalAjout").on("click", function () {
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $("#article").select2("val", "");
            $("#fournisseur_id").select2("val", "");
        });

        $("#fournisseur_id").change(function (e) {
            var fournisseur = $("#fournisseur_id").val();
            $('#prix_achat_ht').val("");
            $('#prix_achat_ttc').val("");
            $('#quantite').val("");
        });
        $("#code_barre").keyup(function (e) {
           if(e.which == '10' || e.which == '13') {
                var code_barre = $("#code_barre").val();
                $.getJSON("../stock/liste-articles-by-code/" + code_barre, function (reponse) {
                    if(reponse.total>0){
                        $.each(reponse.rows, function (index, article) {
                            $("#article").select2("val",article.id);
                            $('#prix_achat_ttc').val(article.prix_achat_ttc);
                        });
                    }else{
                        $('#article').val("");
                        $('#prix_achat_ttc').val("");
                        $('#quantite').val("");
                    }
                });
                e.preventDefault();
                e.stopPropagation();
            }
        });
        $("#code_barre_add").keyup(function (e) {
            var code_barre = $("#code_barre_add").val();
            $.getJSON("../stock/liste-articles-by-code/" + code_barre, function (reponse) {
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, retour) {
                        $("#article_add").select2("val",retour.id);
                        $('#prix_achat_ttc_add').val(article.prix_achat_ttc);
                    });
                }else{
                    $('#article').val("");
                    $('#quantite_add').val("");
                }
           });
        });

        $("#article").change(function (e) {
            var article_id = $('#article').val();
            $("#code_barre").val("");
            $.getJSON("../stock/get-article-by-id/" + article_id, function (reponse) {
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, article) {
                        $("#code_barre").val(article.code_barre);
                        $('#prix_achat_ttc').val(article.prix_achat_ttc);
                    });
                }else{
                    $('#prix_achat_ttc').val("");
                    $('#quantite').val("");
                    $("#code_barre").val("");
                }
           });
        });
        $("#article_add").change(function (e) {
            var article_id = $("#article_add").val();
            $.getJSON("../stock/get-article-by-id/" + article_id, function (reponse) {
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, article) {
                        $("#code_barre_add").val(article.code_barre);
                        $('#prix_achat_ttc_add').val(article.prix_achat_ttc);
                    });
                }else{
                    $('#prix_achat_ttc_add').val("");
                    $('#quantite_add').val("");
                    $("#code_barre_add").val("");
                }
           });
        });

        $(".add-row").click(function () {
            if ($("#article").val() != '' && $("#quantite").val() != '') {
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var article = $("#article").val();
                var quantite = $("#quantite").val();
                var prix_achat_ttc = $("#prix_achat_ttc").val();

                var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='hidden' name='articles[]' value='" + article + "'>" + libelle_article + "</td><td><input type='hidden' name='prix_achats[]' value='" + prix_achat_ttc + "'>" + prix_achat_ttc + "</td><td><input type='hidden' name='quantites[]' value='" + quantite + "'>" + quantite + "</td><td><input type='hidden'>" + prix_achat_ttc*quantite + "</td></tr>";
                $(".articles-info").append(markup);
                $("#prix_achat_ttc").val("");
                $("#article").select2("val","");
                $("#quantite").val("");
                $("#code_barre").val("");
            }else{
                alert("Les champs article et quantité ne doivent pas restés vide");
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

        // Submit the add form
        $("#sendButton").click(function(){
            $("#formAjout").submit();
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
                var url = "{{route('stock.bon-commandes.store')}}";
             }else{
                var id = $("#idBonCommandeModifier").val();
                var methode = 'PUT';
                var url = 'bon-commandes/' + id;
             }
            editerBonAction(methode, url, $(this), $(this).serialize(), $ajaxLoader,$table, ajout);
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
                var url = "{{route('stock.articles-bon.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'articles-bon/' + id;
             }
            editerArticlesBonAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle,$table, ajoutArticle);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('articles-bon/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle,$table);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idBonCommandeSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('bon-commandes/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    function bonRow(idBonCommande){
        window.open("../stock/fiche-bon-commande-pdf/" + idBonCommande,'_blank');
    }
    function updateRow(idBonCommande) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var bonCommande =_.findWhere(rows, {id: idBonCommande});
        $scope.$apply(function () {
            $scope.populateForm(bonCommande);
        });
        $.getJSON("../stock/liste-articles", function (reponse) {
            $('#article_add').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, article) {
                    $('#article_add').append('<option data-libellearticle= "' + article.libelle_article + '" value=' + article.id + '>' + article.libelle_article + '</option>')
                });
        });
        $("#fournisseur_id").select2("val", bonCommande.fournisseur_id);
        $tableArticle.bootstrapTable('refreshOptions', {url: "../stock/liste-articles-bon/" + idBonCommande});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $(".bs-modal-ajout").modal("show");
    }
    function deleteRow(idBonCommande){
        var $scope = angular.element($("#formSupprimer")).scope();
        var bonCommande =_.findWhere(rows, {id: idBonCommande});
         $scope.$apply(function () {
            $scope.populateForm(bonCommande);
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
        var bonCommande = $("#idBonCommandeModifier").val();
        $("#bon_commande_id").val(bonCommande);
        $("#article_add").select2("val",article.article.id);
        $.getJSON("../stock/get-article-by-id/" + article.article.id, function (reponse) {
            $.each(reponse.rows, function (index, article){
                $('#prix_achat_ttc_add').val(article.prix_achat_ttc);
                $("#code_barre_add").val(article.code_barre);
            });
        });
        $('#quantite_add').val(article.quantite_demande);
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

    function montantTtcFormatter(id, row){
        montantTTC = row.prix_achat_ttc * row.quantite_demande;
        return '<span class="text-bold">' + $.number(montantTTC)+ '</span>';
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function etatFormatter(etat){
        switch(etat) {
            case 1:
                return '<span class="text-bold">Brouillon</span>';
                break;
            case 2:
                return '<span class="text-bold text-orange">Enregistré</span>';
                break;
            case 3:
                return '<span class="text-bold text-red">Refusé</span>';
                break;
            case 4:
                return '<span class="text-bold text-green">Receptionné</span>';
                break;
            case 5:
                return '<span class="text-bold">Facturé</span>';
                break;
            default:
                return '<span class="text-bold">Brouillon</span>';
        }
    }

    function optionFormatter(id, row) {
        if(row.etat==4 || row.etat==5){
            return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + row.id + ');"><i class="fa fa-edit"></i></button>';
        }else{
            return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + row.id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + row.id + ');"><i class="fa fa-trash"></i></button>';
        }
    }
    function optionArticleFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function bonFormatter(id, row) {
        return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Impirmer le bon" onClick="javascript:bonRow(' + id + ');"><i class="fa fa-print"></i></button>';
    }

   function editerBonAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#prix_achat_ttc").val("");
                    $("#prix_achat_ht").val("");
                    $("#article").val("");
                    $("#quantite").val("");
                    $("#fournisseur_id").select2("val", "");
                    $("table tbody").find('input[name="record"]').each(function () {
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

   function editerArticlesBonAction(methode, url, $formObject, formData, $ajoutLoader, $table,$table2, ajout = true) {
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
                    $table2.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $table2.bootstrapTable('refresh');
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
    function supprimerArticleAction(url, formData, $question, $ajaxLoader, $table,$table2) {
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
                $table2.bootstrapTable('refresh');
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


