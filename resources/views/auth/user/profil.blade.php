@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<div class="col-md-12">
    <div class="box box-widget widget-user-2">
        <div class="widget-user-header bg-primary">
            <div class="widget-user-image">
            	<img class="img-circle" src="{{asset('images/profil.png')}}" alt="User Avatar">
            </div>
            <a href="{{route('auth.infos-profil-to-update')}}" class="btn btn-default pull-right">Modifier mes infos</a>
            <h3 class="widget-user-username">{{$user->full_name}}</h3>
            <h5 class="widget-user-desc">{{$user->role}}</h5>
        </div>
        <div class="box-footer no-padding">
        	<div class="row">
        		<div class="col-md-4">
		            <ul class="nav nav-stacked">
		            	<li><a>Nom : <b>{{$user->full_name}}</b></a></li>
		                <li><a>Contact : <b>{{$user->contact}}</b></a></li>
		                <li><a>E-mail : <b>{{$user->email}}</b></a></li>
		            </ul>
		        </div>
		        <div class="col-md-4">
		            <ul class="nav nav-stacked">
		                <li><a>Login : <b>{{$user->login}}</b></a></li>
		                <li><a><button onClick="updatePasswordRow({{$user->id}});" class="btn btn-primary">Modifier mot de passe</button></a></li>
		            </ul>
		        </div>
		        <div class="col-md-4">
		            <ul class="nav nav-stacked">
		                <li><a>Inscrit le : <b>{{$user->created}}</b></a></li>
		                <li><a>Etat compte : <b>{{$user->statut_compte= 1 ? 'Actif':'Désactivé'}}</b></a></li>
		                <li><a>Derni&egrave;re connexion : <b>{{$user->last_login}}</b></a></li>
		            </ul>
		        </div>
		    </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	function updatePasswordRow(idUser) {
        window.location.href = basePath + "/auth/update-password-page";
    };
</script>
@endsection