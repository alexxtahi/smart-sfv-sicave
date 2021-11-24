@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">

<div class="row">
    <div class="col-md-12" id="listArticle" ng-controller="listArticleCtrl">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control" id="searchByCodeBarre" placeholder="Code barre">
                 </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control" id="searchByLibelle" ng-model="searchByLibelle" placeholder="Nom de l'article">
                 </div>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="searchByCategorie">
                    <option value="0">-- Toutes les cat&eacute;gories --</option>
                    @foreach($categories as $categorie)
                    <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div ng-repeat="article in articles | filter:{article.libelle_article:searchByLibelle} | firstPage:currentPage*pageSize | limitTo:pageSize">
                <div class="col-md-1">
                    <a class="users-list-name text-center" style="cursor: pointer;" onclick="getArticle(@{{ article.article.id }})">
                        <img ng-if="article.article.image_article" class="profile-user-img img-responsive img-circle" src="@{{article.article.image_article}}" style="width: 50%;" alt="User Image">
                        <img ng-if="!article.article.image_article" class="profile-user-img img-responsive img-circle" src="{{asset('images/point.jpg')}}" style="width: 50%;" alt="User Image">
                        <label>@{{ article.article.libelle_article }}</label>
                    </a>
                </div>
            </div>
        </div>
        <div class="row" ng-show="articles.length > 12">
            <div class="col-md-12">
                <div class="d-flex justify-content-between text-center">
                    <button ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1" class="btn btn-icon btn-lg btn-light-primary mr-2 my-1"><i class="fa fa-angle-left pull-left"></i></button>
                        <a class="btn btn-icon btn-lg border-0 btn-hover-primary active mr-2 my-1">@{{currentPage+1}}/@{{numberOfPages()}}</a>
                    <button ng-disabled="currentPage >= articles.length/pageSize - 1" ng-click="currentPage=currentPage+1" class="btn btn-icon btn-lg btn-light-primary mr-2 my-1"><i class="fa fa-angle-right pull-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <span class="box-title h3">Panier</span>
                        <span class="box-title pull-right h3" id="totalPanier"></span>
                    </div>
                    <div class="box-body">
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
                                            <th data-field="libelle_article">Article</th>
                                            <th data-field="en_stock" data-align="center">En stock</th>
                                            <th data-field="prix" data-align="center">PU</th>
                                            <th data-field="quantite" data-align="center" data-editable="true">Qt&eacute; / Btle</th>
                                            <th data-field="montant">Montant</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <br/>
                        <form id="formAjout" action="#">
                            @csrf
                            <input type="text" class="hidden" name="depot_id" value="{{Auth::user()->depot_id}}">
                            <input type="text" class="hidden" name="caisse_id" value="{{$caisse_id}}">
                            <div class="row" id="row_regle">
                                <div class="col-md-12">
                                    <label for="attente">
                                        <input type="checkbox" id="attente" name="attente"> &nbsp;&nbsp; <span class="text-red">Cochez cette case pour mettre le client en attente</span>
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <ul class="list-group list-group-unbordered">
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                   <h3> <b>Montant Total </b> <a class="pull-right text-bold montantTotal"> </a></h3>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Moyen de paiement *</label>
                                                        <select class="form-control" id="moyen_reglement_id" name="moyen_reglement_id" required>
                                                            <option value="">-- Moyen de paiement --</option>
                                                            @foreach($moyenReglements as $moyenReglement)
                                                                @if ($moyenReglement->libelle_moyen_reglement == 'ESPECE')
                                                                    <option data-libellemoyen="{{ $moyenReglement->libelle_moyen_reglement }}" value="{{ $moyenReglement->id }}" selected>{{ $moyenReglement->libelle_moyen_reglement }}</option>
                                                                @else
                                                                    <option data-libellemoyen="{{ $moyenReglement->libelle_moyen_reglement }}" value="{{ $moyenReglement->id }}">{{ $moyenReglement->libelle_moyen_reglement }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="montant_a_payer">Montant &agrave; payer</label>
                                                        <input type="text" pattern="[0-9]*" class="form-control" name="montant_a_payer" id="montant_a_payer" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="montant_paye">Montant pay&eacute;</label>
                                                        <input type="text" pattern="[0-9]*" class="form-control" name="montant_paye" id="montant_paye" placeholder="Montant pay&eacute;">
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Monnaie &agrave; rendre </b> <a class="pull-right text-bold h4 monnaieArendre"> 0 </a>
                                        </li>
                                        <li class="list-group-item" id="carteFideliteLine">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="numero_carte_fidelite">Num&eacute;ro de la carte de fid&eacute;lit&eacute;</label>
                                                    <input type="text" class="form-control" name="numero_carte_fidelite" placeholder="Numero de la carte">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="numero_carte_fidelite">Montant</label>
                                                    <input type="text" pattern="[0-9]*" class="form-control" name="montant_carte" placeholder="Montant">
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" id="sendButton" class="btn btn-success pull-right"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal fermeture caisse -->
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
                                            <h5 class="description-header"><p class="text-orange" id="solde_caisse"></p></h5>
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
                                @if(Auth::user()->role!='Caissier')
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
                                    <p class="h3 text-bold text-center"> Gestion du billetage</p>
                                    <hr/>
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
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-danger"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span> Fermer</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var articles = {!! json_encode($articles) !!};
    var monPanier = [];
    var idTablle =  0;
    var quantite = 0;
    var montantTotal = 0;
    var $tableAddRowArticle = jQuery("#tableAddRowArticle");

    appSmarty.controller('listArticleCtrl', function ($scope) {
        $scope.populateForm = function (articles) {
            $scope.articles = articles;
            $scope.currentPage = 0;
            $scope.pageSize = 16;
            $scope.numberOfPages=function(){
                return Math.ceil($scope.articles.length/$scope.pageSize);
            }
        };
    });

    appSmarty.filter('firstPage', function() {
        return function(input, start) {
            start = +start;
            return input.slice(start);
        }
    });

    $(function () {

        $("#searchByCategorie").select2({width: '100%'});
        $("#totalPanier").hide();
        $("#row_regle").hide();
        $(".delete-row").hide();
        $("#carteFideliteLine").hide();

        var $scope = angular.element($("#listArticle")).scope();
        $scope.$apply(function () {
            $scope.populateForm(articles);
        });

        $("#montant_paye").keyup(function (e) {
            var montant_paye = $("#montant_paye").val();
            var montant_a_payer = $("#montant_a_payer").val();
            var montant = parseInt(montant_paye) - parseInt(montant_a_payer);

            $(".monnaieArendre").html("<b>"+montant+"</b>");
        });

        $("#moyen_reglement_id").change(function (e) {
            var libelle_moyen = $("#moyen_reglement_id").children(":selected").data("libellemoyen");
            if(libelle_moyen == "CARTE DE FIDELITE"){
                $("#carteFideliteLine").show();
            }else{
                $("#carteFideliteLine").hide();
            }
        });

        $("#searchByCategorie").change(function (e) {
            var categorie = $("#searchByCategorie").val();
            var url = "";
            if(categorie != 0){
                url = "../stock/liste-article-by-categorie-depot/" + categorie + "/" + 1;
            }else{
                url = "../stock/liste-article-by-depot/" + 1;
            }

            $.getJSON(url, function (reponse) {
                var $scope = angular.element($("#listArticle")).scope();
                var articles = [];
                $.each(reponse.rows, function (index, datas) {
                    articles.push(datas);
                });
                $scope.$apply(function () {
                    $scope.populateForm(articles);
                });
            });
        });

        $("#searchByCodeBarre").keyup(function (e) {
            var code_barre = $("#searchByCodeBarre").val();
            if(e.which == '10' || e.which == '13') {
                $.getJSON("../stock/liste-article-by-depot-code-barre/" + 1 + "/" + code_barre, function (reponse) {

                    if(reponse.total>0){

                        quantite++;
                        $.each(reponse.rows, function (index, datas) {
                            var libelle_article = datas.article.libelle_article;
                            var prix = datas.prix_vente_detail;
                            var en_stock = datas.quantite_disponible;

                            //Controle de la quantité avant l'ajout
                            if(parseInt(quantite) > parseInt(en_stock)){
                                $.gritter.add({
                                    title: "SMART-SFV",
                                    text: "La quantité à vendre ne doit pas depasser la quantité disponible en stock",
                                    sticky: false,
                                    image: basePath + "/assets/img/gritter/confirm.png",
                                });
                                quantite=0;
                                return;
                            }

                            //Vérification Si la ligne existe déja dans le tableau
                            var articleTrouver = _.findWhere(monPanier, {articles: datas.article.id})
                            if(articleTrouver!=null) {
                                //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                                oldQte = articleTrouver.quantites;
                                idElementLigne = articleTrouver.id;

                                //Si la somme des deux quantités depasse la quantité à ajouter en stock
                                var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);

                                if(parseInt(sommeDeuxQtes)> parseInt(en_stock)){
                                    $.gritter.add({
                                        title: "SMART-SFV",
                                        text: "Cet article existe dans votre panier, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle disponible en stock",
                                        sticky: false,
                                        image: basePath + "/assets/img/gritter/confirm.png",
                                    });
                                    quantite=0;
                                    return;
                                }
                                //MAJ de la ligne
                                montantTotal = montantTotal - (prix*oldQte);
                                $("#montant_a_payer").val(montantTotal);
                                $("#montant_paye").val(montantTotal);
                                $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                    id: idElementLigne,
                                    row: {
                                        quantite : sommeDeuxQtes,
                                        montant: $.number(prix*sommeDeuxQtes)
                                    }
                                });
                                articleTrouver.quantites = sommeDeuxQtes;
                                montantTotal = montantTotal + (prix*sommeDeuxQtes);
                                $("#montant_a_payer").val(montantTotal);
                                $("#montant_paye").val(montantTotal);
                                $(".montantTotal").html("<b>"+ $.number(montantTotal)+" F CFA</b>");
                                $("#totalPanier").html("Total :<b>"+ $.number(montantTotal)+" F CFA</b>");
                                quantite=0;
                                idTablle
                                return;
                            }
                            idTablle++;
                            $tableAddRowArticle.bootstrapTable('insertRow',{
                                index: idTablle,
                                row: {
                                    id: idTablle,
                                    libelle_article: libelle_article,
                                    en_stock : en_stock,
                                    prix: $.number(prix),
                                    quantite: quantite,
                                    article: datas.article.id,
                                    montant: $.number(quantite*prix),
                                }
                            });
                            //Creation de l'article dans le tableau virtuel (panier)
                            var DataArticle = {'id':idTablle, 'articles':datas.article.id, 'quantites':quantite,'prix':prix};
                            monPanier.push(DataArticle);
                            montantTotal = montantTotal + (prix*quantite);
                            $("#montant_a_payer").val(montantTotal);
                            $("#montant_paye").val(montantTotal);
                            $("#totalPanier").show();
                            $("#row_regle").show();
                            $(".montantTotal").html("<b>"+ $.number(montantTotal)+" F CFA</b>");
                            $("#totalPanier").html("Total :<b>"+ $.number(montantTotal)+" F CFA</b>");
                            if(idTablle>0){
                                $(".delete-row").show();
                            }
                            quantite=0;
                        });
                        $("#searchByCodeBarre").val("");
                    }else{
                        $.gritter.add({
                            title: "SMART-SFV",
                            text: "Cet article n'est pas disponible dans ce dépôt",
                            sticky: false,
                            image: basePath + "/assets/img/gritter/confirm.png",
                        });
                        $("#searchByCodeBarre").val("");
                        return;
                    }
                });
                e.preventDefault();
                e.stopPropagation();
            }
        });
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

                    montantTotal = montantTotal - (articleTrouver.prix*articleTrouver.quantites);
                    $(".montantTotal").html("<b>"+ $.number(montantTotal)+" F CFA</b>");
                    $("#totalPanier").html("Total :<b>"+ $.number(montantTotal)+" F CFA</b>");
                    $("#montant_a_payer").val(montantTotal);
                    $("#montant_paye").val(montantTotal);

                    monPanier = _.reject(monPanier, function (article) {
                        return article.id == value.id;
                    });
                if(monPanier.length==0){
                    idTablle = 0;
                    $("#totalPanier").hide();
                    $("#row_regle").hide();
                    $("#montant_a_payer").val("");
                    $(".delete-row").hide();
                }
            });
        });

        $("#btnFermerCaisse").on("click", function () {
            $(".bs-modal-fermeture-caisse").modal("show");
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

            var methode = 'POST';
            var url = "{{route('vente.caisse.store')}}";
            var formData = new FormData($(this)[0]);
            createFormData(formData, 'monPanier', monPanier);
            editerVenteCaisseAction(methode, url, $(this), formData, $ajaxLoader);
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

    function getArticle(idArticle){
        $.getJSON("../stock/liste-article-by-article-depot/" + idArticle + "/" + 1, function (reponse) {

            if(reponse.total>0){

                quantite++;
                $.each(reponse.rows, function (index, datas) {
                    var libelle_article = datas.article.libelle_article;
                    var prix = datas.prix_vente_detail;
                    var en_stock = datas.quantite_disponible;

                    //Controle de la quantité avant l'ajout
                    if(parseInt(quantite) > parseInt(en_stock)){
                        $.gritter.add({
                            title: "SMART-SFV",
                            text: "La quantité à vendre ne doit pas depasser la quantité disponible en stock",
                            sticky: false,
                            image: basePath + "/assets/img/gritter/confirm.png",
                        });
                        quantite=0;
                        return;
                    }

                    //Vérification Si la ligne existe déja dans le tableau
                    var articleTrouver = _.findWhere(monPanier, {articles: idArticle})
                    if(articleTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = articleTrouver.quantites;
                        idElementLigne = articleTrouver.id;

                        //Si la somme des deux quantités depasse la quantité à ajouter en stock
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);

                        if(parseInt(sommeDeuxQtes)> parseInt(en_stock)){
                            $.gritter.add({
                                title: "SMART-SFV",
                                text: "Cet article existe dans votre panier, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle disponible en stock",
                                sticky: false,
                                image: basePath + "/assets/img/gritter/confirm.png",
                            });
                            quantite=0;
                            return;
                        }
                        //MAJ de la ligne
                        montantTotal = montantTotal - (prix*oldQte);
                        $("#montant_a_payer").val(montantTotal);
                        $("#montant_paye").val(montantTotal);
                        $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                            id: idElementLigne,
                            row: {
                                quantite : sommeDeuxQtes,
                                montant: $.number(prix*sommeDeuxQtes)
                            }
                        });
                        articleTrouver.quantites = sommeDeuxQtes;
                        montantTotal = montantTotal + (prix*sommeDeuxQtes);
                        $("#montant_a_payer").val(montantTotal);
                        $("#montant_paye").val(montantTotal);
                        $(".montantTotal").html("<b>"+ $.number(montantTotal)+" F CFA</b>");
                        $("#totalPanier").html("Total :<b>"+ $.number(montantTotal)+" F CFA</b>");
                        quantite=0;
                        idTablle
                        return;
                    }
                    idTablle++;
                    $tableAddRowArticle.bootstrapTable('insertRow',{
                        index: idTablle,
                        row: {
                            id: idTablle,
                            libelle_article: libelle_article,
                            en_stock : en_stock,
                            prix: $.number(prix),
                            quantite: quantite,
                            article: idArticle,
                            montant: $.number(quantite*prix),
                        }
                    });
                    //Creation de l'article dans le tableau virtuel (panier)
                    var DataArticle = {'id':idTablle, 'articles':idArticle, 'quantites':quantite,'prix':prix};
                    monPanier.push(DataArticle);
                    montantTotal = montantTotal + (prix*quantite);
                    $("#montant_a_payer").val(montantTotal);
                    $("#montant_paye").val(montantTotal);
                    $("#totalPanier").show();
                    $(".delete-row").show();
                    $(".montantTotal").html("<b>"+ $.number(montantTotal)+" F CFA</b>");
                    $("#totalPanier").html("Total :<b>"+ $.number(montantTotal)+" F CFA</b>");
                    if(idTablle>0){
                        $("#row_regle").show();
                    }
                    quantite=0;
                });
            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Cet article n'est pas disponible dans ce dépôt",
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                return;
            }
        });
    }

    function editerVenteCaisseAction(methode, url, $formObject, formData, $ajoutLoader) {
        jQuery.ajax({
            type: methode,
            url: url,
            cache: false,
            data: formData,
            contentType: false,
            processData: false,
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
</script>
@endsection
