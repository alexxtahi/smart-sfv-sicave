@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{$titleControlleur}}</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>D&eacute;p&ocirc;t</th>
                            <th>Caisse</th>
                            <th style="width: 70px; text-align: center">Etat</th>
                            <th style="width: 70px; text-align: center">Acc&eacute;der</th>
                        </tr>
                        @foreach($caisses as $caisse)
                            <tr>
                                <td>{{$caisse->depot->libelle_depot}}</td>
                                <td>{{$caisse->libelle_caisse}}</td>
                                @if($caisse->ouvert==1)
                                <td><span class="label label-success">Ouverte</span></td>
                                @else
                                <td><span class="label label-danger">Fermée</span></td>
                                @endif
                                <td style="text-align: center"><a onclick="event.preventDefault(); document.getElementById('logout-form' + {{$caisse->id}}).submit();" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Accéder à cette caisse"><i class="fa fa-folder-open"></i></a>
                                <form id="logout-form{{$caisse->id}}" action="{{ route('boutique.ponit-caisse-vu-by-admin-gerant') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="caisse_id" value="{{$caisse->id}}">
                                </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

