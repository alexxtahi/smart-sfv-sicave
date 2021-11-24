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
                    Liste des approvisionnements
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
                        <th scope="col" class="text-left">Dépôt</th>
                        <th scope="col" class="text-left" width="50%">Fournisseur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($approvisionnements as $approvisionnement)
                    <tr>
                        <td class="text-left">{{ $approvisionnement['index'] }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($approvisionnement['date_approvisionnement'])) }}</td>
                        <td class="text-left">{{ $approvisionnement['depot']['libelle_depot'] }}</td>
                        @if ($approvisionnement['fournisseur'] != null)
                            <td class="text-left">{{ $approvisionnement['fournisseur']['full_name_fournisseur'] }}</td>
                        @else
                            <td class="text-left"></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($Total, 0, ',', ' ') }} Approvisionnement(s)</h3>
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
