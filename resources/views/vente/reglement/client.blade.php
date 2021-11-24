@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
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
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-4">
    <select class="form-control" id="searchByClient">
        <option value="0">-- Tous les clients --</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByModes">
        <option value="0">-- Tous les moyens payement --</option>
        @foreach($moyenReglements as $moyenReglement)
        <option value="{{$moyenReglement->id}}"> {{$moyenReglement->libelle_moyen_reglement}}</option>
        @endforeach
    </select>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
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
                        <i class="fa fa-paypal fa-2x"></i>
                        Gestion des r&egrave;glements
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" name="idReglement" ng-hide="true" ng-model="reglement.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_id">Client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <select id="client_id" class="form-control" required>
                                        <option value="">-- Selectionner le client --</option>
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
                                    <input type="text"  class="form-control" id="contact_client" placeholder="Contact du client" readonly>
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
                                    <input type="text"  class="form-control" id="adresse_client" placeholder="Adresse du client" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
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
                                <label for="moyen_reglement_id">Moyen de payement *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-cog"></i>
                                    </div>
                                    <select name="moyen_reglement_id" id="moyen_reglement_id" ng-model="reglement.moyen_reglement_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner --</option>
                                        @foreach($moyenReglements as $moyenReglement)
                                        <option data-libellemoyen="{{ $moyenReglement->libelle_moyen_reglement }}" value="{{$moyenReglement->id}}">{{$moyenReglement->libelle_moyen_reglement}}</option>
                                        @endforeach
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
                                    <input type="text" pattern="[0-9]*" class="form-control" ng-model="reglement.montant" id="montant" name="montant" placeholder="Montant" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date de payement *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="reglement.date_reglements" id="date_reglement" name="date_reglement" value="{{date('d-m-Y')}}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="carteFideliteLine">
                            <label for="numero_carte_fidelite">Num&eacute;ro de la carte de fid&eacute;lit&eacute;</label>
                            <input type="text" class="form-control" name="numero_carte_fidelite" placeholder="Numero de la carte">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Ajouter le fichier si le payement a &eacute;t&eacute; fait par ch&egrave;que </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-file"></i>
                                    </div>
                                    <input type="file" class="form-control" name="scan_cheque">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Num&eacute;ro du ch&egrave;que ou du virement </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="reglement.numero_cheque" id="numero_cheque" name="numero_cheque"  placeholder="Numéro du chèque ou du virement">
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
                    <input type="text" class="hidden" id="idReglementSupprimer"  ng-model="reglement.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le r&egrave;glement du client <br/><b>@{{reglement.full_name_client + ' au ' + reglement.date_reglements}}</b></div>
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
    var $table = jQuery("#table"), rows = [];

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (reglement) {
            $scope.reglement = reglement;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.reglement = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (reglement) {
            $scope.reglement = reglement;
        };
        $scope.initForm = function () {
            $scope.reglement = {};
        };
    });

    $(function () {
        $("#carteFideliteLine").hide();
        $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements/' + 'client'});

        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $('#date_reglement, #searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });

        $("#searchByClient, #client_id").select2({width: '100%'});

        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ''){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements/' + 'client'});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-date/' + 'client' + '/' + date});
            }
        });

        $("#searchByClient").change(function (e) {
            var client = $("#searchByClient").val();
            var mode = $("#searchByModes").val();

            if(client == 0 && mode == 0){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements/' + 'client'});
            }
            if(client != 0 && mode == 0){
              $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-client/' + client});
            }
            if(client == 0 && mode != 0){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-moyen-reglement-client/' + mode + '/' + 'client'});
            }
            if(client != 0 && mode != 0){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-moyen-reglement-client/' + mode + '/' + client});
            }
        });
        $("#searchByModes").change(function (e) {
            var client = $("#searchByClient").val();
            var mode = $("#searchByModes").val();

            if(client == 0 && mode == 0){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements/' + 'client'});
            }
            if(client != 0 && mode == 0){
              $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-client/' + client});
            }
            if(client == 0 && mode != 0){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-moyen-reglement-client/' + mode + '/' + 'client'});
            }
            if(client != 0 && mode != 0){
                $table.bootstrapTable('refreshOptions', {url: '../vente/liste-reglements-by-moyen-reglement-client/' + mode + '/' + client});
            }
        });

        $("#moyen_reglement_id").change(function (e) {
            var libelle_moyen = $("#moyen_reglement_id").children(":selected").data("libellemoyen");
            if(libelle_moyen == "CARTE DE FIDELITE"){
                $("#carteFideliteLine").show();
            }else{
                $("#carteFideliteLine").hide();
            }
        });

        $('#btnModalAjout').click(function(){
            $("#client_id").select2('val','');
            $('#contact_client').val("");
            $('#adresse_client').val("");
            $('#montant_ttc').val("");
            $('#montant_restant').val("");
            $('#vente_id').html("<option>-- Selectionner la facture --</option>");
        });

        $('#client_id').change(function(){
            var client_id = $('#client_id').val();
            $.getJSON("../crm/liste-factures-client/" + client_id, function (reponse) {
                $('#vente_id').html("<option>-- Selectionner la facture --</option>");
                    if(reponse.total>0){
                        $.each(reponse.rows, function (index, facture) {
                                $('#contact_client').val(facture.client.contact_client)
                                $('#adresse_client').val(facture.client.adresse_client)
                                if((facture.montantTTC-facture.acompte_facture>0)){
                                    $('#vente_id').append('<option data-numerofacture=' + facture.numero_facture + ' value=' + facture.id + '> Facture N° ' + facture.numero_facture + '</option>');
                                }
                        });
                    }else{
                        alert('Aucune facture trouvée !');
                    }
            });
        });

        $('#vente_id').change(function(){
            var numero_facture = $("#vente_id").children(":selected").data("numerofacture");
            $.getJSON("../vente/liste-ventes/" + numero_facture, function (reponse) {
                $.each(reponse.rows, function (index, vente) {
                    $('#montant_ttc').val(vente.montantTTC);
                    $('#montant_restant').val(vente.montantTTC-vente.acompte_facture)
                });
            })
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
                var url = "{{route('vente.reglements.store')}}";
            }else{
                var methode = 'POST';
                var url = "{{route('vente.update-reglement')}}";
            }
            var formData = new FormData($(this)[0]);
            editerReglement(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idReglementSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('reglements/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });

    function updateRow(idReglement) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var reglement =_.findWhere(rows, {id: idReglement});
        $scope.$apply(function () {
            $scope.populateForm(reglement);
        });
        $("#client_id").select2('val',reglement.vente.client_id);
        $.getJSON("../crm/liste-factures-client/" + reglement.vente.client_id, function (reponse) {
            $('#vente_id').html("<option>-- Selectionner la facture --</option>");
            if(reponse.total>0){
                $.each(reponse.rows, function (index, facture) {
                    $('#contact_client').val(facture.client.contact_client);
                    $('#adresse_client').val(facture.client.adresse_client);
                    $('#vente_id').append('<option selected value=' + reglement.vente_id + '> Facture N° ' + reglement.numero_facture + '</option>');
                    $("#vente_id").val(reglement.vente_id);
                });
            }else{
                $('#contact_client').val("");
                $('#adresse_client').val("");
            }
        })
        $.getJSON("../vente/liste-ventes/" + reglement.numero_facture, function (reponse) {
            if(reponse.total>0){
                $.each(reponse.rows, function (index, vente) {
                    $('#montant_ttc').val(vente.montantTTC);
                    $('#montant_restant').val(vente.montantTTC-vente.acompte_facture)
                });
            }else{
                $('#montant_ttc').val("");
                $('#montant_restant').val("");
            }
        })

        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idReglement) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var reglement =_.findWhere(rows, {id: idReglement});
           $scope.$apply(function () {
              $scope.populateForm(reglement);
          });
       $(".bs-modal-suppression").modal("show");
    }

    function recuPrintRow(idReglement){
        window.open("recu-reglement-pdf/" + idReglement ,'_blank')
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function recuFormatter(id, row){
        return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Imprimer le reçu" onClick="javascript:recuPrintRow(' + row.id + ');"><i class="fa fa-print"></i></button>'
    }
    function objetFormatter(id, row){
        return '<span class="text-bold"> Facture N° ' + row.numero_facture + '</span>';
    }

    function imageFormatter(id, row) {
        return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function editerReglement(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                        $('#contact_client').val("");
                        $('#adresse_client').val("");
                        $('#montant_ttc').val("");
                        $('#montant_restant').val("");
                        $("#client_id").select2('val','');
                        $('#vente_id').html("<option>-- Selectionner la facture --</option>");
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

