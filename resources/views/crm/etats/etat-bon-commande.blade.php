@extends('crm.etats.layouts.etat-basic')
@section('body')
    <body>
        <!-- Header -->
        @include('crm.etats.layouts.etat-header')
        <!-- Fin Header -->
        <div class="container-table">
            <div class="title-container facture-title">
                <h3 class="title">
                    <strong>
                    Bon de commande N° {{ $bonCommande['numero_bon'] }}
                    </strong>
                </h3>
                <div class="infos-destockage">
                    <h4>{{ $configs->commune_compagnie }} le {{ date('d-m-Y', strtotime($bonCommande['date_bon'])) }}</h4>
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
                        <th scope="col" class="text-center" width="70%">Article</th>
                        <th scope="col" class="text-center">Qté / Btle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articlesBon as $article)
                    <tr>
                        <td class="text-center">{{ $article['index'] }}</td>
                        <td class="text-left">{{ $article['article']['libelle_article'] }}</td>
                        <td class="text-center">{{ $article['quantite_demande'] }}</td>
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
            </div>
            @include('crm.etats.layouts.etat-footer')
        </div>
        <script>
            // Lancer l'impression
            imprimeEtat();
        </script>
    </body>
@endsection
