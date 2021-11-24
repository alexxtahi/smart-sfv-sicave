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
    <div class="col-md-3">
        <select class="form-control" id="searchByFournisseur">
            <option value="0">-- Toutes les fournisseurs --</option>
            @foreach($fournisseurs as $fournisseur)
            <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        Total cr&eacute;dit : <span class="text-bold text-green h3" id="MontantTotalDu">0</span>
    </div>
    <div class="col-md-3">
        Total acompte : <span class="text-bold text-red h3" id="MontantTotalAcompte">0</span>
    </div>
    <div class="col-md-2">
        Total du : <span class="text-bold text-orange h3" id="MontantTotalRestant">0</span>
    </div>
    <div class="col-md-1">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-soldes-fournisseurs'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="numero_bon">N° Bon </th>
            <th data-field="fournisseur.full_name_fournisseur">Fournisseur </th>
            <th data-field="fournisseur.contact_fournisseur">Contact </th>
            <th data-field="montantCommande" data-formatter="montantFormatter">Cr&eacute;dit</th>
            <th data-field="accompte" data-formatter="montantFormatter">Acompte</th>
            <th data-formatter="doitFormatter">Doit</th>
       </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
            $("#MontantTotalDu").html($.number(data.MontantTotalDu));
            $("#MontantTotalAcompte").html($.number(data.MontantTotalAcompte));
            $("#MontantTotalRestant").html($.number(data.MontantTotalDu-data.MontantTotalAcompte));
        });
        $("#searchByFournisseur").select2({width: '100%'});
        $("#searchByFournisseur").change(function (e) {
            var fournisseur = $("#searchByFournisseur").val();
            if(fournisseur ==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-soldes-fournisseurs'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../crm/liste-soldes-by-fournisseurs/' + fournisseur});
            }
        });
    });

    function imprimePdf(){
        var fournisseur = $("#searchByFournisseur").val();
        if(fournisseur==0){
            window.open("../etat/liste-soldes-fournisseurs-pdf/",'_blank');
        }else{
            window.open("../etat/liste-soldes-by-fournisseurs-pdf/" + fournisseur ,'_blank');
        }
    }

    function doitFormatter(id, row){
       var doit = row.montantCommande - row.accompte;
       if(doit==0){
           return '<span class="text-bold text-green">Soldé</span>';
       }else{
           return '<span class="text-bold">' + $.number(doit)+ '</span>';
       }
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
