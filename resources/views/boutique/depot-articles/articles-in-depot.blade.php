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
        <a href="{{route('boutique.export-excel-articl-by-depot',$depot_choisi->id)}}" class="btn btn-sm btn-warning"><i class="fa fa-file-excel-o"></i> Exporter</a>
    </div>
    <div class="col-md-3 text-bold">
        Total des articles en stock : <span id="t_article" class="text-yellow" style="font-size: 20px;">0</span> 
    </div>
    <div class="col-md-4 text-bold">
        Montant total TTC des ventes : <span id="t_vente" class="text-green" style="font-size: 20px;">0</span> F CFA
    </div>
    <div class="col-md-2">
        <a class="btn btn-sm btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<div class="row">
    <div class="col-md-3">
        <select class="form-control" id="searchByCategorie">
            <option value="0">-- Toutes les cat&eacute;gories --</option>
            @foreach($categories as $categorie)
            <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control" id="searchByStock">
            <option value="0">-- Tous les articles--</option>
            <option value="1">-- Disponible en stock --</option>
            <option value="2">-- Manque en stock --</option>
        </select>
    </div>
</div>
<br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
           <th data-field="code_barre">Code barre</th>
            <th data-field="description_article" data-searchable="true" data-sortable="true">Article</th>
            <th data-field="libelle_unite" data-searchable="true" data-sortable="true">Lot</th>
            <th data-field="quantite_disponible" data-align="center">En stock </th>
            <th data-formatter="prixAchatHtFormatter">Prix Achat HT</th>
            <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix Achat TTC</th>
            <th data-formatter="prixVenteHtFormatter">Prix vente HT</th>
            <th data-field="prix_vente" data-formatter="montantFormatter">Prix vente TTC</th>
            <th data-formatter="montantTTCventeFormatter">Montant TTC vente</th>
       </tr>
    </thead>
</table>
<form>
    <input class="hidden" value="{{$depot_choisi->id}}" id="depot">
</form>
<script type="text/javascript">
    var $table = jQuery("#table");
    var depot = $("#depot").val();
    $(function () {
        $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot/' + depot});
        
        $table.on('load-success.bs.table', function (e, data) { 
            rows = data.rows;
            $("#t_article").html($.number(data.totalArticle));
            $("#t_vente").html($.number(data.totalVente));
        });
        
        $("#searchByCategorie").change(function (e) {
            
            var quantite = $("#searchByStock").val();
            var categorie = $("#searchByCategorie").val();
            
            if(quantite == 0 && categorie == 0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot/' + depot});
            }
                    
            if(quantite == 0 && categorie != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot-by-categorie/' + depot + '/' + categorie});
            }
            
            if(quantite != 0 && categorie == 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot-by-quantite/' + depot + '/' + quantite});
            }
            
            if(quantite != 0 && categorie != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot-by-categorie-quantite/' + depot + '/' + categorie + '/'+ quantite});
            }
        });
        
        $("#searchByStock").change(function (e) {
            var quantite = $("#searchByStock").val();
            var categorie = $("#searchByCategorie").val();
              
            if(quantite == 0 && categorie == 0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot/' + depot});
            }
                    
            if(quantite == 0 && categorie != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot-by-categorie/' + depot + '/' + categorie});
            }
            
            if(quantite != 0 && categorie == 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot-by-quantite/' + depot + '/' + quantite});
            }
            
            if(quantite != 0 && categorie != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/get-all-article-in-one-depot-by-categorie-quantite/' + depot + '/' + categorie + '/'+ quantite});
            }
        });
    });

    function prixVenteHtFormatter(id, row){
        var prix_vente_ttc = row.prix_vente;
        row.param_tva_id !=null ? tva = row.montant_tva : tva = 0;
        var prix_vente_ht = (prix_vente_ttc/(tva + 1));
        var prix = Math.round(prix_vente_ht);
        return '<span class="text-bold">' +prix+ '</span>';
    }
    function prixAchatHtFormatter(id, row){
        var prix_achat = row.prix_achat_ttc;
        row.param_tva_id !==null ? tva = row.montant_tva : tva = 0;
        var prix_achat_ht = (prix_achat/(tva + 1));
        var prix = Math.round(prix_achat_ht);
        return '<span class="text-bold">' +prix+ '</span>';
    }
    function montantTTCventeFormatter(id, row){
        var montant = row.prix_vente*row.quantite_disponible;
        return '<span class="text-bold">' + $.number(montant)+ '</span>'
    }
    function montantFormatter(montant){
        return montant ? '<span class="text-bold">' + $.number(montant)+ '</span>' : "--";
    }
    function imprimePdf(){
        var quantite = $("#searchByStock").val();
        var categorie = $("#searchByCategorie").val();
            
        if(quantite == 0 && categorie == 0){
             window.open("liste-articles-by-depot-pdf/" + depot,'_blank'); 
        }
        
        if(quantite != 0 && categorie == 0){
             window.open("liste-articles-by-depot-by-quantite-pdf/" + depot + '/' + quantite,'_blank'); 
        }
        
        if(quantite == 0 && categorie != 0){
             window.open("liste-articles-by-depot-by-categorie-pdf/" + depot + '/' + categorie,'_blank'); 
        }
        
        if(quantite != 0 && categorie != 0){
             window.open("liste-articles-by-depot-by-categorie-quantite-pdf/" + depot + '/' + categorie + '/' + quantite,'_blank'); 
        }
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection