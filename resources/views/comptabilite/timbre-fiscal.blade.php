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
</div>
<div class="col-md-3">
    <p class="text-bold h4"> Total HT : <span class="text-bold text-green" id="totalHT">0</span></p>
</div>
<div class="col-md-2">
    <p class="text-bold h4"> Total TVA : <span class="text-bold text-red" id="totalTVA">0</span></p>
</div>
<div class="col-md-3">
    <p class="text-bold h4"> Total TTC : <span class="text-bold text-green" id="totalTTC">0</span></p>
</div>
<div class="col-md-3">
    <p class="text-bold h4"> Total Timbre : <span class="text-bold text-red" id="totalTimbre">0</span></p>
</div>
<div class="col-md-1">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Valider</a><br/>
</div><br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('comptabilite',['action'=>'listes-declarations-timbres'])}}"
               data-page-list="[25,50,100,200,300,500,all]"
               data-page-size="25"
               data-unique-id="id"
               data-id-field="id"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="state" data-checkbox="true"></th>
            <th data-field="numero_ticket">N° Ticket</th>
            <th data-field="date_ventes">Date</th>
            <th data-field="totalHT" data-formatter="montantFormatter">Montant HT</th>
            <th data-formatter="montantTvaFormatter">Montant TVA </th>
            <th data-field="montantTTC" data-formatter="montantFormatter">Montant TTC </th>
            <th data-formatter="netFormatter">Net &agrave; payer </th>
            <th data-formatter="timbreFormatter">Timbre </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="60px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal panier -->
<div class="modal fade bs-modal-panier" id="panierArticle" ng-controller="panierArticleCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:75%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Panier du ticket <b>@{{vente.numero_ticket}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="tablePanierArticle" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.libelle_article">Article</th>
                                    <th data-formatter="tauxTvaLigneFormatter">TVA</th>
                                    <th data-formatter="montantHTLigneFormatter">Prix HT</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix TTC</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTotalHTLigneFormatter">Montant HT </th>
                                    <th data-formatter="montantTvaLigneFormatter">Montant TVA </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant TTC </th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>


<script type="text/javascript">
    var $table = jQuery("#table"), rows = [], dataPrint = [], $tablePanierArticle = jQuery("#tablePanierArticle");
    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
        $scope.vente = vente;
        };
    });
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

            if(depot==0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'listes-declarations-timbres'])}}"});
            }
            if(depot!=0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-timbres-by-depot/' + depot});
            }
            if(depot==0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-timbres-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot!=0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-timbres-by-depot-periode/'+ depot + '/' + dateDebut + '/' + dateFin});
            }
        });
        $("#searchByDepot").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();

            if(depot==0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'listes-declarations-timbres'])}}"});
            }
            if(depot!=0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-timbres-by-depot/' + depot});
            }
            if(depot==0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-timbres-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot!=0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-timbres-by-depot-periode/'+ depot + '/' + dateDebut + '/' + dateFin});
            }
        });

        $table.on('check.bs.table check-all.bs.table', function () {
            var selecteds = $("#table").bootstrapTable('getSelections');
            var totalHT= 0; var totalTTC = 0; var totalTimbre = 0;
            $.each(selecteds, function (index, value) {
               totalHT = totalHT + value.totalHT;
               totalTTC = totalTTC + value.montantTTC;
               value.montantTTC >5000 ?  totalTimbre = totalTimbre + 100 : totalTimbre = totalTimbre + 0;
            });
            $("#totalHT").html($.number(totalHT));
            $("#totalTTC").html($.number(totalTTC));
            $("#totalTVA").html($.number(totalTTC-totalHT));
            $("#totalTimbre").html($.number(totalTimbre));
        });
        $table.on('uncheck.bs.table uncheck-all.bs.table', function () {
            var selecteds = $("#table").bootstrapTable('getSelections');
            var totalHT= 0; var totalTTC = 0; var totalTimbre = 0;
            $.each(selecteds, function (index, value) {
               totalHT = totalHT + value.totalHT;
               totalTTC = totalTTC + value.montantTTC;
               value.montantTTC >5000 ?  totalTimbre = totalTimbre + 100 : totalTimbre = totalTimbre + 0;
            });
            $("#totalHT").html($.number(totalHT));
            $("#totalTTC").html($.number(totalTTC));
            $("#totalTVA").html($.number(totalTTC-totalHT));
            $("#totalTimbre").html($.number(totalTimbre));
        });

    });

    function imprimePdf(){
        var selecteds = $table.bootstrapTable('getSelections');
        dataPrint = [];
        $.each(selecteds, function (index, value) {
            virtuelArray = {'id':value.id};
            dataPrint.push(virtuelArray);
        });
        if(dataPrint.length==0){
            $.gritter.add({
                title: "SMART-SFV",
                text: "Veillez définir la ou les lignes à imprimer",
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            return;
        }
        var arrStr = encodeURIComponent(JSON.stringify(dataPrint));
        window.open("../etat/timbre-fiscal-pdf?array=" + arrStr,'_blank');
    }
    function listeArticleRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: "../vente/liste-articles-vente/" + idVente});
        $(".bs-modal-panier").modal("show");
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function montantHTFormatter(id, row){
        var montant  = row.montantTTC/(row.totalTVA+1);
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function montantTvaFormatter(id, row){
        var montant = row.montantTTC - row.totalHT;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function netFormatter(id,row){
        var montant = row.montantTTC;
         return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function timbreFormatter(id,row){
       row.montantTTC > 5000 ? montant = 100:montant=0;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function montantHTLigneFormatter(id, row){
        var prix_ttc = row.prix;

            var tva = row.tva;
            var prix_ht_article = (prix_ttc/(tva + 1));
            var prix = Math.round(prix_ht_article);
            return '<span class="text-bold">' + $.number(prix)+ '</span>';

    }
    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function tauxTvaLigneFormatter(id, row){
        if(row.article.param_tva_id!=null){
            var tva =row.tva *100;
            return '<span class="text-bold">' + tva.toFixed(2) + ' %'+ '</span>';
        }
    }
    function optionFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
    }

    function montantTvaLigneFormatter(id, row){
        var prix_ttc = row.prix; var prix = 0;
        var tva = row.tva;
        var prix_ht_article = (prix_ttc/(tva + 1));
        prix = Math.round(prix_ht_article);
        var montanTVA = row.prix - prix;
        return '<span class="text-bold">' + $.number(montanTVA*row.quantite)+ '</span>';
    }

    function montantTotalHTLigneFormatter(id, row){
        var prix_ttc = row.prix; var prix = 0;
        var tva = row.tva;
        var prix_ht_article = (prix_ttc/(tva + 1));
        prix = Math.round(prix_ht_article);
        var montanTVA = row.prix - prix;
        var montantHT = row.prix - montanTVA
        return '<span class="text-bold">' + $.number(montantHT*row.quantite)+ '</span>';
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
