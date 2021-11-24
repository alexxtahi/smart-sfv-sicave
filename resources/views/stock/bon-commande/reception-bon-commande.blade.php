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
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByFournisseur">
        <option value="0">-- Tous les fournisseurs --</option>
        @foreach($fournisseurs as $fournisseur)
        <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
        @endforeach
    </select>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-reception-bon-commandes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="numero_bon">N° du bon</th>
            <th data-field="date_bon_commandes">Date du bon</th>
            <th data-field="date_receptions">Date de reception</th>
            <th data-field="fournisseur.full_name_fournisseur">Fournisseur </th>
            <th data-field="montantBonRecu" data-formatter="montantFormatter">Montant Total</th>
            <th data-field="accompte" data-formatter="montantFormatter">Acompte</th>
            <th data-formatter="resteFormatter">Reste</th>
            <th data-field="etat" data-formatter="etatFormatter">Etat</th>
            <th data-formatter="imageFormatter" data-align="center">Facture</th>
            <th data-formatter="optionFormatter" data-width="120px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-clipboard fa-2x"></i>
                        R&eacute;ception de commande
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idBonCommandeModifier" name="idBonCommande" ng-hide="true" ng-model="bonCommande.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>N° du bon</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="bonCommande.numero_bon" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date du bon</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="bonCommande.date_bon_commandes" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fournisseur</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-truck"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="bonCommande.fournisseur.full_name_fournisseur" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date de r&eacute;ception *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="bonCommande.date_receptions" id="date_reception_commande" name="date_reception_commande" placeholder="Ex: 01-01-1994" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Facture fournisseur </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-file"></i>
                                    </div>
                                    <input type="file" class="form-control" name="scan_facture">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="id_row_table">
                        <br/><div class="col-md-12">
                            <table class="table table-primary table-striped box box-primary">
                                <thead>
                                    <th>Article</th>
                                    <th>Quantit&eacute; command&eacute;e</th>
                                    <th>Quantit&eacute; re&ccedil;ue</th>
                                </thead>
                                <tbody id="liste_articles">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal liste règlements -->
