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
            <option value="0">-- Toues les &eacute;p&ocirc;t --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-mouvements-stocks'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
           <th data-field="date_mouvements">Date mouvement</th>
           <th data-field="article.code_barre">Code barre</th>
            <th data-field="article.description_article" data-searchable="true" data-sortable="true">Article</th>
            <th data-field="unite.libelle_unite">Colis</th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t</th>
            <th data-align="center" data-field="quantite_initiale">Qt&eacute; initale </th>
            <th data-align="center" data-field="quantite_approvisionnee">Qt&eacute; appro. </th>
            <th data-align="center" data-field="quantite_destocker">Qt&eacute; d&eacute;stock. </th>
            <th data-align="center" data-field="quantite_transferee">Qt&eacute; transf. </th>
            <th data-align="center" data-field="quantite_vendue">Qt&eacute; vendue </th>
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
        
        $('#dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr'
        });
        $("#searchByArticle").select2({width: '100%', allowClear: true});
        $("#dateDebut, #dateFin").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByArticle").select2("val",0);
            $("#searchByDepot").val(0);
            if(dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-mouvements-stocks'])}}"});
            }
            if(dateDebut!='' && dateFin!=''){
               $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-periode/' + dateDebut + '/' + dateFin});
            }
        });
        $("#searchByArticle").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var article = $("#searchByArticle").val();
            var depot = $("#searchByDepot").val();
            if(dateDebut=='' && dateFin=='' && article ==0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-mouvements-stocks'])}}"});
            }
            if(dateDebut!='' && dateFin!='' && article !=0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-article-by-depot-on-periode/' + dateDebut + '/' + dateFin + '/' + article + '/' + depot});
            }
            if(dateDebut!='' && dateFin!='' && article !=0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-article-on-periode/' + dateDebut + '/' + dateFin + '/' + article});
            }
            if(dateDebut=='' && dateFin=='' && article !=0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-article/' + article});
            }
            if(dateDebut=='' && dateFin=='' && article !=0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-article-depot/' + article + '/' + depot});
            }
            if(dateDebut=='' && dateFin=='' && article ==0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-depot/' + depot});
            }
            if(dateDebut!='' && dateFin!='' && article ==0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-depot-on-periode/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });
        $("#searchByDepot").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var article = $("#searchByArticle").val();
            var depot = $("#searchByDepot").val();
            if(dateDebut=='' && dateFin=='' && article ==0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-mouvements-stocks'])}}"});
            }
            if(dateDebut!='' && dateFin!='' && article !=0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-article-by-depot-on-periode/' + dateDebut + '/' + dateFin + '/' + article + '/' + depot});
            }
            if(dateDebut!='' && dateFin!='' && article !=0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-article-on-periode/' + dateDebut + '/' + dateFin + '/' + article});
            }
            if(dateDebut=='' && dateFin=='' && article !=0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-article/' + article});
            }
            if(dateDebut=='' && dateFin=='' && article !=0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-article-depot/' + article + '/' + depot});
            }
            if(dateDebut=='' && dateFin=='' && article ==0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-depot/' + depot});
            }
            if(dateDebut!='' && dateFin!='' && article ==0 && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-mouvements-stocks-by-depot-on-periode/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });
    });
    
    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var article = $("#searchByArticle").val();
        var depot = $("#searchByDepot").val();
        
        if(dateDebut=='' && dateFin=='' && article ==0 && depot==0){
            window.open("mouvements-stocks-pdf/" ,'_blank');
        }
        if(dateDebut!='' && dateFin!='' && article !=0 && depot!=0){
            window.open("mouvements-stocks-article-by-depot-on-periode-pdf/" + dateDebut + '/' + dateFin + '/' + article + '/' + depot,'_blank');  
        }
        if(dateDebut!='' && dateFin!='' && article ==0 && depot==0){
            window.open("mouvements-stocks-by-periode-pdf/" + dateDebut + '/' + dateFin,'_blank');  
        }
        if(dateDebut!='' && dateFin!='' && article !=0 && depot==0){
            window.open("mouvements-stocks-by-article-on-periode-pdf/" + dateDebut + '/' + dateFin + '/' + article,'_blank');  
        }
        if(dateDebut!='' && dateFin!='' && article ==0 && depot!=0){
            window.open("mouvements-stocks-by-depot-on-periode-pdf/" + dateDebut + '/' + dateFin + '/' + depot,'_blank');  
        }
        if(dateDebut=='' && dateFin=='' && article !=0 && depot==0){
            window.open("mouvements-stocks-article-pdf/" + article,'_blank');  
        }
        if(dateDebut=='' && dateFin=='' && article ==0 && depot!=0){
            window.open("mouvements-stocks-depot-pdf/" + depot,'_blank');  
        }
        if(dateDebut=='' && dateFin=='' && article !=0 && depot!=0){
            window.open("mouvements-stocks-by-article-depot-pdf/" + article + '/' + depot,'_blank');  
        }
    }

    function totalStockFormatter(id, row){
        var totalStock = (parseInt(row.quantite_initiale) + parseInt(row.quantite_approvisionnee)) - (parseInt(row.quantite_vendue) + parseInt(row.quantite_destocker) + parseInt(row.quantite_transferee));
        return '<span class="text text-bold">' + totalStock + '</span>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection