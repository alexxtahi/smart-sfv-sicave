@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <div class="title-container facture-title">
                <div>
                    <h3 class="title">
                        <strong>
                        Facture N° {{ $infosVente['numero_facture'] }}
                        </strong>
                    </h3>
                </div>
                <div class="infos-destockage">
                    <h4 style="margin-bottom: 20px;">{{ $configs->commune_compagnie }} le {{ date('d-m-Y',strtotime($infosVente['date_vente'])) }}</h4>
                    <h4><strong>Client :</strong> {{ $infosVente['client']['full_name_client'] }}</h4>
                    <h4><strong>Adresse :</strong> {{ $infosVente['client']['adresse_client'] }}</h4>
                    <h4><strong>Contact :</strong> {{ $infosVente['client']['contact_client'] }}</h4>
                </div>
                <!--<h4><strong>Dépôt :</strong> {{ $infosVente['depot']['libelle_depot'] }}</h4>-->
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" width="40%">Article</th>
                        <th scope="col" class="text-center" width="8%">Qté / Btle</th>
                        <th scope="col" class="text-center">Prix</th>
                        <th scope="col" class="text-center">Taxes</th>
                        <th scope="col" class="text-center">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articlesVente as $article)
                    <tr>
                        <td class="text-left">{{ $article['article']['libelle_article'] }}</td>
                        <td class="text-center">{{ $article['quantite'] }}</td>
                        <td class="text-right">{{ number_format($article['prix'], 0, ',', ' ') }}</td>
                        <td class="text-center">TVA {{ $article['tva'] * 100 }}%</td>
                        <td class="text-right">{{ number_format(($article['prix'] * $article['quantite']), 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
            <div class="montant-total">
                <h3>Montant HT :</h3>
                <h3>{{ number_format($infosVente['montantHT'], 0, ',', ' ') }} FCFA</h3>
            </div>
            <div>
                <h3>Montant TVA :</h3>
                <h3>{{ number_format(($infosVente['montantTTC'] - $infosVente['montantHT']), 0, ',', ' ') }} FCFA</h3>
            </div>
            <div class="montant-total">
                <h3>Montant total TTC :</h3>
                <h3>{{ number_format($infosVente['montantTTC'], 0, ',', ' ') }} FCFA</h3>
            </div>
            <!--<div class="montant-total">
                <h3>Montant TTC :</h3>
                <h3>{{ number_format($infosVente['montantTTC'], 0, ',', ' ') }} FCFA</h3>
            </div>-->
            <div>
                <h3>Arrêtée de la présente facture à la somme de <strong>{{ ucfirst(NumberToLetter(round($infosVente['montantTTC']))) }} Francs CFA</strong></h3>
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
