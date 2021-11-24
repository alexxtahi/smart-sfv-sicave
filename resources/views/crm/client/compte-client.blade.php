@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Comptable')
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
<div class="col-md-4">
    <select class="form-control" id="searchByClient">
        <option value="0">-- Tous les clients --</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
        @endforeach
    </select>
</div>

<div class="col-md-4">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>

<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="true"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-compte-clients'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="id" data-formatter="rechargeFormatter" data-width="50px" data-align="center"><i class="fa fa-plus"></i></th>
            <th data-field="numero_compte" data-searchable="true">N° du compte </th>
            <th data-field="client.full_name_client" data-sortable="true">Nom du client </th>
            <th data-formatter="soldeFormatter">Solde </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="70px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-truck fa-2x"></i>
                        Gestion des comptes clients
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" name="id" ng-hide="true" ng-model="compte.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-flag"></i>
                                    </div>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le client --</option>
                                        @foreach($clients as $client)
                                            <option value="{{$client->id}}"> {{$client->code_client.' - '.$client->full_name_client}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                    <input type="text" class="hidden" id="idCompteSupprimer"  ng-model="compte.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le compte <br/><b>@{{compte.numero_compte}}</b> du client <b>@{{compte.client.full_name_client}}</b></div>
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

<!-- Modal rechargement -->
<div class="modal fade bs-modal-recharge" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formRecharge" ng-controller="formRechargeCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Rechargement de carte
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" name="id"  ng-model="compte.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="text-left">N° du compte : <b>@{{compte.numero_compte}}</b></div>
                        </div>
                        <div class="col-md-7">
                            <div class="text-left">Client : <b>@{{compte.client.code_client + ' - ' + compte.client.full_name_client}}</b></div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Montant de recharge *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" id="montant" name="montant" placeholder="Montant à recharger" required>
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

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (compte) {
            $scope.compte = compte;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.compte = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (compte) {
            $scope.compte = compte;
        };
        $scope.initForm = function () {
            $scope.compte = {};
        };
    });

    appSmarty.controller('formRechargeCtrl', function ($scope) {
        $scope.populateForm = function (compte) {
            $scope.compte = compte;
        };
    });

    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $("#client_id, #searchByClient").select2({width: '100%'});

        $("#searchByClient").change(function (e) {
            var client = $("#searchByClient").val();

            if(client == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-compte-clients'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../crm/liste-compte-clients-by-client/' + client});
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
            var url = "{{route('crm.comptes.store')}}";

            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formRecharge").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formRecharge .loader-overlay");

            var methode = 'POST';
            var url = "{{route('crm.recharge.compte')}}";

            rechargeAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idCompteSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('comptes/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });

    function imprimePdf(){
        //alert('Client id = ' + $("#searchByClient").val());
        var clientId = $("#searchByClient").val();
        if (clientId == 0) {
            window.open("liste-compte-client-pdf", '_blank');
        } else {
            window.open("liste-compte-un-client-pdf/" + clientId, '_blank');
        }
    }

    function updateRow(idCompte) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var compte =_.findWhere(rows, {id: idCompte});
         $scope.$apply(function () {
            $scope.populateForm(compte);
        });
        $("#client_id").select2("val", compte.client_id);
        $(".bs-modal-ajout").modal("show");
    }

    function rechargeRow(idCompte) {
        ajout= false;
        var $scope = angular.element($("#formRecharge")).scope();
        var compte =_.findWhere(rows, {id: idCompte});
         $scope.$apply(function () {
            $scope.populateForm(compte);
        });
        $("#montant").val("");
        $(".bs-modal-recharge").modal("show");
    }

    function deleteRow(idCompte) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var compte =_.findWhere(rows, {id: idCompte});
           $scope.$apply(function () {
              $scope.populateForm(compte);
          });
       $(".bs-modal-suppression").modal("show");
    }

    function soldeFormatter(id, row){
        var montant = row.entree - row.sortie;
        return montant >= 0 ? "<span class='text-bold text-green'>" + $.number(montant)  + "</span>" : "<span class='text-bold text-red'>" + montant  + "</span>";
    }

    function rechargeFormatter(id, row) {
        return '<button class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Recharger le compte" onClick="javascript:rechargeRow(' + id + ');"><i class="fa fa-plus"></i></button>';
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }


    function rechargeAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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

                    $table.bootstrapTable('refresh');
                    $(".bs-modal-recharge").modal("hide");
                    document.forms["formRecharge"].reset();
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


