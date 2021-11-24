@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <h3 class="title">
                <strong>
                Liste des comptes
                </strong>
            </h3>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">N° du compte</th>
                        <th scope="col" class="text-left">Type</th>
                        <th scope="col" class="text-left" width="50%">Détenteur</th>
                        <th scope="col" class="text-right">Solde</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr>
                        <td class="text-left">{{ $row['numero_compte'] }}</td>
                        @if ($row['carte'] != null)
                            <td class="text-left">Compte lié à la carte de fidélité <strong>{{ $row['carte']['libelle_carte_fidelite'] }}</strong></td>
                        @else
                            <td class="text-left">Compte client</td>
                        @endif
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
