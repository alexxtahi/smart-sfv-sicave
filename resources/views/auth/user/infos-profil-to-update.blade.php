@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<div class="col-md-12">
    <div class="box box-widget widget-user-2">
        <div class="widget-user-header bg-primary">
            <a href="{{route('auth.profil-informations')}}" class="btn btn-default pull-right">Retour sur mon profil</a>
            <h5 class="widget-user-username">Modifier mes informations de profil</h5>
        </div>  
    </div>  
    <div class="box-footer no-padding">    
    	<form id="formUpdateProfil" action="#">
            <div class="modal-content">
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idUserProfilModifier" value="{{$user->id}}" />
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom complet *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" value="{{$user->full_name}}" name="full_name" placeholder="Nom et prénom de l'utilisateur" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Login *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$user->login}}" name="login" placeholder="Login" required>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                         <div class="col-md-6">
                             <div class="form-group">
                                 <label>E-mail </label>
                                 <div class="input-group">
                                     <div class="input-group-addon">
                                         <i class="fa fa-at"></i>
                                     </div>
                                     <input type="email" class="form-control" value="{{$user->email}}" name="email" placeholder="Adresse mail de l'utilisateur">
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
				                  <input type="text" class="form-control bfh-phone" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"  name="contact" value="{{$user->contact}}" required>
				                </div>
              				</div>
                    	</div>                    	
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i></span>Valider</button>
                </div>
            </div>
        </form>
    </div> 
</div>
<script type="text/javascript">
	 $('form').submit(function(){ $(this).find(':submit').attr('disabled','disabled'); });
	$(function () {
		$("#formUpdateProfil").submit(function (e) {
            e.preventDefault();
            var $ajaxLoader = $("#formUpdateProfil .loader-overlay");
            var id = $("#idUserProfilModifier").val();
            var methode = 'PUT';
            var url = 'update-profil/' + id;
            editerProfilAction(methode, url, $(this), $(this).serialize(), $ajaxLoader);
        });
	});

	function editerProfilAction(methode, url, $formObject, formData, $ajoutLoader) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
              window.location.href = basePath + "/auth/profil-informations";
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
@endsection