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
                    Liste des articles
                    </strong>
                </h3>
                <div class="infos-destockage">
                    @if (isset($searchBy))
                        @if ($searchBy['research'] == 'Categorie')
                            <h4><strong>Date :</strong> {{ date('d-m-Y') }}</h4>
                            <h4><strong>Catégorie :</strong> {{ $searchBy['value'] }}</h4>
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
                        <th scope="col" class="text-left">Code barre</th>
                        <th scope="col" class="text-left" width="50%">Article</th>
                        <th scope="col" class="text-left">Catégorie</th>
                        <th scope="col" class="text-left">Sous catégorie</th>
                        <th scope="col" class="text-right">Prix Achat TTC</th>
                        <th scope="col" class="text-right">Prix Achat HT</th>
                        <th scope="col" class="text-right">TVA</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 100; $i++)
                    @foreach ($articles as $article)
                    <tr>
                        <td class="text-left">{{ $article['index'] }}</td>
                        <td class="text-left">{{ $article['code_barre'] }}</td>
                        <td class="text-left">{{ $article['libelle_article'] }}</td>
                        <td class="text-left">{{ $article['categorie']['libelle_categorie'] }}</td>
                        @if ($article['sous_categorie'] != null)
                            <td class="text-left">{{ $article['sous_categorie']['libelle_sous_categorie'] }}</td>
                        @else
                            <td class="text-left"></td>
                        @endif
                        <td class="text-right">{{ number_format($article['prix_achat_ttc'], 0, ',', ' ') }} </td>
                        <td class="text-right">{{ number_format($article['prix_achat_ttc'], 0, ',', ' ') }} </td>
                        <td class="text-right">{{ $article['tva'] * 100 }} %</td>
                    </tr>
                    @endforeach
                    @endfor
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($Total, 0, ',', ' ') }} Article(s)</h3>
                </div>
                <div class="montant-total">
                    <h3>MONTANT TOTAL TTC :</h3>
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
