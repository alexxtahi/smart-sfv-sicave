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
        <select class="form-control" id="searchByArticle">
            <option value="0">-- Tous les articles --</option>
            @foreach($articles as $article)
            <option value="{{$article->id}}"> {{$article->description_article}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Toues les d&eacute;p&ocirc;t --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-mouvements-stocks-grouper'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
           <th data-field="article.code_barre">Code barre</th>
            <th data-field="article.description_article" data-searchable="true" data-sortable="true">Article</th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t</th>
            <th data-align="center" data-field="sommeQuantiteInitiale">Qt&eacute; initiale </th>
            <th data-align="center" data-field="sommeQuantiteApprovisionnee">Qt&eacute; appro. </th>
            <th data-align="center" data-field="sommeQuantiteDestocker">Qt&eacute; d&eacute;stock. </th>
            <th data-align="center" data-field="sommeQuantiteTransferee">Qt&eacute; transf. </th>
            <th data-align="center" data-field="sommeQuantiteVendue">Qt&eacute; vendue </th>
            <!--<th data-align="center" data-formatter="totalStockFormatter">En stock </th>-->
       </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });

        $("#searchByArticle").select2({width: '100%', allowClear: true});
        $("#searchByArticle").change(function (e) { 
            var article = $("#searchByArticle").val();
            var depot = $("#searchByDepot").val();
            if(article ==0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-mouvements-stocks-grouper'])}}"});
            }
            if(article !=0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-grouper-by-depot-article/' + depot + '/' + article});
            }
            if(article !=0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-grouper-by-article/' + article});
            }
            if(article ==0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-grouper-by-depot/'+ depot});
            }
        });
        $("#searchByDepot").change(function (e) { 
            var article = $("#searchByArticle").val();
            var depot = $("#searchByDepot").val();
            if(article ==0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-mouvements-stocks-grouper'])}}"});
            }
            if(article !=0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-grouper-by-depot-article/' + depot + '/' + article});
            }
            if(article !=0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-grouper-by-article/' + article});
            }
            if(article ==0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-grouper-by-depot/'+ depot});
            }
        });
    });
    
    function imprimePdf(){
        var article = $("#searchByArticle").val();
        var depot = $("#searchByDepot").val();
        
        if(article ==0 && depot==0){
            window.open("mouvements-stocks-grouper-pdf/" ,'_blank');
        }
        if(article !=0 && depot==0){
            window.open("mouvements-stocks-grouper-by-article-pdf/" + article,'_blank');  
        }
        if(article ==0 && depot!=0){
            window.open("mouvements-stocks-grouper-by-depot-pdf/" + depot,'_blank');  
        }
        if(article !=0 && depot!=0){
            window.open("mouvements-stocks-grouper-by-depot-article-pdf/" + depot + '/' + article,'_blank');  
        }
    }

    function totalStockFormatter(id, row){
        var totalStock =parseInt(row.sommeQuantiteInitiale)+parseInt(row.sommeQuantiteApprovisionnee)-parseInt(row.sommeQuantiteDestocker)-parseInt(row.sommeQuantiteTransferee)-parseInt(row.sommeQuantiteVendue);
        return '<span class="text text-bold">' + totalStock + '</span>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection