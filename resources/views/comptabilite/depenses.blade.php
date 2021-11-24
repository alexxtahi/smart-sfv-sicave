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
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <input type="text" class="form-control" id="dateDebut" placeholder="Date du début">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <input type="text" class="form-control" id="dateFin" placeholder="Date de fin">
        </div>
    </div>
    <div class="col-md-4">
        <select class="form-control" id="searchByCategorie">
            <option value="0">-- Toutes les cat&eacute;gories --</option>
            @foreach($categorie_depenses as $categorie)
            <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie_depense}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <a class="btn btn-success pull-left" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('comptabilite',['action'=>'liste-depenses'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
           <th data-field="date_operations">Date</th>
           <th data-field="categorie_depense.libelle_categorie_depense" data-sortable="true">Libell&eacute;</th>
            <th data-field="description">Description </th>
            <th data-field="montant_depense" data-formatter="montantFormatter">Montant</th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
       </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 65%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-users fa-2x"></i>
                        Gestion des d&eacute;penses
                    </span>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" id="id" name="id" ng-hide="true" ng-model="depense.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="depense.date_operations" id="date_operation" name="date_operation" value="<?=date('d-m-Y');?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Libell&eacute; *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select name="categorie_depense_id" id="categorie_depense_id" ng-model="depense.categorie_depense_id" class="form-control" required>
                                        <option value="">-- Sectionner le libell&eacute; --</option>
                                        @foreach($categorie_depenses as $categorie)
                                        <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie_depense}}</option>
                                        @endforeach
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
                                    <input type="text" pattern="[0-9]*" class="form-control" ng-model="depense.montant_depense" id="montant_depense" name="montant_depense" placeholder="Montant" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea rows="3" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="depense.description" id="description" name="description" placeholder="Votre text...."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
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
                    <input type="text" class="hidden" id="idDepenseSupprimer"  ng-model="depense.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer <br/><b>@{{depense.categorie_depense.libelle_categorie_depense + ' du ' + depense.date_operations}}</b></div>
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
        $scope.populateForm = function (depense) {
            $scope.depense = depense;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.depense = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (depense) {
            $scope.depense = depense;
        };
        $scope.initForm = function () {
            $scope.depense = {};
        };
    });

    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $('#date_operation, #dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate:new Date()
        });

        $("#searchByCategorie").change(function (e) {
            var categorie = $("#searchByCategorie").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();

            if(categorie == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'liste-depenses'])}}"});
            }

            if(categorie != 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-depenses/' + categorie});
            }

            if(categorie == 0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-depenses-by-periode/' + dateDebut + "/" + dateFin});
            }

            if(categorie != 0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-depenses-by-categorie-periode/' + categorie + '/' + dateDebut + '/' + dateFin});
            }
        });

        $("#dateDebut, #dateFin").change(function (e) {
            var categorie = $("#searchByCategorie").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();

            if(categorie == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'liste-depenses'])}}"});
            }

            if(categorie != 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-depenses/' + categorie});
            }

            if(categorie == 0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-depenses-by-periode/' + dateDebut + "/" + dateFin});
            }

            if(categorie != 0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-depenses-by-categorie-periode/' + categorie + '/' + dateDebut + '/' + dateFin});
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
            var url = "{{route('comptabilite.depenses.store')}}";

            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idDepenseSupprimer").val();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('depenses/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });

    function updateRow(idDepense) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var depense =_.findWhere(rows, {id: idDepense});
         $scope.$apply(function () {
            $scope.populateForm(depense);
        });
        $(".bs-modal-ajout").modal("show");
    }

    function deleteRow(idDepense) {
        var $scope = angular.element($("#formSupprimer")).scope();
        var depense =_.findWhere(rows, {id: idDepense});
        $scope.$apply(function () {
            $scope.populateForm(depense);
        });
        $(".bs-modal-suppression").modal("show");
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }

    function imprimePdf(){
        var categorie = $("#searchByCategorie").val();
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();

        if(categorie == 0 && dateDebut=="" && dateFin==""){
            window.open("liste-depenses-pdf/" ,'_blank');
        }
        if(categorie != 0 && dateDebut=="" && dateFin==""){
            window.open("liste-depenses-by-categorie-pdf/" + categorie ,'_blank');
        }

        if(categorie == 0 && dateDebut!="" && dateFin!=""){
            window.open("liste-depenses-by-periode-pdf/" + dateDebut + "/" +  dateFin,'_blank');
        }

        if(categorie != 0 && dateDebut!="" && dateFin!=""){
            window.open("liste-depenses-by-periode-categorie-pdf/" + dateDebut + "/" +  dateFin + "/" + categorie,'_blank');
        }
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
