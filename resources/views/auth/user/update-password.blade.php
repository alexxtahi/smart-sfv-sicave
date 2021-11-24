@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<div class="row">
<div class="col-md-12">
    <div class="box box-widget widget-user-2">
        <div class="widget-user-header bg-primary">
            <a href="{{route('auth.profil-informations')}}" class="btn btn-default pull-right">Retour sur mon profil</a>
            <h5 class="widget-user-username">Modifier mon mot de passe</h5>
        </div>  
    </div>  
    @error('new_password')
    <div class="alert alert-warning">
      <h4><i class="icon fa fa-warning"></i> Rappel format mot de passe !</h4>
            <p> . Le mot de passe doit &ecirc;tre 8 caract&egrave;res minimum</p>
    <p> . Le mot de passe doit comporter au moins une majuscule (A–Z)</p>
    <p> . Le mot de passe doit comporter au moins une minuscule (a–z)</p>
    <p> . Le mot de passe doit comporter au moins un nombre (0–9)</p>
    <p> . Le mot de passe doit comporter au moins un caract&egrave;re non alphanum&eacute;rique (!, $, @, ou %)</p>
    </div>
    @enderror
    <div class="box-footer no-padding">    
    	<form method="POST" action="{{route('auth.update-password')}}">
            <div class="modal-content">
                <div class="modal-body ">
                    @csrf
                    <input type="text" class="hidden" name="idUser" value="{{$user->id}}" />
                    <input type="text" class="hidden" name="login" value="{{$user->login}}"/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Ancien mot de passe *</label>
                                <div class="input-group">
                                  <div class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                  </div>
                                  <input type="password" class="form-control" name="password" placeholder="Ancien mot de passe" required>
                                </div>
                                @if(\Session::has('error'))
                                    <span class="invalid-feedback text-red" role="alert">
                                        <strong>{!! \Session::get('error')!!}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nouveau mot de passe *</label>
                                <div class="input-group">
                                  <div class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                  </div>
                                  <input type="password" minlength="8" class="form-control" name="new_password" placeholder="Nouveau mot de passe" required>
                                </div>
                                 @error('new_password')
                                  <span class="invalid-feedback text-red" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                            </div>
                        </div>
<!--                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Confirmation du nouveau mot de passe *</label>
                                <div class="input-group">
                                  <div class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                  </div>
                                  <input minlength="8" id="password-confirm" type="password" class="form-control form-control-rounded" name="password_confirmation" placeholder="Confirmer le nouveau mot de passe" required>
                                </div>
                            </div>
                        </div>-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i></span>Valider</button>
                </div>
            </div>
        </form>
    </div> 
</div>
</div>
@endsection