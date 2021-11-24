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
                        Bon de réception N° {{ $bonCommande['numero_bon'] }}
                        </strong>
                    </h3>
                    <h4><strong>Date du bon : </strong>{{ date('d-m-Y', strtotime($bonCommande['date_bon'])) }}</h4>
                    <h4><strong>Date de réception : </strong>{{ date('d-m-Y', strtotime($bonCommande['date_reception'])) }}</h4>
                </div>
                <div class="infos-destockage">
                    <h4 style="margin-bottom: 20px;">{{ $configs->commune_compagnie }} le {{ date('d-m-Y') }}</h4>
                    <h4>
                        <strong>Etat du bon : </strong>
                        @switch($etatBon)
                            @case(1)
                                <span class="text-bold">Brouillon</span>
                                @break
                            @case(2)
                                <span class="text-bold text-orange">Enregistré</span>
                                @break
                            @case(3)
                                <span class="text-bold text-red">Refusé</span>
                                @break
                            @case(4)
                                <span class="text-bold text-green">Receptionné</span>
                                @break
                            @case(5)
                                <span class="text-bold">Facturé</span>
                                @break
                            @default
                                <span class="text-bold">Brouillon</span>
                                @break
                        @endswitch
                    </h4>
                    @if ($bonCommande['fournisseur'] != null)
                        <h4><strong>Fournisseur : </strong>{{ $bonCommande['fournisseur']['full_name_fournisseur'] }}</h4>
                        <h4><strong>Contact : </strong>{{ $bonCommande['fournisseur']['contact_fournisseur'] }}</h4>
                        @if ($bonCommande['fournisseur']['adresse_fournisseur'] != null)
                            <h4><strong>Adresse : </strong>{{ $bonCommande['fournisseur']['adresse_fournisseur'] }}</h4>
                        @endif
                    @endif
                </div>
            </div>

            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" width="8%">N°</th>
                        <th scope="col" class="text-center" width="50%">Article</th>
                        <th scope="col" class="text-center" width="8%">Qté / Btle<br>demandée</th>
                        <th scope="col" class="text-center" width="8%">Qté / Btle<br>reçue</th>
                        <th scope="col" class="text-center">Prix d'achat TTC</th>
                        <th scope="col" class="text-center">Montant TTC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articlesBon as $article)
                    <tr>
                        <td class="text-center">{{ $article['index'] }}</td>
                        <td class="text-left">{{ $article['article']['libelle_article'] }}</td>
                        <td class="text-right">{{ $article['quantite_demande'] }}</td>
                        <td class="text-right">{{ $article['quantite_recu'] }}</td>
                        <td class="text-right">{{ number_format($article['prix_article'], 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format(($article['prix_article'] * $article['quantite_recu']), 0, ',', ' ') }}</td>
                    </tr>
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
                <div>
                    <h3>Réception de bon d'une valeur de <strong>{{ ucfirst(NumberToLetter(round($montantTotal))) }} Francs CFA</strong></h3>
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
