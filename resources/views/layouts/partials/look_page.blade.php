@extends('layouts.app')
@section('content')
        <script src="{{asset('assets/plugins/jQuery/jquery-3.1.0.min.js')}}"></script>

<div class="content">
    <div class="lockscreen-logo">
        <b>STOP</b>
    </div>
    <div class="lockscreen-item">
        <div class="lockscreen-image">
            <img class="img-responsive text-center" src="{{asset('images/interdit.jpg')}}" style="width: 30%; height: 30%;">
        </div>
    </div><br><br>
    <p class="text-center h2">Vous n'avez pas acc&egrave;ss &agrave; cette page !<br/>Merci de contacter l'administrateur ou <a href="{{route('home')}}">aller &agrave; l'accueil</a></p>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnModalAjout").hide();
        $("#seci").hide();
    });
</script>
@endsection