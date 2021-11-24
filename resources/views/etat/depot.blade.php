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
    <div class="col-md-12">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
       data-pagination="true"
       data-search="false"
       data-toggle="table"
       data-url="{{url('stock',['action'=>'liste-depots'])}}"
       data-unique-id="id"
       data-show-toggle="false">
    <thead>
        <tr>
            <th data-field="libelle_depot" data-searchable="true" data-sortable="true">Libell&eacute;  </th>
            <th data-field="adresse_depot" data-searchable="true" data-sortable="true">Adresse du d&eacute;p&ocirc;t </th>
            <th data-field="contact_depot">Contact</th>
        </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
    });

    function imprimePdf(){
        window.open("liste-depots-pdf/",'_blank');
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection



