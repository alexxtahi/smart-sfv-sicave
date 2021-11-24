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
                    Soldes des clients
                    </strong>
                </h3>
                <div class="infos-destockage">
                    @if (isset($searchBy))
                        <h4><strong>Date :</strong> {{ date('d-m-Y') }}</h4>
                        @if ($searchBy['research'] == 'Pays')
                            <h4><strong>Pays :</strong> {{ $searchBy['value'] }}</h4>
                        @elseif ($searchBy['research'] == 'Fournisseur')
                            <h4><strong>Fournisseur :</strong> {{ $searchBy['value'] }}</h4>
                        @elseif ($searchBy['research'] == 'Periode')
                            <h4><strong>Date de début :</strong> {{ $searchBy['date-debut'] }}</h4>
                            <h4><strong>Date de fin :</strong> {{ $searchBy['date-fin'] }}</h4>
                        @elseif ($searchBy['research'] == 'Periode-Fournisseur')
                            <h4><strong>Fournisseur :</strong> {{ $searchBy['value'] }}</h4>
                            <h4><strong>Date de début :</strong> {{ $searchBy['date-debut'] }}</h4>
                            <h4><strong>Date de fin :</strong> {{ $searchBy['date-fin'] }}</h4>
                        @endif
                    @else
                        <h4>{{ $configs->commune_compagnie }} le {{ date('d-m-Y') }}</h4>
                    @endif
                </div>
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">N°</th>
                        <th scope="col" class="text-center">Date</th>
                        <th scope="col" class="text-center">Facture</th>
                        <th scope="col" class="text-center">Client</th>
                        <th scope="col" class="text-center">Contact</th>
                        <th scope="col" class="text-center">Crédit</th>
                        <th scope="col" class="text-center">Acompte</th>
                        <th scope="col" class="text-center">Doit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventes as $vente)
                    <tr>
                        <td class="text-left">{{ $vente['index'] }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($vente['date_vente'])) }}</td>
                        <td class="text-left">{{ $vente['numero_facture'] }}</td>
                        <td class="text-left">{{ $vente['client']['full_name_client'] }}</td>
                        <td class="text-left">{{ $vente['client']['contact_client'] }}</td>
                        <td class="text-right">{{ number_format($vente['sommeTotale'], 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($vente['sommeAcompte'], 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format(($vente['sommeTotale'] - $vente['sommeAcompte']), 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>Total crédit :</h3>
                    <h3>{{ number_format($vente['sommeTotale'], 0, ',', ' ') }} vente(s)</h3>
                </div>
                <div>
                    <h3>Total accompte :</h3>
                    <h3>{{ number_format($vente['sommeAcompte'], 0, ',', ' ') }} vente(s)</h3>
                </div>
                <div>
                    <h3>Total du :</h3>
                    <h3>{{ number_format(($vente['sommeTotale'] - $vente['sommeAcompte']), 0, ',', ' ') }} vente(s)</h3>
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
