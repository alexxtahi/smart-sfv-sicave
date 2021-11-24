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
<!--div class="col-md-3">
    <div class="form-group">
        <div class="radio">
            <label>
                <input type="checkbox" id="compteClient" onchange="compteClient(this)"/>&nbsp;Comptes clients
            </label>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <div class="radio">
            <label>
                <input type="checkbox" id="compteCarte" onchange="compteCarte(this)"/>&nbsp;Comptes carte de fid&eacute;lit&eacute;
            </label>
        </div>
    </div>
</div-->

<div class="col-md-12">
 <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="true"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-comptes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="numero_compte" data-searchable="true">N° du compte </th>
            <th data-formatter="typeFormatter">Type </th>
            <th data-formatter="detenteurFormatter">D&eacute;tenteur </th>
            <th data-formatter="soldeFormatter">Solde </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="50px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

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


<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (compte) {
            $scope.compte = compte;
        };
        $scope.initForm = function () {
            $scope.compte = {};
        };
    });

    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
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

    function deleteRow(idCompte) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var compte =_.findWhere(rows, {id: idCompte});
           $scope.$apply(function () {
              $scope.populateForm(compte);
          });
       $(".bs-modal-suppression").modal("show");
    }

    function compteClient(checkboxElem) {
        $("#compteCarte").prop("checked", false);

        if (checkboxElem.checked) {
            $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-compte-clients'])}}"});
        }else{
            $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-comptes'])}}"});
        }
    }
    function compteCarte(checkboxElem) {
        $("#compteClient").prop("checked", false);

        if (checkboxElem.checked) {
            $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-compte-carte'])}}"});
        }else{
            $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-comptes'])}}"});
        }
    }
    function compteFournisseur(checkboxElem) {
        $("#compteClient").prop("checked", false);
        $("#compteCarte").prop("checked", false);

        if (checkboxElem.checked) {
            $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-compte-fournisseur'])}}"});
        }else{
            $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-comptes'])}}"});
        }
    }

    function imprimePdf(){
        var carte = document.getElementById("compteCarte");
        var client = document.getElementById("compteClient");


        if(!carte.checked && !client.checked){
            window.open("liste-compte-pdf", '_blank');
        }
        if(carte.checked && !client.checked){
            window.open("liste-compte-carte-pdf/", '_blank');
        }
        if(!carte.checked && client.checked){
            window.open("liste-compte-client-pdf/", '_blank');
        }
    }
    function typeFormatter(id, row){
        if(row.client_id && !row.carte_id){
            return '<span class="text-bold">Compte client</span>';
        }
        if(row.fournisseur_id){
            return '<span class="text-bold">Compte fournisseur</span>';
        }
        if(row.carte_id){
            return '<span class="text-bold">Compte lié à la carte de fidélité ' + row.carte.libelle_carte_fidelite + ' </span>';
        }
    }
    function detenteurFormatter(id, row){
        if(row.client_id && !row.carte_id){
            return "<span class='text-bold'>" + row.client.full_name_client + "</span>";
        }
        if(row.fournisseur_id){
            return "<span class='text-bold'>" + row.fournisseur.full_name_fournisseur + "</span>";
        }
        if(row.carte_id){
            return "<span class='text-bold'>" + row.client.full_name_client  + "</span>";
        }
    }
    function soldeFormatter(id, row){
        var montant = row.entree - row.sortie;

        return montant > 0 ? "<span class='text-bold text-green'>" + $.number(montant)  + "</span>" : "<span class='text-bold text-red'>" + montant  + "</span>";
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


