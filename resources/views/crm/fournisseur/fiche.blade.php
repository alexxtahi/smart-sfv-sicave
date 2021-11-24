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
                <h3 class="widget-user-username"><span class="text-bold">{{$infoFournisseur->full_name_fournisseur}}</span></h3>
                <h5 class="widget-user-desc">{{$infoFournisseur->contact_fournisseur}}</h5>
                <h5 class="widget-user-desc">{{$infoFournisseur->adresse_fournisseur}}</h5>
            </div>
        </div>
    </div>
</div>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#bon_info" data-toggle="tab" aria-expanded="true">Liste des bons</a>
        </li>
        <li class="">
            <a href="#reglement_info" data-toggle="tab" aria-expanded="true">Liste des r&egrave;glements</a>
        </li>
        <li class="">
            <a href="#article_achat_info" data-toggle="tab" aria-expanded="true">Articles les plus command&eacute;s</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="bon_info">
            <div class="box-header">
                <div class="col-md-4">
                    <h3 class="box-title pull-left">Liste des bons</h3>
                </div>
                <div class="col-md-8">
                    <a class="btn btn-success pull-right" onclick="imprimeBonPdf()">Imprimer</a><br/>
                </div>
            </div>
            <div class="box-body">
                <table id="tableBon" class="table table-warning table-striped box box-primary"
                        data-pagination="true"
                        data-search="true"
                        data-toggle="table"
                        data-unique-id="id"
                        data-show-toggle="false"
                        data-show-columns="false">
                    <thead>
                        <tr>
                            <th data-width="60px" data-formatter="bonFormatter" data-align="center">Bon </th>
                            <th data-field="date_bons">Date </th>
                            <th data-field="numero_bon" data-search="true">N° Bon</th>
                            <th data-formatter="montantBonFormatter">Montant Total</th>
                            <th data-field="accompte" data-formatter="montantFormatter">Acompte</th>
                            <th data-formatter="resteFormatter">Reste</th>
                            <th data-field="etat" data-formatter="etatBonFormatter" data-align="center">Etat du bon</th>
                            <th data-formatter="imageFormatter" data-align="center">Facture</th>
                            <th data-formatter="panierFormatter" data-width="80px" data-align="center">Articles</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">
                                <span class="text-bold" id="total_tous_bon">0</span>
                            </h5>
                            <span class="description-text">TOTAL TOUS LES BONS</span>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">
                                <span class="text-bold" id="total_bon_recu">0</span>
                            </h5>
                            <span class="description-text">TOTAL BON RECU</span>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">
                                <span class="text-bold text-green" id="total_acompte">0</span>
                            </h5>
                            <span class="description-text">TOTAL ACOMPTE</span>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
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
                            <th data-field="date_reglements">Date</th>
                            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de paiement </th>
                            <th data-field="montant" data-formatter="montantFormatter">Montant</th>
                            <th data-formatter="objetFormatter">Objet</th>
                            <th data-field="full_name_fournisseur">Fournisseur</th>
                            <th data-field="numero_cheque">N° virement ou ch&egrave;que</th>
                            <th data-formatter="imageChekFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="article_achat_info">
            <div class="box-header">
                <div class="col-md-6">
                    <h3 class="box-title pull-left">Articles les plus command&eacute;s</h3>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-success pull-right" onclick="imprimeAchatPdf()">Imprimer</a><br/>
                </div>
            </div>
            <div class="box-body">
                <table id="tableArticleAchetes" class="table table-warning table-striped"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false"
                       data-show-columns="false">
                    <thead>
                        <tr>
                            <th data-field="libelle_article">Article</th>
                             <th data-field="prix_article" data-formatter="montantFormatter">Prix achat TTC</th>
                            <th data-field="qteTotaleDem" data-formatter="montantFormatter">Quantit&eacute; demand. </th>
                            <th data-field="qteTotaleRec" data-formatter="montantFormatter">Quantit&eacute; re&ccedil;ue</th>
                            <th data-formatter="sumFormatter">Montant</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal panier bon -->
<div class="modal fade bs-modal-panier-bon" id="panierBon" ng-controller="panierBonCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Liste des articles du bon N° <b>@{{bonCommande.numero_bon}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="tablePanierBon" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="libelle_article">Article</th>
                                    <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix achat TTC</th>
                                    <th data-field="quantite_demande" data-align="center">Qt&eacute; demand.</th>
                                    <th data-field="quantite_recu" data-align="center">Qt&eacute; re&ccedil;ue</th>
                                    <th data-formatter="montantTtcFormatter">Montant </th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<form>
    <input type="text" class="hidden" id="fournisseur" value="{{$infoFournisseur->id}}"/>
</form>
<script type="text/javascript">
    var fournisseur = $("#fournisseur").val();
    var $tableBon = jQuery("#tableBon"), rows = [], $tableReglement = jQuery("#tableReglement"),$tableArticleAchetes = jQuery("#tableArticleAchetes"), $tablePanierBon = jQuery("#tablePanierBon");

    appSmarty.controller('panierBonCtrl', function ($scope) {
        $scope.populateFormPanierBon = function (bonCommande) {
        $scope.bonCommande = bonCommande;
        };
    });

    $(function () {

        $tableBon.bootstrapTable('refreshOptions', {url: '../liste-tous-les-bons-fournisseur/' + fournisseur});
        $tableReglement.bootstrapTable('refreshOptions', {url: '/vente/liste-reglements-by-fournisseur/' + fournisseur});
        $tableArticleAchetes.bootstrapTable('refreshOptions', {url: '../liste-articles-commandes-by-fournisseur/' + fournisseur});


        $tableBon.on('load-success.bs.table', function (e, data) {
            rowsBon = data.rows;
            $("#total_tous_bon").html($.number(data.totalTousBon));
            $("#total_bon_recu").html($.number(data.totalBonRecu));
            $("#total_acompte").html($.number(data.totalAcompte));
            $("#total_reste").html($.number(data.totalBonRecu-data.totalAcompte));
        });
    });

    function panierBonRow(idBonCommande){
        var $scope = angular.element($("#panierBon")).scope();
        var bonCommande =_.findWhere(rowsBon, {id: idBonCommande});
         $scope.$apply(function () {
            $scope.populateFormPanierBon(bonCommande);
        });
        $tablePanierBon.bootstrapTable('refreshOptions', {url: basePath + "/stock/liste-articles-bon/" + idBonCommande});
        $(".bs-modal-panier-bon").modal("show");
    }

    function imprimeBonPdf(){
        window.open("/etat/liste-bons-fournisseur-pdf/" + fournisseur,'_blank');
    }

    function imprimeRglementPdf(){
        window.open("/etat/liste-rglements-fournisseur-pdf/" + fournisseur,'_blank');
    }

    function imprimeAchatPdf(){
        window.open("/etat/liste-articles-commandes-by-fournisseur-pdf/" + fournisseur,'_blank');
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function sumFormatter(id, row){
        var montant = row.qteTotaleRec*row.prix_article;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function objetFormatter(id, row){
        return '<span class="text-bold"> Bon N° ' + row.numero_bon + '</span>';
    }

    function imageChekFormatter(id, row) {
            return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }

    function panierFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Liste des articles" onClick="javascript:panierBonRow(' + row.id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
    }

    function imageFormatter(id, row) {
        return row.scan_facture ? "<a target='_blank' href='" + basePath + '/' + row.scan_facture + "'>Voir la facture</a>" : "";
    }

    function montantTtcFormatter(id, row){
        var qte = 0;
        row.quantite_recu ? qte = row.quantite_recu : qte = row.quantite_demande;
        montantTTC = row.prix_achat_ttc * qte;
        return '<span class="text-bold">' + $.number(montantTTC)+ '</span>';
    }

    function etatBonFormatter(etat){
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

    function montantBonFormatter(id, row){
        if(row.etat != 5){
            return '<span class="text-bold">' + $.number(row.montantBonDemande) + '</span>';
        }else{
            return '<span class="text-bold">' + $.number(row.montantBonRecu) + '</span>';
        }
    }
    function resteFormatter(id, row) {
        var montant = 0;
        if(row.etat != 5){
            return '<span class="text-bold">0</span>';
        }else{
            montant = row.montantBonRecu - row.accompte;
            return '<span class="text-bold">' + $.number(montant) + '</span>';
        }
    }
    function bonRow(idBon){
        window.open(basePath + "/stock/fiche-bon-commande-pdf/" + idBon,'_blank');
    }
    function bonRecuRow(idBon){
        window.open(basePath + "/stock/fiche-reception-bon-commande-pdf/" + idBon,'_blank');
    }

    function bonFormatter(id, row) {
        if(row.etat != 5){
            return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Impirmer le bon" onClick="javascript:bonRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
        }else{
            return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Impirmer le bon" onClick="javascript:bonRecuRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
        }
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


