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
                    Liste des transferts de stock
                    @if (isset($searchBy) && $searchBy['research'] == 'Periode')
                        par période
                    @endif
                    </strong>
                </h3>
                <div class="infos-destockage">
                    @if (isset($searchBy) && $searchBy['research'] == 'Periode')
                            <h4><strong>Date du début : </strong>{{ $searchBy['date-debut'] }}</h4>
                            <h4><strong>Date du fin : </strong>{{ $searchBy['date-fin'] }}</h4>
                    @else
                        <h4>{{ $configs->commune_compagnie }} le {{ date('d-m-Y') }}</h4>
                    @endif
                </div>
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">N°</th>
                        <th scope="col" class="text-center">Date</th>
                        <th scope="col" class="text-left">Dépôt de départ</th>
                        <th scope="col" class="text-left">Dépôt d'arrivée</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfert_stocks as $transfert_stock)
                    <tr>
                        <td class="text-left">{{ $transfert_stock['index'] }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($transfert_stock['date_transfert'])) }}</td>
                        <td class="text-left">{{ $transfert_stock['depot_depart']['libelle_depot'] }}</td>
                        <td class="text-left">{{ $transfert_stock['depot_arrivee']['libelle_depot'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($Total, 0, ',', ' ') }} Transfert(s)</h3>
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
