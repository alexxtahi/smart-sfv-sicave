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
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Toutes les d&eacute;p&ocirc;ts --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-4">
    <p class="text-bold h4"> Total Prix Achat : <span class="text-bold text-red" id="totalAchat">0</span></p>
</div>
<div class="col-md-4">
    <p class="text-bold h4"> Total Prix Vente : <span class="text-bold text-green" id="totalTTC">0</span></p>
</div>
<div class="col-md-3">
    <p class="text-bold h4"> Total Marge : <span class="text-bold text-red" id="totalMarge">0</span></p>
</div>
<div class="col-md-1">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div><br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('comptabilite',['action'=>'liste-marges-sur-ventes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="numero_ticket">N° Ticket</th>
            <th data-field="date_ventes">Date</th>
            <th data-field="montantAchat" data-formatter="montantFormatter">Montant PA</th>
            <th data-field="montantTTC" data-formatter="montantFormatter">Montant PV </th>
            <th data-formatter="margeFormatter">Marge </th>
            <th data-formatter="tauxMargeFormatter" data-width="75">Taux de Marge </th>
            <th data-field="id" data-formatter="panierFormatter" data-width="75">Panier </th>
        </tr>
    </thead>
</table>

<!-- Modal panier -->
<div class="modal fade bs-modal-panier" id="panierArticle" ng-controller="panierArticleCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Panier du ticket N° <b>@{{vente.numero_ticket}}</b>
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
                                    <th data-field="prix" data-formatter="montantFormatter">Prix TTC</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant TTC </th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<script type="text/javascript">
    var $table = jQuery("#table"), rows = [], $tablePanierArticle = jQuery("#tablePanierArticle");
    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
        $scope.vente = vente;
        };
    });
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
            $("#totalAchat").html($.number(data.totalAchat));
            $("#totalTTC").html($.number(data.totalTTC));
            $("#totalMarge").html($.number(data.totalTTC-data.totalAchat));
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
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'liste-marges-sur-ventes'])}}"});
            }
            if(depot != 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-marges-sur-ventes-by-depot/' + depot});
            }
            if(depot == 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-marges-sur-ventes-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot != 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-marges-sur-ventes-by-periode-depot/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });

         $("#searchByDepot").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();

            if(depot == 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('comptabilite', ['action' => 'liste-marges-sur-ventes'])}}"});
            }
            if(depot != 0 && dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-marges-sur-ventes-by-depot/' + depot});
            }
            if(depot == 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-marges-sur-ventes-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot != 0 && dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../comptabilite/liste-marges-sur-ventes-by-periode-depot/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });

    });

    function listeArticleRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: "../vente/liste-articles-vente/" + idVente});
        $(".bs-modal-panier").modal("show");
    }

    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var depot = $("#searchByDepot").val();

        if(depot==0 && dateDebut=='' && dateFin==''){
            window.open("../etat/liste-marges-sur-ventes-pdf/",'_blank');
        }
         if(depot!=0 && dateDebut=='' && dateFin==''){
            window.open("../etat/liste-marges-sur-ventes-by-depot-pdf/" + depot ,'_blank');
        }
        if(depot==0 && dateDebut!='' && dateFin!=''){
            window.open("../etat/liste-marges-sur-ventes-by-periode-pdf/" + dateDebut + "/" + dateFin ,'_blank');
        }
        if(depot!=0 && dateDebut!='' && dateFin!=''){
            window.open("../etat/liste-marges-sur-ventes-by-periode-depot-pdf/" + dateDebut + "/" + dateFin + "/" + depot ,'_blank');
        }
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function margeFormatter(id, row){
        var montant  = row.montantTTC-row.montantAchat;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function tauxMargeFormatter(id ,row){
        var marge_commercial = row.montantTTC - row.montantAchat;
        var taux_marge = (marge_commercial/row.montantAchat)*100;
        return taux_marge.toFixed(2) + ' %';
    }
    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function panierFormatter(id) {
            return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
