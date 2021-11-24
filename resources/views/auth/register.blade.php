<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->

    <title>SMART-SFV</title>

    <!-- Scripts -->
   <script src="{{asset('assets/plugins/jQuery/jquery-3.1.0.min.js')}}"></script>
           <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
   <script src="js/bootstrap.js" type="text/javascript"></script>
   <script src="{{asset('assets/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
   <script src="{{asset('adminLte/dist/js/adminlte.js')}}" type="text/javascript"></script>
   <script src="{{asset('assets/js/jquery.gritter.min.js')}}" type="text/javascript"></script>
   <script src="{{asset('assets/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{asset('adminLte/plugins/iCheck/square/blue.css') }}" rel="stylesheet">
    <link href="{{asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{asset('assets/template/admin/css/AdminLTE.min.css') }}" rel="stylesheet">
    <link href="{{asset('assets/template/admin/css/skins/skin-blue.min.css') }}" rel="stylesheet">
    <link href="{{asset('adminLte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet">
</head>
<body class="hold-transition register-page">
    
        <div class="register-box">
    <div class="register-logo">
     <a href="{{route('login')}}">SMART-<b>SFV</b></a>
    </div>
    <div class="register-box-body">
      <p class="login-box-msg">Enregistrer votre nouveau mot de passe</p>
      <form method="POST" action="{{ route('update_password') }}">
                          @csrf
         <input type="text" class="hidden" name="confirmation_token" value="{{$token}}">
         <input type="text" class="hidden" name="id" value="{{$id}}">
<!--         <div class="form-group has-feedback">
            <input type="text" name="email" id="email" value="{{old('email')}}" class="form-control" placeholder="Votre nouveau pseudo" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
           @error('pseudo')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
           @enderror
        </div>-->
        <div class="form-group has-feedback">
            <input type="password" name="password" minlength="8" id="password" class="form-control" placeholder="Votre mot de passe" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
           @error('password')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
           @enderror
        </div>
        <div class="form-group has-feedback">
            <input minlength="8" id="password-confirm" type="password" class="form-control form-control-rounded" name="password_confirmation" autocomplete="new-password" placeholder="Confirmer le nouveau mot de passe" required>
          <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-12">
              <button type="submit" id="buton_action" class="btn btn-primary btn-block btn-flat">Valider</button>
          </div>
          <!-- /.col -->
        </div>
      </form><br/>
        <div class="alert alert-danger">
      <h4><i class="icon fa fa-warning"></i> Alert format mot de passe!</h4>
            <p> . Le mot de passe doit &ecirc;tre 8 caract&egrave;res minimum</p>
    <p> . Le mot de passe doit comporter au moins une majuscule (A–Z)</p>
    <p> . Le mot de passe doit comporter au moins une minuscule (a–z)</p>
    <p> . Le mot de passe doit comporter au moins un nombre (0–9)</p>
    <p> . Le mot de passe doit comporter au moins un caract&egrave;re non alphanum&eacute;rique (!, $, @, ou %)</p>
    </div>
      <!--<a href="groupsmarty.com" class="text-center b">Smarty-Technologie</a>-->
    </div>
        </div>

      </div>
</body>
</html>