@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <h3 class="title">
                <strong>
                Mouvements des comptes
                </strong>
            </h3>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">N° du compte</th>
                        <th scope="col" class="text-center">Date</th>
                        <th scope="col" class="text-right">Entrée</th>
                        <th scope="col" class="text-right">Sortie</th>
                        <th scope="col" class="text-right">Solde</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr>
                        <td class="text-left">{{ $row['compte']['numero_compte'] }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($row['date_mouvement'])) }}</td>
                        <td class="text-right">{{ number_format($row['compte']['entree'], 0, ',', ' ') }} </td>
                        <td class="text-right">{{ number_format($row['compte']['sortie'], 0, ',', ' ') }} </td>
                        <td class="text-right">{{ number_format($row['compte']['sortie'], 0, ',', ' ') }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL MOUVEMENTS :</h3>
                    <h3>{{ $Total }}</h3>
                </div>
                <div class="montant-total">
                    <h3>TOTAL ENTREES :</h3>
                    <h3>{{ number_format($totalEntree, 0, ',', ' ') }} FCFA</h3>
                </div>
                <div class="montant-total">
                    <h3>TOTAL SORTIES :</h3>
                    <h3>{{ number_format($totalSortie, 0, ',', ' ') }} FCFA</h3>
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
