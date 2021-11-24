@extends('layouts.app')
@section('content')
@foreach($depots as $depot)
<a onclick="event.preventDefault(); document.getElementById('logout-form' + {{$depot->id}}).submit();" style="cursor: pointer;">
<div class="col-md-4">
    <div class="box box-primary box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{$depot->libelle_depot}}</h3>
        </div>
    </div>
</div>
</a>
<form id="logout-form{{$depot->id}}" action="{{route('boutique.vu-liste-article-by-unite-in-depot')}}" method="POST" style="display: none;">
    {{ csrf_field() }}
    <input type="hidden" name="depot_id" value="{{$depot->id}}">
</form>
@endforeach
@endsection