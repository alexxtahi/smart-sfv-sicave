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
    <select class="form-control" id="searchByFournisseur">
        <option value="0">-- Tous les fournisseurs --</option>
        @foreach($fournisseurs as $fournisseur)
        <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-4">
    <select class="form-control" id="searchByDepot">
        <option value="0">-- Tous les d&eacute;p&ocirc;ts --</option>
        @foreach($depots as $depot)
        <option value="{{$depot->id}}"> {{$depot->libelle_depot.' '.$depot->adresse_depot}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <div class="form-group">
        <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date d'approvisionnement">
    </div>
</div>
<table id="table" class="table table-warning table-striped box box-primary" data-pagination="true" data-search="false" data-toggle="table" data-url="{{url('boutique',['action'=>'liste-approvisionnements'])}}" data-unique-id="id" data-show-toggle="false" data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_approvisionnements">Date </th>
            <th data-field="fournisseur.full_name_fournisseur">Fournisseur </th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="numero_conteneur">N° conteneur</th>
            <th data-field="numero_declaration">N° d&eacute;claration</th>
            <th data-field="numero_immatriculation">N° immatriculation</th>
            <th data-formatter="optionFormatter" data-width="70px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-subway fa-2x"></i>
                        Gestion des approvisionnements
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idApprovisionnementModifier" name="idApprovisionnement" ng-hide="true" ng-model="approvisionnement.id" />
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date d'approvisionnement *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="approvisionnement.date_approvisionnements" id="date_approvisionnement" name="date_approvisionnement" placeholder="Ex: 01-01-1994" value="<?= date('d-m-Y'); ?>" required>
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
                                    <select name="depot_id" id="depot_id" ng-model="approvisionnement.depot_id" ng-init="approvisionnement.depot_id=''" class="form-control select2" required>
                                        <option value="" ng-show="false">-- Selectionner le D&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fournisseur </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                    <select name="fournisseur_id" id="fournisseur_id" ng-model="approvisionnement.fournisseur_id" ng-init="approvisionnement.fournisseur_id=''" class="form-control select2">
                                        <option value="" ng-show="false">-- Selectionner le fournisseur --</option>
                                        @foreach($fournisseurs as $fournisseur)
                                        <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Num&eacute;ro d'immatriculation </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="approvisionnement.numero_immatriculation" id="numero_immatriculation" name="numero_immatriculation" placeholder="Numéro d'immatriculation">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Num&eacute;ro du conteneur </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="approvisionnement.numero_conteneur" id="numero_conteneur" name="numero_conteneur" placeholder="Numéro du conteneur">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Num&eacute;ro d&eacute;claration </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="approvisionnement.numero_declaration" id="numero_declaration" name="numero_declaration" placeholder="Numéro de déclaration">
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <option value="" ng-show="false">-- Article --</option>
                                        @foreach($articles as $article)
                                        <option data-libellearticle="{{$article->description_article}}" value="{{$article->id}}"> {{$article->description_article}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date de p&eacute;remption *</label>
                                    <input type="text" class="form-control" id="date_perimer" placeholder="date péremption">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Colis *</label>
                                    <select class="form-control" id="unite">
                                        <option value="" ng-show="false">-- Colis--</option>
                                        @foreach($unites as $unite)
                                        <option data-libelleunite="{{$unite->libelle_unite}}" value="{{$unite->id}}"> {{$unite->libelle_unite}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; *</label>
                                    <input type="number" class="form-control" id="quantite" min="0" placeholder="Qté / Btle">
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
                                    <label>Prix vente TTC </label>
                                    <input type="text" class="form-control" id="prix_vente_ttc" placeholder="Prix de vente TTC" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>TVA</label>
                                    <input type="text" class="form-control" id="montant_tva" placeholder="Montant TVA" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group"><br />
                                    <button type="button" class="btn btn-success btn-xs  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div><br />
                        <table class="table table-info table-striped box box-success">
                            <thead>
                                <tr>
                                    <th>Cochez</th>
                                    <th>Article</th>
                                    <th>Date de p&eacute;remption</th>
                                    <th>Colis</th>
                                    <th>Qt&eacute; / Btle</th>
                                    <th>Prix achat TTC</th>
                                    <th>Prix vente TTC</th>
                                    <th>TVA</th>
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
                        </div><br />
                        <table id="tableArticle" class="table table-success table-striped box box-success" data-pagination="true" data-search="false" data-toggle="table" data-unique-id="id" data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.description_article">Article</th>
                                    <th data-field="date_peremptions">Date de p&eacute;remption </th>
                                    <th data-field="unite.libelle_unite">Colis</th>
                                    <th data-field="quantite" data-align="center">Qt&eacute; </th>
                                    <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix achat TTC</th>
                                    <th data-field="prix_vente_ttc_base" data-formatter="montantFormatter">Prix vente TTC</th>
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

<!-- Modal liste articles -->
<div class="modal fade bs-modal-liste-article" id="listeArticle" ng-controller="listeArticleCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Liste des articles de l'approvisionnement <b>@{{approvisionnement.libelle_approvisionnement}}</b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListeArticle" class="table table-success table-striped box box-success" data-pagination="true" data-search="false" data-toggle="table" data-unique-id="id" data-show-toggle="false">
                    <thead>
                        <tr>
                            <th data-field="article.libelle_article">Article</th>
                            <th data-field="libelle_categorie">Cat&eacute;gorie </th>
                            <th data-field="quantite" data-align="center">Qt&eacute; </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
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
                    <input type="text" class="hidden" id="idArticleModifier" ng-model="article.id" />
                    <input type="text" class="hidden" id="approvisionnement" name="approvisionnement_id" />
                    <input type="text" class="hidden" id="depot" name="depot" />
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
                                <select class="form-control" name="article_id" id="article_add" required>
                                    <option value="" ng-show="false">-- Article --</option>
                                    @foreach($articles as $article)
                                    <option data-libellearticle="{{$article->description_article}}" value="{{$article->id}}"> {{$article->description_article}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date de p&eacute;remption *</label>
                                <input type="text" class="form-control" name="date_peremption" ng-model="article.date_peremptions" id="date_perimer_add" min="0" placeholder="Date de péremption" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Colis *</label>
                                <select class="form-control" id="unite_add" name="unite_id" ng-model="article.unite_id" ng-init="article.unite_id=''" required>
                                    <option value="" ng-show="false">-- Colis--</option>
                                    @foreach($unites as $unite)
                                    <option data-libelleunite="{{$unite->libelle_unite}}" value="{{$unite->id}}"> {{$unite->libelle_unite}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Qt&eacute; *</label>
                                <input type="number" class="form-control" name="quantite" id="quantite_add" min="0" placeholder="Qté / Btle" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix achat TTC</label>
                                <input type="number" class="form-control" id="prix_achat_ttc_add" min="0" placeholder="Prix achat TTC" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix vente TTC</label>
                                <input type="number" class="form-control" id="prix_vente_ttc_add" min="0" placeholder="Prix de vente TTC" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label> TVA</label>
                                <input type="number" class="form-control" id="montant_tva_add" min="0" placeholder="Montant TVA" readonly>
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
                    <input type="text" class="hidden" id="idArticleSupprimer" ng-model="article.id" />
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'article <br /><b>@{{article.description_article}}</b></div>
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
    var $table = jQuery("#table"),
        rows = [],
        $tableListeReglement = jQuery("#tableListeReglement"),
        $tableListeArticle = jQuery("#tableListeArticle"),
        $tableArticle = jQuery("#tableArticle"),
        rowsArticle = [];

    appSmarty.controller('formAjoutCtrl', function($scope) {
        $scope.populateForm = function(approvisionnement) {
            $scope.approvisionnement = approvisionnement;
        };
        $scope.initForm = function() {
            ajout = true;
            $scope.approvisionnement = {};
        };
    });

    appSmarty.controller('formAjoutArticleCtrl', function($scope) {
        $scope.populateArticleForm = function(article) {
            $scope.article = article;
        };
        $scope.initForm = function() {
            ajout = true;
            $scope.article = {};
        };
    });

    appSmarty.controller('formSupprimerArticleCtrl', function($scope) {
        $scope.populateSupArticleForm = function(article) {
            $scope.article = article;
        };
        $scope.initForm = function() {
            $scope.article = {};
        };
    });

    appSmarty.controller('listeArticleCtrl', function($scope) {
        $scope.populateListeArticleForm = function(approvisionnement) {
            $scope.approvisionnement = approvisionnement;
        };
    });

    appSmarty.controller('listeReglementCtrl', function($scope) {
        $scope.populateListeReglementForm = function(approvisionnement) {
            $scope.approvisionnement = approvisionnement;
        };
    });

    $(function() {
        $table.on('load-success.bs.table', function(e, data) {
            rows = data.rows;
        });
        $tableArticle.on('load-success.bs.table', function(e, data) {
            rowsArticle = data.rows;
        });
        $("#fournisseur_id, #depot_id, #article, #article_add").select2({
            width: '100%'
        });
        $("#div_enregistrement").show();
        $("#div_update").hide();

        $("#searchByFournisseur").change(function(e) {
            var fournisseur = $("#searchByFournisseur").val();
            if (fournisseur == 0) {
                $table.bootstrapTable('refreshOptions', {
                    url: "{{url('boutique', ['action' => 'liste-approvisionnements'])}}"
                });
            } else {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-approvisionnements-by-fournisseur/' + fournisseur
                });
            }
        });
        $("#searchByDepot").change(function(e) {
            var depot = $("#searchByDepot").val();
            if (depot == 0) {
                $table.bootstrapTable('refreshOptions', {
                    url: "{{url('boutique', ['action' => 'liste-approvisionnements'])}}"
                });
            } else {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-approvisionnements-by-depot/' + depot
                });
            }
        });
        $("#searchByDate").change(function(e) {
            var date = $("#searchByDate").val();
            if (date == "") {
                $table.bootstrapTable('refreshOptions', {
                    url: "{{url('boutique', ['action' => 'liste-approvisionnements'])}}"
                });
            } else {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-approvisionnements-by-date/' + date
                });
            }
        });

        $('#searchByDate, #date_approvisionnement').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local: 'fr',
            maxDate: new Date()
        });

        $('#date_perimer, #date_perimer_add').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local: 'fr',
            minDate: new Date()
        });

        $("#btnModalAjoutArticle").on("click", function() {
            ajoutArticle = true;
            var approvisionnement = $("#idApprovisionnementModifier").val();
            var depot = $("#depot_id").val();
            document.forms["formAjoutArticle"].reset();
            $("#article_add").select2("val", "");
            $("#approvisionnement").val(approvisionnement);
            $("#depot").val(depot);
            $(".bs-modal-add-article").modal("show");
        });

        $("#btnModalAjout").on("click", function() {
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $("#depot_id").select2("val", "");
            $("#article").select2("val", "");
            $("#fournisseur_id").select2("val", "");
        });

        $("#depot_id").change(function(e) {
            $('#prix_vente_ttc').val("");
            $('#prix_achat_ttc').val("");
            $('#quantite').val("");
        });
        $("#code_barre").keyup(function(e) {
            if (e.which == '10' || e.which == '13') {
                var code_barre = $("#code_barre").val();
                var depot_id = $("#depot_id").val();
                $('#montant_tva').val("");
                $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function(reponse) {
                    if (reponse.total > 0) {
                        $.each(reponse.rows, function(index, retour) {
                            $("#article").select2("val", retour.article.id)
                            $.getJSON("../parametre/find-article/" + retour.article.id, function(reponse) {
                                if (reponse.total > 0) {
                                    $.each(reponse.rows, function(index, article) {
                                        $('#prix_achat_ttc').val(article.prix_achat_ttc);
                                        $('#prix_vente_ttc').val(article.prix_vente_ttc_base);
                                        if (article.param_tva_id != null) {
                                            $('#montant_tva').val(article.param_tva.montant_tva * 100);
                                        }
                                    });
                                } else {
                                    $('#prix_achat_ttc').val("");
                                    $('#prix_vente_ttc').val("");
                                    $('#quantite').val("");
                                    $('#montant_tva').val("");
                                }
                            });
                        });
                    }
                });
                e.preventDefault();
                e.stopPropagation();
            }
        });

        $("#code_barre_add").keyup(function(e) {
            var code_barre = $("#code_barre_add").val();
            var depot_id = $("#depot_id").val();
            $('#montant_tva').val("");
            $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function(reponse) {
                if (reponse.total > 0) {
                    $('#article').html("<optionvalue=''>-- Article --</option>");
                    $.each(reponse.rows, function(index, retour) {
                        $("#article_add").select2("val", retour.article.id)
                        $.getJSON("../parametre/find-article/" + retour.article.id, function(reponse) {
                            if (reponse.total > 0) {
                                $.each(reponse.rows, function(index, article) {
                                    $('#prix_achat_ttc_add').val(article.prix_achat_ttc);
                                    $('#prix_vente_ttc_add').val(article.prix_vente_ttc_base);
                                    if (article.param_tva_id != null) {
                                        $('#montant_tva_add').val(article.param_tva.montant_tva * 100);
                                    }

                                });
                            } else {
                                $('#prix_achat_ttc_add').val("");
                                $('#prix_vente_ttc_add').val("");
                                $('#quantite_add').val("");
                                $('#montant_tva_add').val("");
                            }
                        });
                    });
                } else {

                }
            });
        });

        $("#article").change(function(e) {
            var article_id = $("#article").val();
            $('#montant_tva').val("");
            $.getJSON("../parametre/find-article/" + article_id, function(reponse) {
                if (reponse.total > 0) {
                    $.each(reponse.rows, function(index, article) {
                        $("#code_barre").val(article.code_barre)
                        $('#prix_achat_ttc').val(article.prix_achat_ttc);
                        $('#prix_vente_ttc').val(article.prix_vente_ttc_base);
                        if (article.param_tva_id != null) {
                            $('#montant_tva').val(article.param_tva.montant_tva * 100);
                        }
                    });
                } else {
                    $('#prix_achat_ttc').val("");
                    $('#prix_vente_ttc').val("");
                    $('#quantite').val("");
                    $('#montant_tva').val("");
                }
            });
        });
        $("#article_add").change(function(e) {
            var article_id = $("#article_add").val();
            $('#montant_tva_add').val("");
            $.getJSON("../parametre/find-article/" + article_id, function(reponse) {
                if (reponse.total > 0) {
                    $.each(reponse.rows, function(index, article) {
                        $("#code_barre_add").val(article.code_barre)
                        $('#prix_achat_ttc_add').val(article.prix_achat_ttc);
                        $('#prix_vente_ttc_add').val(article.prix_vente_ttc_base);
                        if (article.param_tva_id != null) {
                            $('#montant_tva_add').val(article.param_tva.montant_tva * 100);
                        }

                    });
                } else {
                    $('#prix_achat_ttc_add').val("");
                    $('#prix_vente_ttc_add').val("");
                    $('#quantite_add').val("");
                    $('#montant_tva_add').val("");
                }
            });
        });

        $(".add-row").click(function() {
            if ($("#article").val() != '' && $("#quantite").val() != '' && $("#date_perimer").val() && $("#unite").val()) {
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var libelle_unite = $("#unite").children(":selected").data("libelleunite");
                var article = $("#article").val();
                var quantite = $("#quantite").val();
                var unite = $("#unite").val();
                var date_perimer = $("#date_perimer").val();
                var prix_achat_ttc = $("#prix_achat_ttc").val();
                var prix_vente_ttc = $("#prix_vente_ttc").val();
                var montant_tva = $("#montant_tva").val();

                var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='hidden' name='articles[]' value='" + article + "'>" + libelle_article + "</td><td><input type='hidden' name='date_peremptions[]' value='" + date_perimer + "'>" + date_perimer + "</td><td><input type='hidden' name='unites[]' value='" + unite + "'>" + libelle_unite + "</td><td><input type='hidden' name='quantites[]' value='" + quantite + "'>" + quantite + "</td><td><input type='hidden'>" + prix_achat_ttc + "</td><td><input type='hidden'>" + prix_vente_ttc + "</td><td><input type='hidden'>" + montant_tva + "</td></tr>";
                $(".articles-info").append(markup);
                $("#prix_achat_ttc").val("");
                $("#unite").val("");
                $("#article").select2("val", "");
                $("#quantite").val("");
                $("#prix_vente_ttc").val("");
                $("#montant_tva").val("");
                $("#date_perimer").val("");
                $("#code_barre").val("");
            } else {
                alert("Les champs article, date de péremption, colis et quantité ne doivent pas restés vides");
            }
        });

        // Find and remove selected table rows
        $(".delete-row").click(function() {
            $(".articles-info").find('input[name="record"]').each(function() {
                if ($(this).is(":checked")) {
                    $(this).parents("tr").remove();
                } else {
                    alert("Cochez la ligne que vous souhaitez supprimer !");
                }
            });
        });
        // Submit the add form
        $("#sendButton").click(function() {
            $("#formAjout").submit();
        });
        $("#formAjout").submit(function(e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjout .loader-overlay");

            if (ajout == true) {
                var methode = 'POST';
                var url = "{{route('boutique.approvisionnements.store')}}";
            } else {
                var methode = 'POST';
                var url = "{{route('boutique.update-approvisionnement')}}";
            }
            var formData = new FormData($(this)[0]);
            editerApprovisionnementAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });

        $("#formAjoutArticle").submit(function(e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutArticle .loader-overlay");

            if (ajoutArticle == true) {
                var methode = 'POST';
                var url = "{{route('boutique.approvisionnements-articles.store')}}";
            } else {
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'approvisionnements-articles/' + id;
            }
            editerApprovisionnementsArticlesAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle, $table, ajoutArticle);
        });

        $("#formSupprimerArticle").submit(function(e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('approvisionnements-articles/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle);
        });
    });

    function updateRow(idApprovisionnement) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var approvisionnement = _.findWhere(rows, {
            id: idApprovisionnement
        });
        $scope.$apply(function() {
            $scope.populateForm(approvisionnement);
        });
        $.getJSON("../parametre/liste-articles/", function(reponse) {
            $('#article_add').html("<option>-- Article --</option>");
            $.each(reponse.rows, function(index, article) {
                $('#article_add').append('<option data-libellearticle= "' + article.description_article + '" value=' + article.id + '>' + article.description_article + '</option>')
            });
        });
        if (approvisionnement.fournisseur_id != null) {
            $("#fournisseur_id").select2("val", approvisionnement.fournisseur_id);
            $("#depot_id").select2("val", approvisionnement.depot_id);
        }
        $tableArticle.bootstrapTable('refreshOptions', {
            url: "../boutique/liste-articles-approvisionnes/" + idApprovisionnement
        });
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $(".bs-modal-ajout").modal("show");
    }

    function articleRow(idApprovisionnement) {
        var $scope = angular.element($("#listeArticle")).scope();
        var approvisionnement = _.findWhere(rows, {
            id: idApprovisionnement
        });
        $scope.$apply(function() {
            $scope.populateListeArticleForm(approvisionnement);
        });
        $tableListeArticle.bootstrapTable('refreshOptions', {
            url: "../boutique/liste-articles-approvisionnes/" + idApprovisionnement
        });
        $(".bs-modal-liste-article").modal("show");
    }

    function updateArticleRow(idArticle) {
        ajoutArticle = false;
        var $scope = angular.element($("#formAjoutArticle")).scope();
        var article = _.findWhere(rowsArticle, {
            id: idArticle
        });
        $scope.$apply(function() {
            $scope.populateArticleForm(article);
        });
        var approvisionnement = $("#idApprovisionnementModifier").val();
        var depot = $("#depot_id").val();
        $("#approvisionnement").val(approvisionnement);
        $("#depot").val(depot);

        $("#article_add").select2("val", article.article.id);
        $.getJSON("../parametre/article-by-name/" + article.article.description_article, function(reponse) {
            $.each(reponse.rows, function(index, article) {
                $('#prix_achat_ttc_add').val(article.prix_achat_ttc);
                $('#prix_vente_ttc_add').val(article.prix_vente_ttc_base);
            });
        });
        $('#quantite_add').val(article.quantite);
        $(".bs-modal-add-article").modal("show");
    }

    function deleteArticleRow(idArticle) {
        var $scope = angular.element($("#formSupprimerArticle")).scope();
        var article = _.findWhere(rowsArticle, {
            id: idArticle
        });
        $scope.$apply(function() {
            $scope.populateSupArticleForm(article);
        });
        $(".bs-modal-supprimer-article").modal("show");
    }

    function printRow(idApprovisionnement) {
        window.open("../boutique/fiche-approvisionnement-pdf/" + idApprovisionnement, '_blank');
    }

    function prixTtcFormatter(id, row) {
        var montant = 0;
        if (row.id_tva != null) {
            montant = row.prix_achat_ht * row.montant_tva + row.prix_achat_ht;
            return '<span class="text-bold">' + montant + '</span>';
        } else {
            montant = row.prix_achat_ht;
        }
        return '<span class="text-bold">' + montant + '</span>';
    }

    function montantTtcFormatter(id, row) {
        var montantTTC = 0;
        if (row.id_tva != null) {
            var montant = row.prix_achat_ht * row.montant_tva + row.prix_achat_ht;
            montantTTC = montant * row.quantite;
        } else {
            montantTTC = row.prix_achat_ht * row.quantite;
        }
        return '<span class="text-bold">' + montantTTC + '</span>';
    }

    function depotFormatter(id, row) {
        return row.depot.libelle_depot + ' ' + row.depot.adresse_depot;
    }

    function imageFormatter(id, row) {
        return row.scan_facture_fournisseur ? "<a target='_blank' href='" + basePath + '/' + row.scan_facture_fournisseur + "'>Voir la facture</a>" : "";
    }

    function montantFormatter(montant) {
        return '<span class="text-bold">' + $.number(montant) + '</span>';
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + row.id + ');"><i class="fa fa-edit"></i></button>\n\
                <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
    }

    function optionArticleFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function optionListeArticleFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function imageFormatter(id, row) {
        return row.scan_facture_fournisseur ? "<a target='_blank' href='" + basePath + '/' + row.scan_facture_fournisseur + "'>Voir la facture</a>" : "";
    }

    function editerApprovisionnementAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
        jQuery.ajax({
            type: methode,
            url: url,
            cache: false,
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse, textStatus, xhr) {
                if (reponse.code === 1) {
                    var $scope = angular.element($formObject).scope();
                    $scope.$apply(function() {
                        $scope.initForm();
                    });
                    if (ajout) { //creation
                        $table.bootstrapTable('refresh');
                        $("#prix_achat_ttc").val("");
                        $("#prix_achat_ht").val("");
                        $("#article").val("");
                        $("#quantite").val("");
                        $("#fournisseur_id").select2("val", "");
                        $("#depot_id").select2("val", "");
                        $("#code_barre_add").val("");
                        $("#article").select2("val", "");
                        $("table tbody").find('input[name="record"]').each(function() {
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
            error: function(err) {
                var res = eval('(' + err.responseText + ')');
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
            beforeSend: function() {
                $formObject.attr("disabled", true);
                $ajoutLoader.show();
            },
            complete: function() {
                $ajoutLoader.hide();
            },
        });
    };

    function editerApprovisionnementsArticlesAction(methode, url, $formObject, formData, $ajoutLoader, $table, $table2, ajout = true) {
        jQuery.ajax({
            type: methode,
            url: url,
            cache: false,
            data: formData,
            success: function(reponse, textStatus, xhr) {
                if (reponse.code === 1) {
                    var $scope = angular.element($formObject).scope();
                    $scope.$apply(function() {
                        $scope.initForm();
                    });
                    if (ajout) { //creation
                        $table.bootstrapTable('refresh');
                        $table2.bootstrapTable('refresh');
                        $("#code_barre_add").val("");
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
            error: function(err) {
                var res = eval('(' + err.responseText + ')');
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
            beforeSend: function() {
                $formObject.attr("disabled", true);
                $ajoutLoader.show();
            },
            complete: function() {
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
            success: function(reponse) {
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
            error: function(err) {
                var res = eval('(' + err.responseText + ')');
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
            beforeSend: function() {
                $question.hide();
                $ajaxLoader.show();
            },
            complete: function() {
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
