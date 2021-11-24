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
        <select class="form-control" id="searchByCategorie">
            <option value="0">-- Toutes les cat&eacute;gories --</option>
            @foreach($categories as $categorie)
            <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">

    </div>
    <div class="col-md-4">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('stock',['action'=>'liste-articles'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="code_barre">Code barre</th>
            <th data-field="libelle_article" data-searchable="true" data-sortable="true">Article</th>
            <th data-field="categorie.libelle_categorie">Cat&eacute;gorie </th>
            <th data-field="sous_categorie.libelle_categorie">Sous cat&eacute;gorie </th>
            <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix Achat TTC</th>
            <th data-formatter="prixAchatHtFormatter">Prix Achat HT</th>
            <th data-formatter="tvaFormatter">TVA </th>
       </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });

        $("#searchByCategorie").change(function (e) {
            var categorie = $("#searchByCategorie").val();
            if(categorie==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('stock', ['action' => 'liste-articles'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../stock/liste-articles-by-categorie/' + categorie});
            }
        });

    });

    function imprimePdf(){
        var categorie = $("#searchByCategorie").val();
        if(categorie==0){
            window.open("../etat/liste-articles-pdf/" ,'_blank');
        }else{
            window.open("../etat/liste-articles-by-categorie-pdf/" + categorie,'_blank');
        }
    }

    function tvaFormatter(id, row){
        var tva = row.param_tva.tva*100;
        return '<span class="text-bold">' + tva.toFixed(2) + ' %' + '</span>';
    }

    function prixAchatHtFormatter(id, row){
        var prix_achat = row.prix_achat_ttc;
        var tva = row.param_tva.tva;
        var prix_achat_ht = (prix_achat/(tva + 1));
        var prix = Math.round(prix_achat_ht);
        return '<span class="text-bold">' +prix+ '</span>';
    }
    function montantFormatter(montant){
        return montant ? '<span class="text-bold">' + $.number(montant)+ '</span>' : "--";
    }
    function alertFormatter(id, row){
        if(row.totalStock<=row.stock_mini){
            return '<span class="label label-danger">' + $.number(row.totalStock) + '</span>';
        }else{
            return '<span class="label label-success">' +  $.number(row.totalStock) + '</span>';
        }
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
