@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">

        <table id="table" class="table table-info table-striped box box-info"
               data-pagination="true"
               data-search="true"
               data-show-columns="false"
               data-toggle="table"
               data-unique-id="token"
               data-show-toggle="false">
            <thead>
                <tr>
                    <th data-field="libelle" data-searchable="true">Donn&eacute;es</th>
                    <th data-field="full_name" data-searchable="true">Supprim&eacute; par </th>
                    <th data-field="contact" data-searchable="true">Contact </th>
                    <th data-field="role" data-searchable="true">Niveau d'acc&egrave;s </th> 
                    <th data-field="email" data-searchable="true">E-mail</th>
                    <th data-field="deleted_at" data-formatter="dateFormatter">Date de suppression</th>
                    <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center">Restaurer</th>
                </tr>
            </thead>
        </table>
<!-- Modal suppresion -->
<div class="modal fade bs-modal-restaure" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formRestaure" ng-controller="formRestaureCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Confimation de la restauration de donn&eacute;
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden"  name="id"  ng-model="rowTable.id"/>
                    <input type="text" class="hidden"  name="table" value="{{$table}}" />
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir restaurer l'enregistrement<br/><b>@{{rowTable.libelle}}</b></div>
                        <div class="text-center vertical processing">Restauration en cours</div>
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
<form>
    <input type="text" class="hidden" id="tableObtenue" value="{{$table}}">
</form>

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];
    
    appSmarty.controller('formRestaureCtrl', function ($scope) {
        $scope.populateForm = function (rowTable) {
        $scope.rowTable = rowTable;
        };
        $scope.initForm = function () {
        $scope.rowTable = {};
        };
    });
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        $table.bootstrapTable('refreshOptions', {url: '../liste_content_one_table/' + $("#tableObtenue").val()});
        
        $("#formRestaure").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            ajout = true;
            var $ajaxLoader = $("#formRestaure .loader-overlay");
            var methode = 'POST';
            var url = "{{route('auth.restaurage')}}";
            restaureAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });
    });
    
    function restaureRow(idRow) {
        ajout= true;
        var $scope = angular.element($("#formRestaure")).scope();
        var rowTable =_.findWhere(rows, {id: idRow});
        $scope.$apply(function () {
        $scope.populateForm(rowTable);
        });
        $(".bs-modal-restaure").modal("show");
    }
    
    function dateFormatter(date){
        if(date == null){
            return '-';
        }else{
           var dates = date.substr(0, 10)
           var resultat = dates.split("-");
          return  resultat[2] + "-" + resultat[1] + "-" + resultat[0] + ' à ' + date.substr(10, 9);  
        }
    }
    
    function optionFormatter(id, row) {
    return '<button class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" data-original-title="Modifier" onClick="javascript:restaureRow(' + id + ');"><i class="fa fa-check"></i></button>';
    }
    
    function restaureAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                //creation
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-restaure").modal("hide");
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