<div class="modal fade bs-modal-liste-reglement" id="listeReglement" ng-controller="listeReglementCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Liste des r&egrave;glements du bon de commande N° <b>@{{bonCommande.numero_bon}}</b> du fournisseur <b>@{{bonCommande.fournisseur.full_name_fournisseur}}</b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListeReglement" class="table table-success table-striped box box-success"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false">
                    <thead>
                        <tr>
                            <th data-field="date_reglements">Date</th>
                            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de paiement </th>
                            <th data-field="montant" data-formatter="montantFormatter">Montant</th>
                            <th data-formatter="objetFormatter">Objet</th>
                            <th data-field="full_name_fournisseur">Fournisseur</th>
                            <th data-field="numero_cheque">N° virement ou ch&egrave;que</th>
                            <th data-formatter="imageRglFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [], $tableListeReglement = jQuery("#tableListeReglement");
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (bonCommande) {
            $scope.bonCommande = bonCommande;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.bonCommande = {};
        };
    });
    appSmarty.controller('listeReglementCtrl', function ($scope) {
        $scope.populateListeReglementForm = function (bonCommande) {
            $scope.bonCommande = bonCommande;
        };
    });
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $('#searchByDate,#date_reception_commande').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $("#searchByBonCommande").keyup(function (e) {
            var numero_bon = $("#searchByBonCommande").val();
            if(numero_bon == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-reception-bon-commandes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../stock/liste-reception-bon-commandes-by-numero/' + numero_bon});
            }
        });
        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-reception-bon-commandes'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../stock/liste-reception-bon-commandes-by-date/' + date});
            }
        });
        $("#searchByFournisseur").change(function (e) {
            var fournisseur = $("#searchByFournisseur").val();
            if(fournisseur == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-reception-bon-commandes'])}}"});
            }
            else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-reception-bon-commandes-by-founisseur/' + fournisseur});
            }
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
            var url = "{{route('stock.reception-bon-store')}}";
            var formData = new FormData($(this)[0]);
            editerCommandeAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });
    });
    function verifierRow(idCommande){
        ajout = true;
        var $scope = angular.element($("#formAjout")).scope();
        var bonCommande =_.findWhere(rows, {id: idCommande});
        $scope.$apply(function () {
            $scope.populateForm(bonCommande);
        });
        $.getJSON("../stock/liste-articles-bon/" + idCommande , function (reponse) {
            $('#liste_articles').html('');
            if(reponse.total==0){
                $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe sur cette commande</td></tr>');
            }else{
                $.each(reponse.rows, function (index, article) {
                    $('#liste_articles').append("<tr><td><input type='hidden' name='articles[]' value='" + article.id_article + "'>" + article.libelle_article + "</td><td align='center' width='200'><input type='hidden' name='quantite_demandes[]' value='" + article.quantite_demande + "'>" + article.quantite_demande + "</td><td width='200'><input type='number' min='0' name='quantite_recus[] value='" + article.quantite_recu + "'></td></tr>");
                });
            }
        });
        $(".bs-modal-ajout").modal("show");
    }
    function livrerRow(idCommande){
        ajout = true;
        var $scope = angular.element($("#formAjout")).scope();
        var bonCommande =_.findWhere(rows, {id: idCommande});
        $scope.$apply(function () {
            $scope.populateForm(bonCommande);
        });
        $.getJSON("../stock/liste-articles-bon/" + idCommande , function (reponse) {
            $('#liste_articles').html('');
            if(reponse.total==0){
                $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe sur cette commande</td></tr>');
            }else{
                $.each(reponse.rows, function (index, article) {
                    $('#liste_articles').append("<tr><td>" + article.libelle_article + "</td><td align='center' width='200'>" + article.quantite_demande + "</td><td width='200'>" + article.quantite_recu +"</td></tr>");
                });
            }
        });
        $(".bs-modal-ajout").modal("show");
    }

    function reglementRow(idCommande) {
        var $scope = angular.element($("#listeReglement")).scope();
        var bonCommande =_.findWhere(rows, {id: idCommande});
        $scope.$apply(function () {
            $scope.populateListeReglementForm(bonCommande);
        });
        $tableListeReglement.bootstrapTable('refreshOptions', {url: "../vente/liste-reglements-by-bon/" + idCommande});
       $(".bs-modal-liste-reglement").modal("show");
    }
    function resteFormatter(id,row) {
        var montantReste = row.montantBonRecu - row.accompte;
        if(montantReste< 0){
           return '<span class="text-bold text-green">' + $.number(montantReste)+ '</span>';
        }
        if(montantReste> 0){
           return '<span class="text-bold text-red">' + $.number(montantReste)+ '</span>';
        }
        if(montantReste == 0 && row.etat==5){
           return '<span class="text-bold">Soldé</span>';
        }
    }
    function bonReceptionRow(idCommande){
        window.open("../stock/fiche-reception-bon-commande-pdf/" + idCommande,'_blank');
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function etatFormatter(etat){
        switch(etat) {
            case 4:
                return '<span class="text-bold text-green">Receptionné</span>';
                break;
            case 5:
                return '<span class="text-bold">Facturé</span>';
                break;
            default:
                return '<span class="text-bold text-green">Receptionné</span>';
        }
    }
    function imageFormatter(id, row) {
          return row.scan_facture ? "<a target='_blank' href='" + basePath + '/' + row.scan_facture + "'>Voir la facture</a>" : "";
    }
    function imageRglFormatter(id, row){
        return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }
    function objetFormatter(id, row){
        return '<span class="text-bold"> Bon de commande N° ' + row.numero_bon + '</span>';
    }
    function optionFormatter(id, row) {
        if(row.etat == 4 && row.accompte == 0){
            return  '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Vérifer les articles" onClick="javascript:verifierRow(' + row.id + ');"><i class="fa fa-edit"></i></button>';
        }

        if(row.etat == 5 && row.accompte==0){
            return '<button type="button" class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Livraison" onClick="javascript:livrerRow(' + row.id + ');"><i class="fa fa-check"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Bon de livraison" onClick="javascript:bonReceptionRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
        }

        if(row.etat == 5 && row.accompte>0){
            return '<button type="button" class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Livraison" onClick="javascript:livrerRow(' + row.id + ');"><i class="fa fa-check"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Liste des règlements" onClick="javascript:reglementRow(' + row.id + ');"><i class="fa fa-money"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Bon de livraison" onClick="javascript:bonReceptionRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
        }
    }

    function editerCommandeAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $('#liste_articles').html('');
                    $('#id_row_table').show();
                     $(".bs-modal-ajout").modal("hide");
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
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection

