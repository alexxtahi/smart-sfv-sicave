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
                        @if ($infosDevis['proformat_devis'] == 'facture_proforma')
                            Facture proforma
                        @endif
                        {{ $infosDevis['numero_devis'] }}
                        </strong>
                    </h3>
                </div>
                <div class="infos-destockage">
                    <h4 style="margin-bottom: 20px;">{{ $configs->commune_compagnie }} le {{ date('d-m-Y',strtotime($infosDevis['date_devis'])) }}</h4>
                    <h4><strong>Client :</strong> {{ $infosDevis['client']['full_name_client'] }}</h4>
                    <h4><strong>Adresse :</strong> {{ $infosDevis['client']['adresse_client'] }}</h4>
                    <h4><strong>Contact :</strong> {{ $infosDevis['client']['contact_client'] }}</h4>
                </div>
                <!--<h4><strong>Dépôt :</strong> {{ $infosDevis['depot']['libelle_depot'] }}</h4>-->
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" width="50%">Article</th>
                        <th scope="col" class="text-center" width="8%">Qté / Btle</th>
                        <th scope="col" class="text-center">Prix</th>
                        <th scope="col" class="text-center">Taxes</th>
                        <th scope="col" class="text-center">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articlesDevis as $article)
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
                <h3>{{ number_format($infosDevis['montantHT'], 0, ',', ' ') }} FCFA</h3>
            </div>
            <div>
                <h3>Montant TVA :</h3>
                <h3>{{ number_format($infosDevis['montantTVA'], 0, ',', ' ') }} FCFA</h3>
            </div>
            <div class="montant-total">
                <h3>Montant total TTC :</h3>
                <h3>{{ number_format($infosDevis['montantTTC'], 0, ',', ' ') }} FCFA</h3>
            </div>
            <!--<div class="montant-total">
                <h3>Montant TTC :</h3>
                <h3>{{ number_format($infosDevis['montantTTC'], 0, ',', ' ') }} FCFA</h3>
            </div>-->
            <div>
                <h3>
                @if ($infosDevis['proformat_devis'] == 'facture_proforma')
                    Arrêtée de la présente facture proforma à la somme de
                @else
                    Devis d'une valeur de
                @endif
                <strong>{{ ucfirst(NumberToLetter(round($infosDevis['montantTTC']))) }} Francs CFA</strong></h3>
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
