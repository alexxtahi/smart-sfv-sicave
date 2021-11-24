@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Comptable')
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="box box-widget widget-user-2">
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle" src="{{asset('images/profil.png')}}" alt="User Avatar">
                </div>
                <h3 class="widget-user-username"><span class="text-bold">{{$infoClient->full_name_client}}</span></h3>
                <h5 class="widget-user-desc">{{$infoClient->contact_client}}</h5>
                <h5 class="widget-user-desc">{{$infoClient->adresse_client}}</h5>
            </div>
        </div>
    </div>
</div>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#facture_info" data-toggle="tab" aria-expanded="true">Liste des factures</a>
        </li>
        <li class="">
            <a href="#reglement_info" data-toggle="tab" aria-expanded="true">Liste des r&egrave;glements</a>
        </li>
        <li class="">
            <a href="#article_achat_info" data-toggle="tab" aria-expanded="true">Articles les plus achet&eacute;s</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="facture_info">
            <div class="box-header">
                <div class="col-md-2">
                    <h3 class="box-title pull-left">Liste des factures</h3>
                </div>
                <div class="col-md-10">
                    <a class="btn btn-success pull-right" onclick="imprimeFacturePdf()">Imprimer</a><br/>
                </div>
            </div>
            <div class="box-body">
                <table id="tableFacture" class="table table-warning table-striped"
                        data-pagination="true"
                        data-search="true"
                        data-toggle="table"
                        data-unique-id="id"
                        data-show-toggle="false"
                        data-show-columns="false">
                    <thead>
                        <tr>
                            <th data-formatter="factureFormatter" data-align="center" data-width="60px"><i class="fa fa-print"></i></th>
                            <th data-field="date_facture">Date </th>
                            <th data-field="numero_facture" data-search="true">Facture </th>
                            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
                            <th data-field="montantTTC" data-formatter="montantFormatter">Montant TTC</th>
                            <th data-field="acompte_facture" data-formatter="montantFormatter">Acompte</th>
                            <th data-formatter="resteFormatter">Reste</th>
                            <th data-field="id" data-formatter="panierFormatter" data-width="60px" data-align="center"><i class="fa fa-wrench"></i></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-4 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">
                                <span class="text-bold text-green" id="total_facture">0</span>
                            </h5>
                            <span class="description-text">TOTAL FACTURE</span>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">
                                <span class="text-bold" id="total_acompte">0</span>
                            </h5>
                            <span class="description-text">TOTAL ACOMPTE</span>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-6">
                        <div class="description-block">
                            <h5 class="description-header">
                                <span class="text-bold text-red" id="total_reste">0</span>
                            </h5>
                            <span class="description-text">TOTAL RESTE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="reglement_info">
            <div class="box-header">
                <div class="col-md-4">
                    <h3 class="box-title pull-left">Liste des r&egrave;glements</h3>
                </div>
                <div class="col-md-8">
                    <a class="btn btn-success pull-right" onclick="imprimeRglementPdf()">Imprimer</a><br/>
                </div>
            </div>
            <div class="box-body">
                <table id="tableReglement" class="table table-warning table-striped"
                        data-pagination="true"
                        data-search="false"
                        data-toggle="table"
                        data-unique-id="id"
                        data-show-toggle="false"
                        data-show-columns="false">
                    <thead>
                        <tr>
                            <th data-formatter="recuFormatter">Re&ccedil;u</th>
                            <th data-field="date_reglements">Date</th>
                            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de paiement </th>
                            <th data-field="montant" data-formatter="montantFormatter">Montant</th>
                            <th data-formatter="objetFormatter">Objet</th>
                            <th data-field="full_name_client">Client</th>
                            <th data-field="numero_cheque">N° virement ou ch&egrave;que</th>
                            <th data-formatter="imageFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="article_achat_info">
            <div class="box-header">
                <div class="col-md-8">
                    <h3 class="box-title pull-left">Liste des articles les plus achet&eacute;s</h3>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-success pull-right" onclick="imprimeArticleAchatPdf()">Imprimer</a><br/>
                </div>
            </div>
            <div class="box-body">
                <table id="tableArticlesAchats" class="table table-warning table-striped"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false"
                       data-show-columns="false">
                    <thead>
                        <tr>
                            <th data-field="libelle_article">Article</th>
                            <th data-field="qteTotale" data-formatter="montantFormatter">Quantit&eacute;</th>
                            <th data-field="montantTTC" data-formatter="montantFormatter">Montant</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal panier -->
<div class="modal fade bs-modal-panier" id="panierArticle" ng-controller="panierArticleCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Panier de la facture N° <b>@{{vente.numero_facture}}</b>
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

<form>
    <input type="text" class="hidden" id="client" value="{{$infoClient->id}}"/>
</form>

<script type="text/javascript">
    var client = $("#client").val();
    var $tableFacture = jQuery("#tableFacture"), rowsFactures = [],$tableReglement = jQuery("#tableReglement"),$tablePanierArticle = jQuery("#tablePanierArticle"), $tableArticlesAchats = jQuery("#tableArticlesAchats");

    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
        $scope.vente = vente;
        };
    });

    $(function () {
        $tableFacture.bootstrapTable('refreshOptions', {url: '../liste-factures-client/' + client});
        $tableReglement.bootstrapTable('refreshOptions', {url: '/vente/liste-reglements-by-client/' + client});
        $tableArticlesAchats.bootstrapTable('refreshOptions', {url: '../liste-articles-achetes-by-client/' + client});


        $tableFacture.on('load-success.bs.table', function (e, data) {
            rowsFactures = data.rows;
            $("#total_facture").html($.number(data.totalFacture));
            $("#total_acompte").html($.number(data.totalAcompte));
            $("#total_reste").html($.number(data.totalFacture-data.totalAcompte));
        });
    });

    function listePanierRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rowsFactures, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: basePath + "/vente/liste-articles-vente/" + idVente});
        $(".bs-modal-panier").modal("show");
    }

    function imprimeFacturePdf(){
        window.open("/etat/liste-factures-client-pdf/" + client,'_blank');
    }

    function imprimeRglementPdf(){
        window.open("/etat/liste-rglements-client-pdf/" + client,'_blank');
    }

    function imprimeArticleAchatPdf(){
        window.open("/etat/liste-articles-achetes-by-client-pdf/" + client,'_blank');
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function objetFormatter(id, row){
        return '<span class="text-bold"> Facture N° ' + row.numero_facture + '</span>';
    }
    function recuPrintRow(idReglement){
        window.open(basePath + "/vente/recu-reglement-pdf/" + idReglement ,'_blank')
    }
    function recuFormatter(id, row){
        return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Imprimer le reçu" onClick="javascript:recuPrintRow(' + row.id + ');"><i class="fa fa-print"></i></button>'
    }

    function resteFormatter(id, row){
        var montant = row.montantTTC - row.acompte_facture;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function facturePrintRow(idVente){
        window.open("/vente/facture-vente-pdf/" + idVente ,'_blank')
    }
    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function factureFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Facture" onClick="javascript:facturePrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }
    function panierFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listePanierRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
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
    function imageFormatter(id, row) {
          return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


