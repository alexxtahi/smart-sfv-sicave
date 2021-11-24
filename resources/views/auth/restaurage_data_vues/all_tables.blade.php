@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">

        <table id="table" class="table table-info table-striped box box-info"
               data-pagination="true"
               data-search="true"
               data-toggle="table"
               data-url="{{route('auth.liste_all_tables')}}"
               data-unique-id="token"
               data-show-toggle="false">
            <thead>
                <tr>
                    <th data-formatter="nomFormatter" data-searchable="true">Nom de la table </th>
                    <th data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                </tr>
            </thead>
        </table>

<script type="text/javascript">
    function nomFormatter(id, row){
        var tableName = row.Tables_in_smartsfv;
        var table = tableName.replace("_",' ');
        return table.toUpperCase();
    }
    
    function optionFormatter(id, row){ 
        if(row.Tables_in_smartsfv === 'failed_jobs' || row.Tables_in_smartsfv === 'migrations' || row.Tables_in_smartsfv === 'password_resets'|| row.Tables_in_smartsfv === 'ventes')
        {
          return '<button class="btn btn-xs btn-danger disabled"><i class="fa fa-stop"></i></button>';  
        }else{
           return '<a class="btn btn-xs btn-success" title="Voir la table" href="../auth/one_table/' + row.Tables_in_smartsfv + '"><i class="fa fa-eye"></i></a>';
        }
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
