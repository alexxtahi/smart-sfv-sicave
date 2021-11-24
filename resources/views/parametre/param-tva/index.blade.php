@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Comptable')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">

<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <input type="text" name="id" ng-model="paramTva.id" ng-hide="true" class="hidden">
            @csrf
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$titleControlleur}}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="tva">Libell&eacute;</label>
                        <input type="number" step="0.01" id="tva" name="tva" ng-model="paramTva.tva_convertis" class="form-control" placeholder="Pourcentage de la TVA   Exemple : 18" required>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-right">
                    <span class="loader"></span>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Valider</button>
                </div>
                <!-- /.box-footer-->
                <div class="overlay loader-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
            <!-- /.box -->
        </form>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">
        <table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('parametre',['action'=>'liste-param-tva'])}}"
               data-unique-id="id"
               data-show-toggle="false">
            <thead>
                <tr>
                    <th data-field="tva_convertis" data-formatter="tvaFormatter">Montant TVA  </th>
                    <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                </tr>
            </thead>
        </table>
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
                    <input type="text" class="hidden" id="idParamTvaSupprimer"  ng-model="paramTva.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer la TVA <br/><b>@{{paramTva.tva_convertis}}%</b></div>
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
        $scope.populateForm = function (paramTva) {
            $scope.paramTva = paramTva;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.paramTva = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (paramTva) {
            $scope.paramTva = paramTva;
        };
        $scope.initForm = function () {
            $scope.paramTva = {};
        };
    });

    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
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
            var url = "{{route('parametre.param-tva.store')}}";

            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idParamTvaSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('param-tva/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });

    });

    function updateRow(idParamTva) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var paramTva =_.findWhere(rows, {id: idParamTva});
         $scope.$apply(function () {
            $scope.populateForm(paramTva);
        });
    }

    function deleteRow(idParamTva) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var paramTva =_.findWhere(rows, {id: idParamTva});
           $scope.$apply(function () {
              $scope.populateForm(paramTva);
          });
       $(".bs-modal-suppression").modal("show");
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function tvaFormatter(montant){
        return montant.toFixed(2) + ' %';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


