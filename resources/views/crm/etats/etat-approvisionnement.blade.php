@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <div class="title-container">
                <div class="facture-title">
                    <h3 class="title">
                        <strong>
                        Fiche d'approvisionnement
                        </strong>
                    </h3>
                    <div class="infos-destockage">
                        <h4 style="margin-bottom: 20px;">{{ $configs->commune_compagnie }} le {{ date('d-m-Y') }}</h4>
                        <h4><strong>Date :</strong> {{ date('d-m-Y', strtotime($info_approvisionnement['date_approvisionnement'])) }}</h4>
                        @if ($info_approvisionnement['fournisseur'] != null)
                            <h4><strong>Fournisseur :</strong> {{  $info_approvisionnement['fournisseur']['full_name_fournisseur'] }}</h4>
                        @endif
                    </div>
                </div>
                <h4><strong>Dépôt :</strong> {{ $info_approvisionnement['depot']['libelle_depot'] }}</h4>
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">N°</th>
                        <th scope="col" class="text-center" width="40%">Article</th>
                        <th scope="col" class="text-center">Stock initial</th>
                        <th scope="col" class="text-center" width="8%">Qté / Btle</th>
                        <th scope="col" class="text-center">Prix d'achat TTC</th>
                        <th scope="col" class="text-center">Prix total achat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($article_approvisionnements as $index => $article)
                    <tr>
                        <td class="text-left">{{ $article['index'] }}</td>
                        <td class="text-left">{{ $article['article']['libelle_article'] }}</td>
                        <td class="text-right">{{ $article['quantite_disponible'] }}</td>
                        <td class="text-right">{{ $article['quantite'] }}</td>
                        <td class="text-right">{{ number_format($article['prix_achat_ttc'], 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format(($article['prix_achat_ttc'] * $article['quantite']), 0, ',', ' ') }}</td>
                    </tr>
                    <!--@if ($article['index'] == 7)
                        <div style="page-break-after: always;"></div>
                    @endif-->
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>ARTICLE(S) :</h3>
                    <h3>{{ $Total }} Article(s)</h3>
                </div>
                <div>
                    <h3>QUANTITE TOTALE :</h3>
                    <h3>{{ $qteTotal }} Unité(s) / Btle</h3>
                </div>
                <div class="montant-total">
                    <h3>MONTANT TOTAL :</h3>
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
