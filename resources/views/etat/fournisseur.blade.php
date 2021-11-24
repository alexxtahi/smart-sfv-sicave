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
    <div class="col-md-4">
        <select class="form-control" id="searchByNation">
            <option value="0">-- Tous les pays --</option>
            @foreach($nations as $nation)
                @if ($nation->id == 51) <!-- Sélectionner Côte d'ivoire par défaut -->
                    <option value="{{$nation->id}}" selected> {{$nation->libelle_nation}}</option>
                @else
                    <option value="{{$nation->id}}"> {{$nation->libelle_nation}}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('crm',['action'=>'liste-fournisseurs'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="full_name_fournisseur" data-searchable="true" data-sortable="true">Nom du fournisseur </th>
            <th data-field="contact_fournisseur">Contact</th>
            <th data-field="nation.libelle_nation">Pays </th>
            <th data-field="email_fournisseur">E-mail </th>
            <th data-field="boite_postale_fournisseur">Boite postale</th>
            <th data-field="adresse_fournisseur">Adresse</th>
            <th data-field="fax_fournisseur">Fax</th>
        </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $("#searchByNation").select2({width: '100%'});
        $("#searchByNation").change(function (e) {
            var nation = $("#searchByNation").val();
            if(nation == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('crm', ['action' => 'liste-fournisseurs'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../crm/liste-fournisseurs-by-nation/' + nation});
            }
        });
    });

    function imprimePdf(){
        var nation = $("#searchByNation").val();
        if(nation == 0){
           window.open("liste-fournisseurs-pdf/",'_blank');
        }else{
           window.open("liste-fournisseurs-by-nation-pdf/" + nation,'_blank');
        }
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection

