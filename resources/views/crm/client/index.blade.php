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
<!--
<div class="col-md-12" style="margin-bottom: 10px;padding-right:0">
    <h1>
        <button type="button" data-toggle="modal" data-target="#ModalImportationEnMasse" class="btn btn-sm btn-primary pull-right" style="margin-left:10px">
            <i class="fa fa-download"></i>&nbsp; Ajout en masse
        </button>
        <a href="{{route('crm.client.dowload_model')}}" role="button" class="btn btn-sm btn-secondary pull-right" style="background:#F9F9F9">
            <i class="fa fa-upload"></i>&nbsp; Télécharger le modèle
        </a>
    </h1>
</div>
<div class="col-md-4">
    <select class="form-control" id="searchByNation">
        <option value="0">-- Tous les pays --</option>
        @foreach($nations as $nation)
            @if ($nation->id == 51)
                <option value="{{$nation->id}}" selected> {{$nation->libelle_nation}}</option>
            @else
                <option value="{{$nation->id}}"> {{$nation->libelle_nation}}</option>
            @endif
        @endforeach
    </select>
</div>
-->
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="true"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-clients'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="true">
    <thead>
        <tr>
            <th data-field="code_client">Code </th>
            <th data-field="full_name_client" data-searchable="true" data-sortable="true">Nom du client </th>
            <th data-field="contact_client">Contact</th>
            <th data-field="nation.libelle_nation">Pays </th>
            <th data-field="regime.libelle_regime">R&eacute;gime </th>
            <th data-field="email_client" data-formatter="mailFormatter">E-mail </th>
            <th data-field="adresse_client">Adresse</th>
            <th data-field="plafond_client" data-formatter="montantFormatter">Montant plafond</th>
            <th data-field="compte_contribuable_client">Compte contr.</th>
            <th data-field="boite_postale_client" data-visible="false">Boite postale</th>
            <th data-field="fax_client" data-visible="false">Fax</th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
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
                        <i class="fa fa-shopping-cart fa-2x"></i>
                        Gestion des clients
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" name="id" ng-hide="true" ng-model="client.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom complet ou raison sociale du client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="client.full_name_client" id="full_name_client" name="full_name_client" placeholder="Nom & prénom(s) ou raison sociale du client" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>R&eacute;gime </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-circle-o"></i>
                                    </div>
                                    <select name="regime_id" id="regime_id" ng-model="client.regime_id" class="form-control">
                                        <option value="" ng-show="false">-- Selectionner le r&eacute;gime --</option>
                                        @foreach($regimes as $regime)
                                        <option value="{{$regime->id}}"> {{$regime->libelle_regime}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="client.contact_client" id="contact_client" name="contact_client" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact du client" required>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" ng-model="client.email_client" id="email_client" name="email_client" placeholder="Adresse mail du client">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>Pays du client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-flag"></i>
                                    </div>
                                    <select name="nation_id" id="nation_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le pays --</option>
                                        @foreach($nations as $nation)
                                            @if ($nation->id == 51) <!-- Sélectionner Côte d'ivoire par défaut -->
                                                <option value="{{$nation->id}}" selected> {{$nation->libelle_nation}}</option>
                                            @else
                                                <option value="{{$nation->id}}"> {{$nation->libelle_nation}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse postale du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="client.boite_postale_client" id="adresse_client" name="boite_postale_client" placeholder="Adresse du boite postale de client">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Fax </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-fax"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="client.fax_client" id="fax_client" name="fax_client" data-format="dd-dd-dd-dd" pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Numéro fax du client">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse g&eacute;ographique du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);"  class="form-control" ng-model="client.adresse_client" id="adresse_client" name="adresse_client" placeholder="Adresse géographique du client">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Compte contribuable </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-copy"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="client.compte_contribuable_client" id="compte_contribuable_client" name="compte_contribuable_client" placeholder="Numéro du compte contribuable">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Montant plafond du client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" ng-model="client.plafond_client" id="plafond_client" name="plafond_client" placeholder="Montant du plafond">
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
                    <input type="text" class="hidden" id="idClientSupprimer"  ng-model="client.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le client <br/><b>@{{client.full_name_client}}</b></div>
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



<x-modal-creation-en-masse action="{{route('crm.client.store_from_upload')}}"/>


<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (client) {
            $scope.client = client;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.client = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (client) {
            $scope.client = client;
        };
        $scope.initForm = function () {
            $scope.client = {};
        };
    });

    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $("#nation_id,#searchByNation").select2({width: '100%'});

        $("#searchByNation").change(function (e) {
            var nation = $("#searchByNation").val();
            if(nation == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-clients'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../crm/liste-clients-by-nation/' + nation});
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
            var url = "{{route('crm.clients.store')}}";

            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idClientSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('clients/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });

    });

    function updateRow(idClient) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var client =_.findWhere(rows, {id: idClient});
         $scope.$apply(function () {
            $scope.populateForm(client);
        });
        $("#nation_id").select2("val", client.nation_id);
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idClient) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var client =_.findWhere(rows, {id: idClient});
           $scope.$apply(function () {
              $scope.populateForm(client);
          });
       $(".bs-modal-suppression").modal("show");
    }

    function detailClientRow(idClient){
        window.open("../crm/fiche-client/" + idClient,'_blank');
    }
    function mailFormatter(mail){
        return mail ? '<a href="mailto:' + mail + '">' + mail + '</a>' : '-';
    }
    function montantFormatter(montant){
        return montant ? '<span class="text-bold">' + $.number(montant)+ '</span>' : "--";
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Détails du client" onClick="javascript:detailClientRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


