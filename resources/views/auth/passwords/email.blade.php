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
            <a href="{{route('login')}}">SMART-<b>SFV</b></a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">R&eacute;initialiez votre mot de passe</p>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="{{route('password.email')}}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Votre E-mail pour le lien de réinitialisation">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12 offset-md-12">
                                <button type="submit" class="btn btn-block btn-flat btn-primary">
                                    {{ __('Envoyer') }}
                                </button>
                            </div>
                        </div>
                    </form>
        </div>
    </div>
</div>
</body>
</html>
