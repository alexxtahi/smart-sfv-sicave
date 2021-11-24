@extends('layouts.app')
@section('content')

<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
@if(Auth::user()->role!="Caissier")
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
<div class="col-md-3">
    <select class="form-control" id="searchByClient">
        <option value="0">-- Tous les clients --</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
        @endforeach
    </select>
</div>
@endif
<table id="table" class="table table-primary table-striped box box-primary" data-pagination="true" data-search="false" data-toggle="table" data-unique-id="id" data-show-toggle="false" data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_operations">Date</th>
            <th data-field="montant_operation" data-formatter="montantFormatter">Montant</th>
            <th data-field="objet_operation">Objet</th>
            <th data-formatter="typeFormatter">Type op&eacute;ration</th>
            <th data-formatter="concerneFormatter">Concerne</th>
            @if(Auth::user()->role!="Caissier")
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
            @endif
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
                        <i class="fa fa-paypal fa-2x"></i>
                        Gestion des p&eacute;rations de caisse
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idOperationModifier" ng-hide="true" ng-model="operation.id" />
                    @if(Auth::user()->role == 'Caissier' && $caisse_ouverte!=null)
                    <input type="text" class="hidden" id="caisse" ng-hide="true" name="caisse_id" value="{{$caisse_ouverte->caisse_id}}" />
                    @endif
                    @if(Auth::user()->role != 'Caissier' && $caisse!=null)
                    <input type="text" class="hidden" id="caisse" ng-hide="true" name="caisse_id" value="{{$caisse->id}}" />
                    @endif
                    @csrf
                    <div class="row" id="row_premier">
                        <div class="col-md-1"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    <input type="radio" onclick="concerner(this.value)" name="concerne" checked="checked" value="client">&nbsp;Client
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <input type="radio" onclick="concerner(this.value)" name="concerne" value="fournisseur">&nbsp;Fournisseur
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <input type="radio" onclick="concerner(this.value)" name="concerne" value="autres">&nbsp;Autre
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="row_client">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_id">Client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <select id="client_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le client --</option>
                                        @foreach($clients as $client)
                                        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control" id="contact_client" placeholder="Contact du client" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Adresse client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" class="form-control" id="adresse_client" placeholder="Adresse du client" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="row_fournisseur">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fournisseur_id">Fournisseur *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-institution"></i>
                                    </div>
                                    <select id="fournisseur_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le fournisseur --</option>
                                        @foreach($fournisseurs as $fournisseur)
                                        <option value="{{$fournisseur->id}}">{{$fournisseur->full_name_fournisseur}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact fournisseur</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control" id="contact_fournisseur" placeholder="Contact du fournisseur" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Adresse g&eacute;ographique </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" class="form-control" id="adresse_fournisseur" placeholder="Adresse du fournisseur" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="row_second">
                        <div class="col-md-4" id="facture_vente">
                            <div class="form-group">
                                <label for="vente_id">Facture(s) *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select id="vente_id" name="vente_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner la facture --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="facture_fournisseur">
                            <div class="form-group">
                                <label for="bon_commande_id">Commande(s) *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select id="commande_id" name="bon_commande_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner la commande --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Montant TTC </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" id="montant_ttc" placeholder="Montant TTC" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reste &agrave; payer </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" id="montant_restant" placeholder="Montant restant" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type_operation">Type d'op&eacute;ration *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-cog"></i>
                                    </div>
                                    <select name="type_operation" id="type_operation" ng-model="operation.type_operation" ng-init="operation.type_operation=''" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner --</option>
                                        <option value="entree"> Entr&eacute; d'argent </option>
                                        <option value="sortie"> Sortie d'argent </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Montant *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" ng-model="operation.montant_operation" id="montant_operation" name="montant_operation" placeholder="Montant" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Objet de l'op&eacute;ration *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="operation.objet_operation" id="objet_operation" name="objet_operation" placeholder="Objet de l'opération" required>
                                </div>
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
                    <input type="text" class="hidden" id="idOperationSupprimer" ng-model="operation.id" />
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'operation <br /><b>@{{operation.objet_operation}}</b></div>
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
    var $table = jQuery("#table"),
        rows = [];

    appSmarty.controller('formAjoutCtrl', function($scope) {
        $scope.populateForm = function(operation) {
            $scope.operation = operation;
        };
        $scope.initForm = function() {
            ajout = true;
            $scope.operation = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function($scope) {
        $scope.populateForm = function(operation) {
            $scope.operation = operation;
        };
        $scope.initForm = function() {
            $scope.operation = {};
        };
    });

    $(function() {
        var caisse = $("#caisse").val();
        $table.bootstrapTable('refreshOptions', {
            url: '../boutique/liste-operations-by-caisse/' + caisse
        });

        $table.on('load-success.bs.table', function(e, data) {
            rows = data.rows;
        });

        $('#searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local: 'fr',
            maxDate: new Date()
        });
        $("#searchByDate").change(function(e) {
            var date = $("#searchByDate").val();
            if (date == '') {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-operations-by-caisse/' + caisse
                });
            } else {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-operations-by-caisse-date/' + caisse + '/' + date
                });
            }
        });
        $("#searchByFournisseur").change(function(e) {
            var fournisseur = $("#searchByFournisseur").val();
            if (fournisseur == 0) {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-operations-by-caisse/' + caisse
                });
            } else {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-operations-by-caisse-fournisseur/' + caisse + '/' + fournisseur
                });
            }
        });
        $("#searchByClient").change(function(e) {
            var client = $("#searchByClient").val();
            if (client == 0) {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-operations-by-caisse/' + caisse
                });
            } else {
                $table.bootstrapTable('refreshOptions', {
                    url: '../boutique/liste-operations-by-caisse-client/' + caisse + '/' + client
                });
            }
        });

        $('#row_client').show();
        $('#row_fournisseur').hide();
        $('#row_premier').show();
        $('#facture_vente').show();
        $('#facture_fournisseur').hide();
        $('#vente_id').attr('disabled', false)
        $('#commande_id').attr('disabled', true)
        $('#client_id').attr('required', true)
        $('#fournisseur_id').attr('required', false)
        $('#row_second').show();
        $('#btnModalAjout').click(function() {
            $('#row_premier').show();
            $('#row_second').show();
            $('#row_client').show();
            $('#row_fournisseur').hide();
            $('#facture_vente').show();
            $('#facture_fournisseur').hide();
            $('#vente_id').attr('disabled', false)
            $('#commande_id').attr('disabled', true)
            $('#client_id').attr('required', true)
            $('#fournisseur_id').attr('required', false)
            $('#contact_client').val("");
            $('#contact_fournisseur').val("");
            $('#adresse_client').val("");
            $('#adresse_fournisseur').val("");
            $('#montant_ttc').val("");
            $('#montant_restant').val("");
            $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
            $('#commande_id').html("<option value=''>-- Selectionner la commande --</option>");
        });

        $('#client_id').change(function() {
            var client_id = $('#client_id').val();
            $.getJSON("../boutique/get-all-facture-client/" + client_id, function(reponse) {
                $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function(index, client) {
                    $('#contact_client').val(client.client.contact_client)
                    $('#adresse_client').val(client.client.adresse_client)
                    if ((client.sommeTotale - client.acompte_facture > 0) && client.proformat == 0) {
                        $('#vente_id').append('<option value=' + client.id + '> Facture N° ' + client.numero_facture + '</option>')
                    }
                });
            })
        });
        $('#fournisseur_id').change(function() {
            var fournisseur_id = $('#fournisseur_id').val();
            $.getJSON("../boutique/liste-reception-commande-by-fournisseur/" + fournisseur_id, function(reponse) {
                $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function(index, fournisseur) {
                    $('#contact_fournisseur').val(fournisseur.fournisseur.contact_fournisseur)
                    $('#adresse_fournisseur').val(fournisseur.fournisseur.adresse_fournisseur)
                    if ((fournisseur.montantCommande - fournisseur.accompteFournisseur) > 0) {
                        $('#commande_id').append('<option data-numeorbon= "' + fournisseur.numero_bon + '" value=' + fournisseur.commmande_id + '>' + fournisseur.numero_bon + '</option>')
                    }
                });
            })
        });
        $('#vente_id').change(function() {
            var vente_id = $('#vente_id').val();
            $.getJSON("../boutique/find-vente-by-id/" + vente_id, function(reponse) {
                $.each(reponse.rows, function(index, vente) {
                    $('#montant_ttc').val(vente.sommeTotale);
                    $('#montant_restant').val(vente.sommeTotale - vente.acompte_facture)
                });
            })
        });
        $('#commande_id').change(function() {
            var numero_bon = $("#commande_id").children(":selected").data("numeorbon");
            $.getJSON("../boutique/liste-reception-commande-by-numero_bon/" + numero_bon, function(reponse) {
                $.each(reponse.rows, function(index, commande) {
                    var montant_restant = commande.montantCommande - commande.accompteFournisseur;
                    $('#montant_ttc').val(commande.montantCommande);
                    $('#montant_restant').val(montant_restant)
                });
            })
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
                var url = "{{route('boutique.operations.store')}}";
            } else {
                var id = $("#idOperationModifier").val();
                var methode = 'PUT';
                var url = 'operations/' + id;
            }
            editerOperation(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });
        $("#formSupprimer").submit(function(e) {
            e.preventDefault();
            var id = $("#idOperationSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('operations/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });

    function updateRow(idOperation) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var operation = _.findWhere(rows, {
            id: idOperation
        });
        $scope.$apply(function() {
            $scope.populateForm(operation);
        });
        if (operation.vente_id != null) {
            $.getJSON("../parametre/find-client-by-vente/" + operation.vente_id, function(reponse) {
                $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function(index, client) {
                    $("#client_id").val(client.client.id);
                    $('#contact_client').val(client.client.contact_client)
                    $('#adresse_client').val(client.client.adresse_client)
                    $('#vente_id').append('<option selected value=' + client.id + '> Facture N° ' + client.numero_facture + '</option>')
                });
            })
            $.getJSON("../boutique/find-vente-by-id/" + operation.vente_id, function(reponse) {
                $.each(reponse.rows, function(index, vente) {
                    $('#montant_ttc').val(vente.sommeTotale);
                    $('#montant_restant').val(vente.sommeTotale - vente.acompte_facture)
                });
            })
            $('input:radio[name=concerne]').val(['client']);
            $('#fournisseur_id').val("");
            $('#adresse_fournisseur').val("");
            $('#contact_fournisseur').val("");
            $('#row_fournisseur').hide();
            $('#facture_fournisseur').hide();
            $('#facture_vente').show();
            $('#row_client').show();
            $('#row_second').show();
            $('#row_premier').show();
            $('#vente_id').attr('disabled', false)
            $('#commande_id').attr('disabled', true)
            $('#client_id').attr('required', true)
            $('#fournisseur_id').attr('required', false)
            $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
            $("#vente_id").val(operation.vente_id);
        }
        if (operation.bon_commande_id != null) {
            $.getJSON("../parametre/find-fournisseur-by-commande/" + operation.bon_commande_id, function(reponse) {
                $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function(index, fournisseur) {
                    $("#fournisseur_id").val(fournisseur.fournisseur.id);
                    $('#contact_fournisseur').val(fournisseur.fournisseur.contact_fournisseur)
                    $('#adresse_fournisseur').val(fournisseur.fournisseur.adresse_fournisseur)
                    $('#commande_id').append('<option selected data-numeorbon= "' + fournisseur.numero_bon + '" value=' + fournisseur.id + '>' + fournisseur.numero_bon + '</option>')
                });
            })
            $("#commande_id").val(operation.commande_id);
            $.getJSON("../boutique/find-reception-commande-by-id/" + operation.bon_commande_id, function(reponse) {
                $.each(reponse.rows, function(index, commande) {
                    var montant_restant = commande.montantCommande - commande.accompteFournisseur;
                    $('#montant_ttc').val(commande.montantCommande);
                    $('#montant_restant').val(montant_restant)
                });
            })
            $('input:radio[name=concerne]').val(['fournisseur']);
            $('#client_id').val("");
            $('#adresse_client').val("");
            $('#contact_client').val("");
            $('#row_client').hide();
            $('#facture_vente').hide();
            $('#facture_fournisseur').show();
            $('#row_fournisseur').show();
            $('#vente_id').attr('disabled', true)
            $('#row_second').show();
            $('#row_premier').show();
            $('#commande_id').attr('disabled', false)
            $('#client_id').attr('required', false)
            $('#fournisseur_id').attr('required', true)
            $('#vente_id').html("<option>-- Selectionner la facture --</option>");
            $("#commande_id").val(operation.commande_id)
        }

        if (operation.bon_commande_id == null && operation.vente_id == null) {
            $('input:radio[name=concerne]').val(['autres']);
            $('#client_id').val("");
            $('#adresse_client').val("");
            $('#contact_client').val("");
            $('#row_client').hide();
            $('#facture_vente').hide();
            $('#facture_fournisseur').hide();
            $('#row_fournisseur').hide();
            $('#row_second').hide();
            $('#row_premier').show();
            $('#fournisseur_id').val("");
            $('#adresse_fournisseur').val("");
            $('#contact_fournisseur').val("");
            $('#row_fournisseur').hide();
            $('#facture_fournisseur').hide();
            $('#facture_vente').hide();
            $('#row_client').hide();
            $('#vente_id').attr('disabled', true)
            $('#commande_id').attr('disabled', true)
            $('#client_id').attr('required', false)
            $('#fournisseur_id').attr('required', false)
            $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
            $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
        }
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idOperation) {
        var $scope = angular.element($("#formSupprimer")).scope();
        var operation = _.findWhere(rows, {
            id: idOperation
        });
        $scope.$apply(function() {
            $scope.populateForm(operation);
        });
        $(".bs-modal-suppression").modal("show");
    }

    function montantFormatter(montant) {
        return '<span class="text-bold">' + $.number(montant) + '</span>';
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function concerneFormatter(id, row) {
        if (row.id_client) {
            return '<span class="text-bold text-green"> Client ' + row.full_name_client + '</span>';
        }
        if (row.commande_id) {
            return '<span class="text-bold text-red"> Fournisseur ' + row.full_name_fournisseur + '</span>';
        }
        if (row.bon_commande_id == null && row.vente_id == null) {
            return ''
        }
    }

    function typeFormatter(id, row) {
        if (row.type_operation == 'entree') {
            return "<span class='text-bold text-green'>Entrée</span>";
        } else {
            return "<span class='text-bold text-red'>Sortie d'argent</span>";
        }
    }

    function concerneFormatter(id, row) {
        if (row.concerne == 'fournisseur') {
            return "<span class='text-bold'>Fournisseur</span>";
        }
        if (row.concerne == "client") {
            return "<span class='text-bold'>Client</span>";
        }
        if (row.concerne == "autres") {
            return "<span class='text-bold'>Autre</span>";
        }
    }

    function concerner(concerneValue) {
        switch (concerneValue) {
            case 'client':
                if ($('#client_id').val() == '') {
                    $('#fournisseur_id').val("");
                    $('#adresse_fournisseur').val("");
                    $('#contact_fournisseur').val("");
                    $('#montant_ttc').val("");
                    $('#montant_restant').val("");
                    $('#row_fournisseur').hide();
                    $('#facture_fournisseur').hide();
                    $('#facture_vente').show();
                    $('#row_client').show();
                    $('#row_second').show();
                    $('#vente_id').attr('disabled', false)
                    $('#commande_id').attr('disabled', true)
                    $('#client_id').attr('required', true)
                    $('#fournisseur_id').attr('required', false)
                    $('#contact_client').val("");
                    $('#adresse_client').val("");
                    $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
                    $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
                } else {
                    $('#fournisseur_id').val("");
                    $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
                    $('#adresse_fournisseur').val("");
                    $('#contact_fournisseur').val("");
                    $('#commande_id').attr('disabled', true)
                    $('#fournisseur_id').attr('required', false)
                    $('#client_id').attr('required', true)
                    $('#row_fournisseur').hide();
                    $('#facture_fournisseur').hide();
                }
                break;
            case 'fournisseur':
                if ($('#fournisseur_id').val() == '') {
                    $('#row_client').hide();
                    $('#facture_vente').hide();
                    $('#facture_fournisseur').show();
                    $('#row_fournisseur').show();
                    $('#vente_id').attr('disabled', true)
                    $('#commande_id').attr('disabled', false)
                    $('#client_id').attr('required', false)
                    $('#fournisseur_id').attr('required', true)
                    $('#contact_fournisseur').val("");
                    $('#adresse_fournisseur').val("");
                    $('#contact_client').val("");
                    $('#client_id').val("");
                    $('#row_second').show();
                    $('#adresse_client').val("");
                    $('#montant_ttc').val("");
                    $('#montant_restant').val("");
                    $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
                    $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
                } else {
                    $('#client_id').val("");
                    $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
                    $('#adresse_client').val("");
                    $('#contact_client').val("");
                    $('#vente_id').attr('disabled', true)
                    $('#client_id').attr('required', false)
                    $('#fournisseur_id').attr('required', true)
                    $('#row_client').hide();
                    $('#facture_vente').hide();
                }
                break;
            case 'autres':
                $('input:radio[name=concerne]').val(['autres']);
                $('#client_id').val("");
                $('#adresse_client').val("");
                $('#contact_client').val("");
                $('#row_client').hide();
                $('#facture_vente').hide();
                $('#facture_fournisseur').hide();
                $('#row_fournisseur').hide();
                $('#row_second').hide();
                $('#row_premier').show();
                $('#fournisseur_id').val("");
                $('#adresse_fournisseur').val("");
                $('#contact_fournisseur').val("");
                $('#row_fournisseur').hide();
                $('#facture_fournisseur').hide();
                $('#facture_vente').hide();
                $('#row_client').hide();
                $('#vente_id').attr('disabled', true)
                $('#commande_id').attr('disabled', true)
                $('#client_id').attr('required', false)
                $('#fournisseur_id').attr('required', false)
                $('#vente_id').html("<option value=''>-- Selectionner la facture --</option>");
                $('#commande_id').html("<option value=''>-- Selectionner la facture --</option>");
            default:
        }
    }


    function editerOperation(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                        $('#row_client').show();
                        $('#row_fournisseur').hide();
                        $('#row_premier').show();
                        $('#row_second').show();
                        $('#facture_vente').show();
                        $('#facture_fournisseur').hide();
                        $('#vente_id').attr('disabled', false)
                        $('#commande_id').attr('disabled', true)
                        $('#fournisseur_id').attr('required', false)
                        $('#client_id').attr('required', true)
                        $('#contact_client').val("");
                        $('#contact_fournisseur').val("");
                        $('#adresse_client').val("");
                        $('#adresse_fournisseur').val("");
                        $("#clientAnonyme").val("");
                        $('#montant_ttc').val("");
                        $('#montant_restant').val("");
                        $('#date_reglement').attr('disabled', false)
                        $('#montant_reglement').attr('disabled', false)
                        $('#vente_id').html("<option>-- Selectionner la facture --</option>");
                        $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                        $('input:radio[name=concerne]').val(['client']);
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
</script>

@endsection