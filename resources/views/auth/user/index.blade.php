@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur')
 <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
 <script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
 <script src="{{asset('assets/js/underscore-min.js')}}"></script>
 <script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
 <script src="{{asset('assets/js/fonction_crude.js')}}"></script>
 <script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
 <script src="{{asset('assets/plugins/iCheck/icheck.min.js')}}"></script>
 <link href="{{asset('assets/plugins/iCheck/square/orange.css')}}" rel="stylesheet">
 <link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">

 <table id="table" class="table table-warning table-striped box box-primary"
    data-pagination="true"
    data-search="true"
    data-toggle="table"
    data-show-columns="false"
    data-url="{{url('auth', ['action'=>'liste_users'])}}"
    data-unique-id="token"
    data-toolbar="#toolbar"
    data-show-toggle="false"
>
    <thead>
        <tr>
        <th data-field="id" data-width="50px" data-align="center" data-formatter="optionResetPasswordFormatter"><i class="fa fa-key"></i></th>
        <th data-field="full_name" data-sortable="true" data-searchable="true">Nom</th>
        <th data-field="login" data-searchable="true">Login</th>
        <th data-field="contact" data-searchable="true">Contact</th>
        <th data-field="role" data-searchable="true">Role</th>
        <th data-field="statut_compte" data-formatter="etatCompteFormatter">Etat</th>
        <th data-field="last_login">Derni&egrave;re connexion</th>
        <th data-field="id" data-width="80px" data-align="center" data-formatter="optionFormatter"><i class="fa fa-wrench"></i></th>
    </tr>
    </thead>
</table>
<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-users fa-2x"></i>
                        Gestion des utilisateurs (Admin et Superviseurs)
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idUserModifier" ng-hide="true" ng-model="user.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom complet *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="user.full_name" id="full_name" name="full_name" placeholder="Nom et prénom de l'utilisateur" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-key"></i>
                                    </div>
                                    <select name="role" id="role" ng-model="user.role" class="form-control" required>
                                        <option value="" ng-hide="true">-- Aucun --</option>
                                        @if(Auth::user()->role == 'Concepteur')
                                        <option value="Concepteur"> Concepteur</option>
                                        @endif
                                        <option value="Administrateur"> Administrateur</option>
                                        <option value="Caissier"> Caissier</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" ng-model="user.email" id="email" name="email" placeholder="Adresse mail de l'utilisateur">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"  name="contact" id="contact" ng-model="user.contact" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Login</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="user.login" id="login" name="login" placeholder="Login">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mot de passe</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <input type="password" class="form-control"  name="password" id="password">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="row_depot">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_id" id="depot_id" ng-model="user.depot_id" ng-init="user.depot_id=''" class="form-control">
                                        <option value="" ng-show="false">-- Selectionner le D&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
<!-- Modal fermeture de compte -->
<div class="modal fade bs-modal-lokked-acount" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formLokedAcount" ng-controller="formLokedAcountCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Confimation de l'op&eacute;ration
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idUserLokedAcount"  ng-model="user.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir <b>@{{user.statut_compte==1?'désactiver' : 'activer'}}</b> le compte de l'utilisateur <br/><b>@{{user.full_name}}</b></div>
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

<!-- Modal reset password -->
<div class="modal fade bs-modal-reset-password" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formPasswordReset" ng-controller="formPasswordResetCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Confimation de l'op&eacute;ration
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idUserPasswordReset"  ng-model="user.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir r&eacute;initialiser le mot de passe de cet utilisateur <br/><b>@{{user.full_name}}</b></div>
                        <div class="text-center vertical processing">R&eacute;initialisation en cours</div>
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

<script>
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    const EXCEL_EXTENSION = '.xlsx';
    function exportAsExcelFile(jsonData, excelFileName) {
        const worksheet=XLSX.WorkSheet = XLSX.utils.json_to_sheet(jsonData);
        const workbook=XLSX.WorkBook = { Sheets: { 'data': worksheet }, SheetNames: ['data'] };
        const excelBuffer= XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
        console.log('Buffer:'+excelBuffer,worksheet,workbook)
        this.saveAsExcelFile(excelBuffer, excelFileName);
    }
    function saveAsExcelFile(buffer,fileName) {
        const data= new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8'});
        // saveAs(data, fileName + '_export_' + new  Date().getTime()+'.xlsx');
        //Télécharger sur tous les navigateur
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(data);
        link.download = fileName +'-'+ new  Date();
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    $("input[name='fileToUpload']").on("change", function (e) {
        var file = e.target.files[0];
        // input canceled, return
        if (!file) return;

        var FR = new FileReader();
        FR.onload = function(e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, {type: 'array'});
            var firstSheet = workbook.Sheets[workbook.SheetNames[0]];

            // header: 1 instructs xlsx to create an 'array of arrays'
            var result = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
            console.log('output', result)

            // data preview
            var output = document.getElementById('result');
            output.innerHTML = JSON.stringify(result, null, 2);
        };
        FR.readAsArrayBuffer(file);
    });
</script>


<script type="text/javascript">
    	var ajout = true;
    	var $table = jQuery("#table"), rows = [];
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (user) {
        $scope.user = user;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.user = {};
        };
    });
    appSmarty.controller('formLokedAcountCtrl', function ($scope) {
        $scope.populateForm = function (user) {
        $scope.user = user;
        };
        $scope.initForm = function () {
        $scope.user = {};
        };
    });
    appSmarty.controller('formPasswordResetCtrl', function ($scope) {
        $scope.populateForm = function (user) {
        $scope.user = user;
        };
        $scope.initForm = function () {
        $scope.user = {};
        };
    });
     $(function () {
     	$table.on('load-success.bs.table', function (e, data) {
            rows = data.rows;
        });
        $("#row_depot").hide();
        $("#row_caisse").hide();
        $('#depot_id').attr('required', false)
        $('#caisse_id').attr('required', false)

        $('#btnModalAjout').click(function(){
            $("#row_depot").hide();
            $('#depot_id').attr('required', false)
        });
        $('#role').change(function(){
            var role = $('#role').val();
            if(role=="Gerant" || role=="Caissier" || role=="Comptable"){
                $("#row_depot").show();
                $('#depot_id').attr('required', true)
            }
            if(role!="Gerant" && role!="Caissier" && role!="Comptable"){
                $("#row_depot").hide();
                $('#depot_id').attr('required', false)
            }
        });

        $("#formAjout").submit(function (e) {
            e.preventDefault();
            var $ajaxLoader = $("#formAjout .loader-overlay");
            if (ajout === true) {
            var methode = 'POST';
            var url = "{{route('auth.users.store')}}";
            } else {
            var id = $("#idUserModifier").val();
            var methode = 'PUT';
            var url = 'users/' + id;
            }
            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formLokedAcount").submit(function (e) {
		    e.preventDefault();
		    var id = $("#idUserLokedAcount").val();
		    var formData = $(this).serialize();
		    var $question = $("#formLokedAcount .question");
		    var $ajaxLoader = $("#formLokedAcount .processing");
		    lokedAcountAction('users/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
    	});

        $("#formPasswordReset").submit(function (e) {
            e.preventDefault();
            var id = $("#idUserPasswordReset").val();
            var formData = $(this).serialize();
            var $question = $("#formPasswordReset .question");
            var $ajaxLoader = $("#formPasswordReset .processing");
            resetPasswordAction('reset_password_manualy/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
     });

    function updateRow(idUser) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var user =_.findWhere(rows, {id: idUser});
        $scope.$apply(function () {
        $scope.populateForm(user);
        });

        if(user.role=="Gerant" || user.role=="Caissier" || user.role=="Comptable"){
                $("#row_depot").show();
                $('#depot_id').attr('required', true)
        }
        if(user.role!="Gerant" && user.role!="Caissier" && user.role!="Comptable"){
                $("#row_depot").hide();
                $('#depot_id').attr('required', false)
        }
        $(".bs-modal-ajout").modal("show");
    }

    function lokedAcountRow(idUser) {
        var $scope = angular.element($("#formLokedAcount")).scope();
        var user =_.findWhere(rows, {id: idUser});
        $scope.$apply(function () {
        $scope.populateForm(user);
        });
        $(".bs-modal-lokked-acount").modal("show");
    }

    function updatePasswordRow(idUser) {
    var $scope = angular.element($("#formPasswordReset")).scope();
    var user =_.findWhere(rows, {id: idUser});
    $scope.$apply(function () {
    $scope.populateForm(user);
    });
    $(".bs-modal-reset-password").modal("show");
    }
    function optionFormatter(id, row) {
        if(row.statut_compte==0){
            return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Activer" onClick="javascript:lokedAcountRow(' + id + ');"><i class="fa fa-check"></i></button>';
            }else{
                 return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Désactiver" onClick="javascript:lokedAcountRow(' + id + ');"><i class="fa fa-remove"></i></button>';
            }

    }
    function etatCompteFormatter(etat){
        return etat==1 ? "<span class='label label-success'>Active</span>":"<span class='label label-danger'>Fermé</span>";
    }
    function optionResetPasswordFormatter(id, row){
        if(row.email!=null){
            return '<button class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Reset password" onClick="javascript:updatePasswordRow(' + id + ');"><i class="fa fa-refresh"></i></button>';
        }else{
            return '---';
        }
    }


//Réinitialiser un mot de passe
function resetPasswordAction(url, formData, $question, $ajaxLoader, $table) {
    jQuery.ajax({
        type: "DELETE",
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                $(".bs-modal-reset-password").modal("hide");
                $table.bootstrapTable('refresh');
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
            //alert(res.message);
            //alert(Object.getOwnPropertyNames(res));
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: res.message,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png"
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}

//Fermer un compte
   //Réinitialiser un mot de passe
function lokedAcountAction(url, formData, $question, $ajaxLoader, $table) {
    jQuery.ajax({
        type: "DELETE",
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                $(".bs-modal-lokked-acount").modal("hide");
                $table.bootstrapTable('refresh');
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
            //alert(res.message);
            //alert(Object.getOwnPropertyNames(res));
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: res.message,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png"
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
