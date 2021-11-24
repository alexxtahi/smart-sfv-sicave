@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <h3 class="title">
                @if (isset($searchBy) && $searchBy == 'carte')
                    <strong>
                    Liste des comptes liés à la carte :
                    </strong>
                    {{ $rows[0]['carte']['libelle_carte_fidelite'] }}
                @elseif (isset($searchBy) && $searchBy == 'client')
                    <strong>
                    Liste des comptes du client :
                    </strong>
                    {{ $rows[0]['client']['full_name_client'] }}
                @else
                    <strong>
                    Liste des comptes cartes de fidélité
                    </strong>
                @endif
            </h3>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">N° du compte</th>
                        <th scope="col" class="text-left">Carte</th>
                        <th scope="col" class="text-left" width="50%">Client</th>
                        <th scope="col" class="text-right">Solde</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr>
                        <td class="text-left">{{ $row['numero_compte'] }}</td>
                        <td class="text-left">{{ $row['carte']['libelle_carte_fidelite'] . ' - ' . ($row['carte']['reduction'] * 100) . '%' }}</td>
                        <td class="text-left">{{ $row['client']['full_name_client'] }}</td>
                        <td class="text-right">{{ number_format($row['entree'], 0, ',', ' ') }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>COMPTES :</h3>
                    <h3>{{ $Total }} Compte(s)</h3>
                </div>
                <div class="montant-total">
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</h3>
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