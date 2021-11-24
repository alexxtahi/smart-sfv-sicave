@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <div class="title-container">
                <div style="display: flex; justify-content: space-between;">
                    <h3 class="title">
                        <strong>
                        Timbre fiscal
                        </strong>
                    </h3>
                    <h4 style="margin-bottom: 20px;">{{ $configs->commune_compagnie }} le {{ date('d-m-Y') }}</h4>
                </div>
            </div>
            <table class="table table-striped table-bordered custom-table" style="font-size: 17px;">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">N° Ticket</th>
                        <th scope="col" class="text-center">Date</th>
                        <th scope="col" class="text-center">Montant HT</th>
                        <th scope="col" class="text-center">Montant TTC</th>
                        <th scope="col" class="text-center">Net à payer</th>
                        <th scope="col" class="text-center">Timbre</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventes as $vente)
                    <tr>
                        <td class="text-left">{{ $vente->numero_ticket }}</td>
                        <td class="text-center">{{ $vente->date_ventes }}</td>
                        <td class="text-right">{{ number_format($vente->totalHT, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($vente->montantTTC, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($vente->montantTTC, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($timbre, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
            <div class="montant-total">
                <h3>Total HT :</h3>
                <h3>{{ number_format($totalHT, 0, ',', ' ') }} FCFA</h3>
            </div>
            <div class="montant-total">
                <h3>Total TTC :</h3>
                <h3>{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</h3>
            </div>
            <div class="montant-total">
                <h3>Total Timbre TTC :</h3>
                <h3>{{ number_format($totalTimbre, 0, ',', ' ') }} FCFA</h3>
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
