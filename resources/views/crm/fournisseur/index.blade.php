@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Comptable')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
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
        <a href="{{route('crm.fournisseur.dowload_model')}}" role="button" class="btn btn-sm btn-secondary pull-right" style="background:#F9F9F9">
            <i class="fa fa-upload"></i>&nbsp; Télécharger le modèle
        </a>
    </h1>
</div>
<div class="col-md-4">
    <select class="form-control" id="searchByNation">
        <option value="0">-- Tous les pays --</option>
        @foreach($nations as $nation)
            <option value="{{$nation->id}}"> {{$nation->libelle_nation}}</option>
        @endforeach
    </select>
</div>
-->
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="true"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-fournisseurs'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="true">
    <thead>
        <tr>
            <th data-field="code_fournisseur">Code </th>
            <th data-field="full_name_fournisseur" data-searchable="true" data-sortable="true">Nom du fournisseur </th>
            <th data-field="contact_fournisseur">Contact</th>
            <th data-field="nation.libelle_nation">Pays </th>
            <th data-field="banque.libelle_banque">Banque </th>
            <th data-field="compte_banque_fournisseur">Compte banque </th>
            <th data-field="email_fournisseur" data-formatter="mailFormatter">E-mail </th>
            <th data-field="boite_postale_fournisseur" data-visible="false">Boite postale</th>
            <th data-field="adresse_fournisseur" data-visible="false">Adresse</th>
            <th data-field="fax_fournisseur">Fax</th>
            <th data-field="compte_contribuable_fournisseur">Compte contr.</th>
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
                        <i class="fa fa-truck fa-2x"></i>
                        Gestion des fournisseurs
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" name="id" ng-hide="true" ng-model="fournisseur.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom complet ou raison sociale du fournisseur *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="fournisseur.full_name_fournisseur" id="full_name_fournisseur" name="full_name_fournisseur" placeholder="Nom & prénom(s) ou raison sociale du fournisseur" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>N° du compte contribuable</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="fournisseur.compte_contribuable_fournisseur" id="compte_contribuable_fournisseur" name="compte_contribuable_fournisseur" placeholder="N° du compte contribuable du fournisseur">
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
                                    <input type="text" class="form-control bfh-phone" ng-model="fournisseur.contact_fournisseur" id="contact_fournisseur" name="contact_fournisseur" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact du fournisseur" required>
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
                                    <input type="email" class="form-control" ng-model="fournisseur.email_fournisseur" id="email_fournisseur" name="email_fournisseur" placeholder="Adresse mail du client">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>Pays du fournisseur *</label>
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
                                <label>Adresse postale du fournisseur </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="fournisseur.boite_postale_fournisseur" id="adresse_fournisseur" name="boite_postale_fournisseur" placeholder="Adresse du boite postale de fournisseur">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>Banque </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="banque_id" id="banque_id" ng-model="fournisseur.banque_id" class="form-control">
                                        <option value="" ng-show="false">-- Selectionner la banque --</option>
                                        @foreach($banques as $banque)
                                        <option value="{{$banque->id}}"> {{$banque->libelle_banque}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Compte banque </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="fournisseur.compte_banque_fournisseur" id="compte_banque_fournisseur" name="compte_banque_fournisseur" placeholder="Compte banque du fournisseur">
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
                                    <input type="text" class="form-control bfh-phone" ng-model="fournisseur.fax_fournisseur" id="fax_fournisseur" name="fax_fournisseur" data-format="dd-dd-dd-dd" pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Numéro fax du fournisseur">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse g&eacute;ographique du fournisseur </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);"  class="form-control" ng-model="fournisseur.adresse_fournisseur" id="adresse_fournisseur" name="adresse_fournisseur" placeholder="Adresse géographique du fournisseur">
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
                    <input type="text" class="hidden" id="idFournisseurSupprimer"  ng-model="fournisseur.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le fournisseur <br/><b>@{{fournisseur.full_name_fournisseur}}</b></div>
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



<x-modal-creation-en-masse action="{{route('crm.fournisseur.store_from_upload')}}"/>


<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (fournisseur) {
            $scope.fournisseur = fournisseur;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.fournisseur = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (fournisseur) {
            $scope.fournisseur = fournisseur;
        };
        $scope.initForm = function () {
            $scope.fournisseur = {};
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
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-fournisseurs'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../crm/liste-fournisseurs-by-nation/' + nation});
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
            var url = "{{route('crm.fournisseurs.store')}}";

            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idFournisseurSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('fournisseurs/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });

    function updateRow(idFournisseur) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var fournisseur =_.findWhere(rows, {id: idFournisseur});
         $scope.$apply(function () {
            $scope.populateForm(fournisseur);
        });
        $("#nation_id").select2("val", fournisseur.nation_id);
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idFournisseur) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var fournisseur =_.findWhere(rows, {id: idFournisseur});
           $scope.$apply(function () {
              $scope.populateForm(fournisseur);
          });
       $(".bs-modal-suppression").modal("show");
    }
    function ficheRow(idFournisseur){
        window.open("../crm/fiche-fournisseur/" + idFournisseur,'_blank');
    }
    function mailFormatter(mail){
        return mail ? '<a href="mailto:' + mail + '">' + mail + '</a>' : '-';
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
         <button class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Détails du fournisseur" onClick="javascript:ficheRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


