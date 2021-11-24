@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">

<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-promotions'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_debuts">Date d&eacute;but</th>
            <th data-field="date_fins">Date fin </th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="article.description_article">Article </th>
            <th data-field="unite.libelle_unite">Lot </th>
            <th data-field="prix_promotion">Prix du promo</th>
            <th data-field="en_promotion" data-formatter="enPromotionFormatter">Promo</th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 70%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-tripadvisor fa-2x"></i>
                        Gestion des promotions
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idPromotionModifier" ng-hide="true" ng-model="promotion.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date du d&eacute;but *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="promotion.date_debuts" id="date_debut" name="date_debut" placeholder="Date début promo" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date de fin *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="promotion.date_fins" id="date_fin" name="date_fin" placeholder="Date fin promo" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_id" id="depot_id" ng-model="promotion.depot_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le d&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Code barre</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-barcode"></i>
                                    </div>
                                    <input type="text" class="form-control" id="code_barre">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Articles *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-cubes"></i>
                                    </div>
                                    <select id="article_id" name="article_id"  class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner l'article --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Colis *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select name="unite_id" id="unite_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le colis --</option>
                                       
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*"  class="form-control" ng-model="promotion.prix_promotion" id="prix_promotion" name="prix_promotion" placeholder="Prix promo" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"> 
                            <h5 class="text-bold text-red"><br/><br/>
                                <label>
                                    <input type="checkbox" id="en_promotion" name="en_promotion" ng-model="promotion.en_promotion" ng-checked="promotion.en_promotion">&nbsp; Cochez cette case pour que l'article reste en promotion jusqu'&agrave; la date de fin de la promo
                                </label>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion -->
<div class="modal fade bs-modal-suppression" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimer" ng-controller="formSupprimerCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idPromotionSupprimer"  ng-model="promotion.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer la promotion de l'article <br/><b>@{{promotion.article.description_article}}</b> du <b>@{{promotion.date_debuts}}</b> au <b>@{{promotion.date_fins}}</b></div>
                        <div class="text-center vertical processing">Suppression en cours</div>
                        <div class="pull-right">
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Non</button>
                            <button type="submit" class="btn btn-danger btn-sm ">Oui</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];
    
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (promotion) {
            $scope.promotion = promotion;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.promotion = {};
        };
    });
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (promotion) {
            $scope.promotion = promotion;
        };
        $scope.initForm = function () {
            $scope.promotion = {};
        };
    });
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        $("#depot_id, #article_id").select2({width: '100%'});
        $('#date_debut').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            minDate : new Date()
        });
        $('#date_fin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            minDate : new Date()
        });
        $("#searchByDate").change(function (e) {
            $("#searchByArticle").val(0);
            $("#searchByDepot").val(0);
            var date = $("#searchByDate").val();
            if(date == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-promotions'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-date/' + date});
            }
        }); 
        $("#searchByArticle").change(function (e) {
            $("#searchByDate").val("");
            var depot = $("#searchByDepot").val();
            var article = $("#searchByArticle").val();
            if(article == 0 && depot == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-promotions'])}}"});
            }
            if(article != 0 && depot != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-article-depot/' + article + '/' + depot});
            }
            if(article != 0 && depot == 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-article/' + article});
            }
            if(article == 0 && depot != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-depot/' + depot});
            }
        });
        $("#searchByDepot").change(function (e) {
            $("#searchByDate").val("");
            var depot = $("#searchByDepot").val();
            var article = $("#searchByArticle").val();
            if(article == 0 && depot == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-promotions'])}}"});
            }
            if(article != 0 && depot != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-article-depot/' + article + '/' + depot});
            }
            if(article != 0 && depot == 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-article/' + article});
            }
            if(article == 0 && depot != 0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-promotions-by-depot/' + depot});
            }
        });
        
        $("#btnModalAjout").on("click", function () {
           $('#article_id').html("<option value=''>-- Selectionner l'article --</option>");
           $('#unite_id').html("<option>-- Colis --</option>");
           $("#depot_id").select2("val","");
           $("#article_id").select2("val","");
           $('#code_barre').val('');
        });
        
        $('#depot_id').change(function(){
            var depot_id = $('#depot_id').val();
            $.getJSON("../boutique/liste-article-by-unite-in-depot/" + depot_id, function (reponse) {
                $('#article_id').html("<option>-- Selectionner l'article --</option>");
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, article) { 
                    $('#article_id').append('<option value=' + article.id_article + '>' + article.description_article + '</option>')
                    });
                }else{
                    alert('Aucun article disponible dans ce dépôt !')
                }
            })
        });
        
        $('#code_barre').keyup(function(){
            var code_barre = $('#code_barre').val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function (reponse) {
                $('#article_id').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, retour) { 
                    $('#article_id').append('<option selected value=' + retour.article.id + '>' + retour.article.description_article + '</option>')
                    $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + retour.article.id , function (reponse) {
                        $('#unite_id').html("<option>-- Colis --</option>");
                        $.each(reponse.rows, function (index, colis) { 
                            $('#unite_id').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                        });
                    })
                });
            })
        });
        $('#article_id').change(function(){
            var article_id = $("#article_id").val();
            var depot_id = $("#depot_id").val();
             $("#code_barre").val("");
             $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) { 
                    $("#code_barre").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + article_id , function (reponse) {
                $('#unite_id').html("<option>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) { 
                    $('#unite_id').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
            })
        });
        $("#formAjout").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjout .loader-overlay");

             if (ajout==true) {
                var methode = 'POST';
                var url = "{{route('boutique.promotions.store')}}";
             }else{
                var id = $("#idPromotionModifier").val();
                var methode = 'PUT';
                var url = 'promotions/' + id;
             }
            editerPromotionAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });
        
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idPromotionSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('promotions/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    
    function updateRow(idPromotion) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var promotion =_.findWhere(rows, {id: idPromotion});
         $scope.$apply(function () {
            $scope.populateForm(promotion);
        });
        $.getJSON("../boutique/liste-article-by-unite-in-depot/" + promotion.depot_id, function (reponse) {
            $('#article_id').html("<option>-- Selectionner l'article --</option>");
                $.each(reponse.rows, function (index, article) { 
                    $('#article_id').append('<option value=' + article.id_article + '>' + article.description_article + '</option>')
                });
                $("#article_id").select2("val",promotion.article_id);
              
        })
        $.getJSON("../boutique/liste-unites-by-depot-article/" + promotion.depot_id + "/" + promotion.article_id , function (reponse) {
                $('#unite_id').html("<option>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) { 
                    $('#unite_id').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
                 $("#unite_id").val(promotion.unite_id);
        })
        $("#depot_id").select2("val",promotion.depot_id);
        $(".bs-modal-ajout").modal("show");
    }
    
    function deleteRow(idPromotion) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var promotion =_.findWhere(rows, {id: idPromotion});
           $scope.$apply(function () {
              $scope.populateForm(promotion);
          });
       $(".bs-modal-suppression").modal("show");
    }
    
    function enPromotionFormatter(promo){
        return promo ? "OUI":"NON";
    }
    
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function editerPromotionAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $('#article_id').html("<option>-- Selectionner l'article --</option>");
                $('#unite_id').html("<option>-- Colis --</option>");
                $('#code_barre').val("");
                $("#depot_id").select2("val","");
                $formObject.trigger('eventAjouter', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
         },
          error: function (err) {
            var res = eval('('+err.responseText+')');
            var messageErreur = res.message;
            
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: messageErreur,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
         beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        },
    });
};
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection