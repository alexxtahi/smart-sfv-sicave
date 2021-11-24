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
<div class="col-md-4">
    <p class="text-bold h4"> Total HT : <span class="text-bold text-green" id="totalHT">0</span></p>
</div>
<div class="col-md-3">
    <p class="text-bold h4"> Total TVA : <span class="text-bold text-red" id="totalTVA">0</span></p>
</div>
<div class="col-md-4">
    <p class="text-bold h4"> Total TTC : <span class="text-bold text-green" id="totalTTC">0</span></p>
</div>
<div class="col-md-1">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Valider</a><br/>
</div><br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('comptabilite',['action'=>'listes-declarations-fiscales'])}}"
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
            <th data-field="libelle_article">Article</th>
            <th data-field="quantite">Qt&eacute; / Btle</th>
            <th data-field="prix_ht" data-formatter="montantFormatter">Prix HT</th>
            <th data-field="prix_vente_ttc" data-formatter="montantFormatter">Prix TTC</th>
            <th data-formatter="montantHT">Montant HT</th>
            <th data-formatter="montantTTC">Montant TTC </th>
            <th data-formatter="montantTvaFormatter">Montant TVA </th>
            <th data-formatter="tvaFormatter">TVA </th>
            <th data-formatter="netFormatter">Montant net</th>
            <th data-field="idArticleVente" data-visible="false"  data-width="60px" data-align="center">Id</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var $table = jQuery("#table"), rows = [], dataPrint = [];
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

            if(depot == 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'listes-declarations-fiscales'])}}"});
            }
            if(depot != 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-fiscales-by-depot/' + depot});
            }
            if(depot == 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-fiscales-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot != 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-fiscales-by-depot-periode/' + depot + '/' + dateDebut + '/' + dateFin});
            }
        });

        $("#searchByDepot").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();

            if(depot == 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'listes-declarations-fiscales'])}}"});
            }
            if(depot != 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-fiscales-by-depot/' + depot});
            }
            if(depot == 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-fiscales-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot != 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/listes-declarations-fiscales-by-depot-periode/' + depot + '/' + dateDebut + '/' + dateFin});
            }
        });

        $table.on('check.bs.table check-all.bs.table', function () {
             var selecteds = $("#table").bootstrapTable('getSelections');
            var totalHT= 0; var totalTTC = 0; var totalTva = 0;
            $.each(selecteds, function (index, value) {
                var tav = 0;
               totalHT = totalHT + value.prix_ht*value.quantite;
               totalTTC = totalTTC + value.prix_vente_ttc*value.quantite;
               totalTva = totalTTC-totalHT;
            });
            $("#totalHT").html($.number(totalHT));
            $("#totalTTC").html($.number(totalTTC));
            $("#totalTVA").html($.number(totalTva));
        });
        $table.on('uncheck.bs.table uncheck-all.bs.table', function () {
            var selecteds = $("#table").bootstrapTable('getSelections');
            var totalHT= 0; var totalTTC = 0; var totalTva = 0;
            $.each(selecteds, function (index, value) {
               var tav = 0;
               totalHT = totalHT + value.prix_ht*value.quantite;
               totalTTC = totalTTC + value.prix_vente_ttc*value.quantite;
               totalTva = totalTTC-totalHT;
            });
            $("#totalHT").html($.number(totalHT));
            $("#totalTTC").html($.number(totalTTC));
            $("#totalTVA").html($.number(totalTva));
        });
    });

    function imprimePdf(){
        var selecteds = $table.bootstrapTable('getSelections');
        dataPrint = [];
        $.each(selecteds, function (index, value) {
            virtuelArray = {'id':value.idArticleVente};
            dataPrint.push(virtuelArray);
        });
        if(dataPrint.length==0){
            $.gritter.add({
                title: "SMART-SFV",
                text: "Veillez définir la ou les lignes à déclarer",
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            return;
        }
        var arrStr = encodeURIComponent(JSON.stringify(dataPrint));
        window.open("../etat/declaration-tva-pdf?array=" + arrStr,'_blank');
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function montantHT(id, row){
        return '<span class="text-bold">' + $.number(row.prix_ht*row.quantite)+ '</span>';
    }
    function montantTTC(id, row){
        return '<span class="text-bold">' + $.number(row.prix_vente_ttc*row.quantite)+ '</span>';
    }

    function montantTvaFormatter(id, row){
        var montant = row.prix_ht*row.tva*row.quantite;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function tvaFormatter(id, row){
        return '<span class="text-bold">' + row.tva * 100 + '%</span>';
    }
    function netFormatter(id,row){
        var montant = row.prix_vente_ttc;
         return '<span class="text-bold">' + $.number(montant*row.quantite)+ '</span>';
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection

