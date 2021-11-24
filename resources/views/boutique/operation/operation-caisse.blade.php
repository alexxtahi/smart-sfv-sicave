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
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
              
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_operations">Date</th>
            <th data-field="montant_operation" data-formatter="montantFormatter">Montant</th>
            <th data-field="objet_operation">Objet</th>
            <th data-formatter="typeFormatter">Type op&eacute;ration</th>
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
                        Gestion des op&eacute;rations de caisse
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idOperationModifier" ng-hide="true" ng-model="operation.id"/>
                    @if(Auth::user()->role == 'Caissier' && $caisse_ouverte!=null)
                    <input type="text" class="hidden" id="caisse" ng-hide="true" name="caisse_id" value="{{$caisse_ouverte->caisse_id}}"/>
                    @endif
                    @if(Auth::user()->role != 'Caissier' && $caisse!=null)
                    <input type="text" class="hidden" id="caisse" ng-hide="true" name="caisse_id" value="{{$caisse->id}}"/>
                    @endif
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-6">
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
                    <input type="text" class="hidden" id="idOperationSupprimer"  ng-model="operation.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'operation <br/><b>@{{operation.objet_operation}}</b></div>
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
        $scope.populateForm = function (operation) {
            $scope.operation = operation;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.operation = {};
        };
    });
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (operation) {
            $scope.operation = operation;
        };
        $scope.initForm = function () {
            $scope.operation = {};
        };
    });
    
    $(function () {
        var caisse = $("#caisse").val();
        $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-operations-by-caisse/' + caisse});
        
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
        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ''){
                 $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-operations-by-caisse/' + caisse});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-operations-by-caisse-date/' + caisse + '/' + date});
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

            if (ajout==true) {
                var methode = 'POST';
                var url = "{{route('boutique.operations.store')}}";
             }else{
                var id = $("#idOperationModifier").val();
                var methode = 'PUT';
                var url = 'operations/' + id;
             }
            editerOperation(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idOperationSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('operations/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    function updateRow(idOperation) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var operation =_.findWhere(rows, {id: idOperation});
        $scope.$apply(function () {
            $scope.populateForm(operation);
        });
        $(".bs-modal-ajout").modal("show");
    }
    
    function deleteRow(idOperation) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var operation =_.findWhere(rows, {id: idOperation});
           $scope.$apply(function () {
              $scope.populateForm(operation);
          });
       $(".bs-modal-suppression").modal("show");
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    
    function typeFormatter(id, row){
        if(row.type_operation=='entree'){
                return "<span class='text-bold text-green'>Entrée d'argent</span>";
        }else{
                return "<span class='text-bold text-red'>Sortie d'argent</span>";
        }
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function editerOperation(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
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

@endsection



