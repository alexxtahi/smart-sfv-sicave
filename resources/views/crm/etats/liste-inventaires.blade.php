@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <div class="title-container">
                <h3 class="title">
                    <strong>
                    Liste des inventaires
                    @if (isset($searchBy))
                        @if ($searchBy['research'] == 'Periode')
                            par période
                        @elseif ($searchBy['research'] == 'Depot')
                            par dépôt
                        @elseif ($searchBy['research'] == 'PeriodeDepot')
                            par période & dépôt
                        @endif
                    @endif
                    </strong>
                </h3>
                <div class="infos-inventaire">
                    @if (isset($searchBy))
                        @if ($searchBy['research'] == 'Depot')
                            <h4><strong>Date : </strong>{{ date('d-m-Y') }}</h4>
                            <h4><strong>Dépôt : </strong>{{ $searchBy['depot'] }}</h4>
                        @elseif ($searchBy['research'] == 'Periode')
                            <h4><strong>Date du début : </strong>{{ $searchBy['date-debut'] }}</h4>
                            <h4><strong>Date du fin : </strong>{{ $searchBy['date-fin'] }}</h4>
                        @elseif ($searchBy['research'] == 'PeriodeDepot')
                            <h4><strong>Dépôt : </strong>{{ $searchBy['depot'] }}</h4>
                            <h4><strong>Date du début : </strong>{{ $searchBy['date-debut'] }}</h4>
                            <h4><strong>Date du fin : </strong>{{ $searchBy['date-fin'] }}</h4>
                        @endif
                    @else
                        <h4><strong>Date :</strong></h4>
                        <h4>{{ date('d-m-Y') }}</h4>
                    @endif
                </div>
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">N°</th>
                        <th scope="col" class="text-center">Date</th>
                        <th scope="col" class="text-left">Période</th>
                        <th scope="col" class="text-left">Dépôt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventaires as $inventaire)
                    <tr>
                        <td class="text-left">{{ $inventaire['index'] }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($inventaire['date_inventaire'])) }}</td>
                        <td class="text-left">{{ $inventaire['libelle_inventaire'] }}</td>
                        <td class="text-left">{{ $inventaire['depot']['libelle_depot'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($Total, 0, ',', ' ') }} Inventaire(s)</h3>
                </div>
            </div>
            @include('crm.etats.layouts.etat-footer')
        </div>
        <script>
            // Lancer l'impression
            imprimeEtat();
        </script>
    </body>
@endsection
