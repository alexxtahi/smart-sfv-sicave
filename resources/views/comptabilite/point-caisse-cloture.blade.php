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
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Toutes les d&eacute;p&ocirc;ts --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('comptabilite',['action'=>'liste-caisses-cloturees'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="libelle_depot">D&eacute;p&ocirc;t</th>
            <th data-field="libelle_caisse">Caisse</th>
            <th data-field="date_ouvertures">Ouverture</th>
            <th data-field="date_fermetures">Fermeture</th>
            <th data-field="montant_ouverture">Montant Ouver.</th>
            <th data-formatter="entreeFormatter">Entr&eacute;e</th>
            <th data-field="sortie">Sortie</th>
            <th data-formatter="soldeFormatter">Solde</th>
            <th data-field="full_name">Caissier </th>
            <th data-formatter="printFormatter" data-width="60" data-align="center">Imprimer </th>
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

         $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();

            if(depot == 0 && dateDebut == '' && dateFin == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'liste-caisses-cloturees'])}}"});
            }
            if(depot !=0 && dateDebut == '' && dateFin == ''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-caisses-cloturees-by-depot/' + depot});
            }
            if(depot ==0 && dateDebut != '' && dateFin != ''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-caisses-cloturees-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot !=0 && dateDebut != '' && dateFin != ''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-caisses-cloturees-by-periode-depot/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });
        $("#searchByDepot").change(function (e) {
             var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();

            if(depot == 0 && dateDebut == '' && dateFin == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'liste-caisses-cloturees'])}}"});
            }
            if(depot !=0 && dateDebut == '' && dateFin == ''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-caisses-cloturees-by-depot/' + depot});
            }
            if(depot ==0 && dateDebut != '' && dateFin != ''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-caisses-cloturees-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot !=0 && dateDebut != '' && dateFin != ''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-caisses-cloturees-by-periode-depot/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });
    });

    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var depot = $("#searchByDepot").val();

        if(depot==0 && dateDebut=='' && dateFin==''){
            window.open("../comptabilite/liste-caisses-cloturees-pdf/",'_blank');
        }
        if(depot!=0 && dateDebut=='' && dateFin==''){
            window.open("../comptabilite/liste-caisses-cloturees-by-depot-pdf/" + depot,'_blank');
        }
        if(depot==0 && dateDebut!='' && dateFin!=''){
            window.open("../comptabilite/liste-caisses-cloturees-by-periode-pdf/" + dateDebut + '/' + dateFin,'_blank');
        }
        if(depot!=0 && dateDebut!='' && dateFin!=''){
            window.open("../comptabilite/liste-caisses-cloturees-by-periode-depot-pdf/" + dateDebut + '/' + dateFin + '/' + depot,'_blank');
        }
    }

    function printRow(caisseId){
        window.open("../vente/billetage-pdf/" + caisseId ,'_blank');
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function entreeFormatter(id, row){
        var montant  = row.sommeTotale+row.entree;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function soldeFormatter(id, row){
        var montant = row.montant_ouverture+row.sommeTotale+row.entree - row.sortie;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function printFormatter(id,row){
        return '<button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
