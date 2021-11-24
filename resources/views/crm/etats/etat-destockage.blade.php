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
                    Fiche de déstockage
                    </strong>
                    <h4 align="right">{{ $configs->commune_compagnie }} le {{ date('d-m-Y', strtotime($infosDestockages['date_destockage'])) }}</h4>
                </h3>
                <div class="infos-destockage">
                    <h4><strong>Dépôt :</strong> {{ $infosDestockages['depot']['libelle_depot'] }}</h4>
                    <h4><strong>Motif :</strong> {{ $infosDestockages['motif'] }}</h4>
                </div>
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" width="8%">N°</th>
                        <th scope="col" class="text-center" width="70%">Article</th>
                        <th scope="col" class="text-center">Stock initial</th>
                        <th scope="col" class="text-center">Qté / Btle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articlesDestocker as $article)
                    <tr>
                        <td class="text-center">{{ $article['index'] }}</td>
                        <td class="text-left">{{ $article['article']['libelle_article'] }}</td>
                        <td class="text-center">{{ $article['qteEnStock'] }}</td>
                        <td class="text-center">{{ $article['quantite_destocker'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>ARTICLE(S) :</h3>
                    <h3>{{ $Total }} Article(s)</h3>
                </div>
                <div class="montant-total">
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($qteTotal, 0, ',', ' ') }} Unité(s) / Btle</h3>
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
