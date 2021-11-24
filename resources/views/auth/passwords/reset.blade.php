<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->

    <title>SMART-SFV</title>

    <!-- Scripts -->
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
<body class="hold-transition login-page content">
<div class="container">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{('/')}}">SMART-<b>SFV</b></a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Entrez votre nouveau mot de passe</p>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Votre adresse E-mail" readonly>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Votre nouveau mot de passe">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmer le nouveau mot de passe">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 offset-md-12">
                                <button type="submit" class="btn btn-block btn-flat btn-primary">
                                    {{ __('Réinitialiser') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <br/>
        <div class="alert alert-warning">
      <h4><i class="icon fa fa-warning"></i> Alert format mot de passe!</h4>
            <p> . Le mot de passe doit &ecirc;tre 8 caract&egrave;res minimum</p>
    <p> . Le mot de passe doit comporter au moins une majuscule (A–Z)</p>
    <p> . Le mot de passe doit comporter au moins une minuscule (a–z)</p>
    <p> . Le mot de passe doit comporter au moins un nombre (0–9)</p>
    <p> . Le mot de passe doit comporter au moins un caract&egrave;re non alphanum&eacute;rique (!, $, @, ou %)</p>
    </div>
        </div>
    </div>
</div>
</body>
</html>
