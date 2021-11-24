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
    <div class="col-md-6">
        <label>Voir la liste sur une p&eacute;riode</label>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <input type="text" class="form-control" id="dateDebut" placeholder="Date du début">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <input type="text" class="form-control" id="dateFin" placeholder="Date de fin">
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
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-approvisionnements'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_approvisionnements">Date </th>
            <th data-formatter="depotFormatter">D&eacute;p&ocirc;t </th>
            <th data-field="fournisseur.full_name_fournisseur">Fournisseur </th>
        </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $('#dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr'
        });
        $("#searchByFournisseur").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var fournisseur = $("#searchByFournisseur").val();
            if(fournisseur == 0 && dateDebut=="" && dateFin == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-approvisionnements'])}}"});
            }
            if(fournisseur != 0 && dateDebut=="" && dateFin == ""){
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-approvisionnements-by-fournisseur/' + fournisseur});
            }
            if(fournisseur == 0 && dateDebut!="" && dateFin != ""){
                $table.bootstrapTable('refreshOptions', {url: '../stock/liste-approvisionnements-by-periode/' + dateDebut + '/' + dateFin});
              }
            if(fournisseur != 0 && dateDebut!="" && dateFin != ""){
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-approvisionnements-by-periode-fournisseur/' + dateDebut + '/' + dateFin + '/' + fournisseur});
            }
        });
        $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByFournisseur").val(0);
            if(dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../stock/liste-approvisionnements-by-periode/' + dateDebut + '/' + dateFin});
            }else{
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-approvisionnements'])}}"});
            }
        });
    });

    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var fournisseur = $("#searchByFournisseur").val();
        if(dateDebut=='' && dateFin=='' && fournisseur!=0){
            window.open("liste-approvisionnements-by-fournisseur-pdf/" + fournisseur,'_blank');
        }
        if(dateDebut!='' && dateFin!='' && fournisseur==0){
            window.open("liste-approvisionnements-by-periode-pdf/" + dateDebut + '/' + dateFin ,'_blank');
        }
        if(fournisseur == 0 && dateDebut=="" && dateFin == ""){
            window.open("liste-approvisionnements-pdf/" ,'_blank');
        }
        if(fournisseur != 0 && dateDebut!="" && dateFin != ""){
            window.open("liste-approvisionnements-by-periode-fournisseur-pdf/" + dateDebut + '/' + dateFin + '/' + fournisseur ,'_blank');
        }
    }
    function depotFormatter(id, row){
        return row.depot.libelle_depot;
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
