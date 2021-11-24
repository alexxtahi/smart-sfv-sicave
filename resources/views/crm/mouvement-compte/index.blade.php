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
    <select class="form-control" id="searchByCompte">
        <option value="0">-- Tous les comptes --</option>
        @foreach($comptes as $compte)
        <option value="{{$compte->id}}"> {{$compte->numero_compte}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <div class="form-group">
        <input type="text" class="form-control" id="searchByDate" placeholder="Chercher par date"/>
    </div>
</div>

<div class="col-md-5">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>

<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-mouvements-comptes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="compte.numero_compte" data-searchable="true">N° du compte </th>
            <th data-field="date_mouvements">Date </th>
            <th data-field="initiale" data-formatter="montantFormatter">Initial </th>
            <th data-field="entree" data-formatter="montantFormatter">Entr&eacute; </th>
            <th data-field="sortie" data-formatter="montantFormatter">Sortie </th>
            <th data-formatter="soldeFormatter">Solde </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        Gestion de mouvement du compte N° : @{{ mouvement.compte.numero_compte }} survenu le @{{mouvement.date_mouvements}}
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" name="id" ng-hide="true" ng-model="mouvement.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Initial </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" id="initiale" ng-model="mouvement.initiale" name="initiale" placeholder="Montant initial">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Entr&eacute;e </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" id="entree" ng-model="mouvement.entree" name="entree" placeholder="Montant rechargé">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sortie </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" id="sortie" ng-model="mouvement.sortie" name="sortie" placeholder="Montant dépensé">
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
                    <input type="text" class="hidden" id="idMouvementSupprimer"  ng-model="mouvement.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le mouvement du <br/><b>@{{mouvement.date_mouvements}}</b> du compte <b>@{{mouvement.compte.numero_compte}}</b></div>
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

    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (mouvement) {
            $scope.mouvement = mouvement;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.mouvement = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (mouvement) {
            $scope.mouvement = mouvement;
        };
        $scope.initForm = function () {
            $scope.mouvement = {};
        };
    });

    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $('#searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });

        $("#searchByCompte").select2({width: '100%'});

        $("#searchByCompte").change(function (e) {
            var compte = $("#searchByCompte").val();
            if(compte == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-mouvements-comptes'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../crm/liste-mouvements-comptes-by-compte/' + compte});
            }
        });

        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-mouvements-comptes'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../crm/liste-mouvements-comptes-by-date/' + date});
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
            var url = "{{route('crm.mouvements.store')}}";

            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idMouvementSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('mouvements/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });

    function imprimePdf(){
        window.open("liste-mouvements-comptes-pdf", '_blank');
    }

    function updateRow(idMouvement) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var mouvement =_.findWhere(rows, {id: idMouvement});
         $scope.$apply(function () {
            $scope.populateForm(mouvement);
        });
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idMouvement) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var mouvement =_.findWhere(rows, {id: idMouvement});
           $scope.$apply(function () {
              $scope.populateForm(mouvement);
          });
       $(".bs-modal-suppression").modal("show");
    }
    function soldeFormatter(id, row){
        if(row.sortie){
            var solde = row.initiale - row.sortie
            return solde > 0 ? "<span class='text-bold text-green'>" + $.number(solde) + "</span>" : "<span class='text-bold text-red'>" + solde + "</span>";
        }
        if(row.entree){
            var solde = row.initiale + row.entree
            return solde > 0 ? "<span class='text-bold text-green'>" + $.number(solde) + "</span>" : "<span class='text-bold text-red'>" + solde + "</span>";
        }
    }
    function montantFormatter(montant){
        return "<span class='text-bold'>" + $.number(montant) + "</span>";
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


