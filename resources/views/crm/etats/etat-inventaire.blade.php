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
                    {{ $infos_inventaire['libelle_inventaire'] }}
                </strong>
            </h3>
            <div class="infos-destockage">
                <h4>{{ $configs->commune_compagnie }} le {{ date('d-m-Y') }}</h4>
                <h4><strong>Dépôt : </strong>{{ $infos_inventaire['depot']['libelle_depot'] }}</h4>
            </div>
        </div>
        <table class="table table-striped table-bordered custom-table">
            <thead>
                <tr>
                    <th scope="col" class="text-center" width="8%">N°</th>
                    <th scope="col" class="text-center">Code barre</th>
                    <th scope="col" class="text-center" width="40%">Article</th>
                    <th scope="col" class="text-center" width="8%">Qté<br>en stock</th>
                    <th scope="col" class="text-center" width="8%">Qté<br>dénombrée</th>
                    <th scope="col" class="text-center">Ecart</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details_inventaire as $details)
                <tr>
                    <td class="text-center">{{ $details['index'] }}</td>
                    <td class="text-left">{{ $details['article']['code_barre'] }}</td>
                    <td class="text-left">{{ $details['article']['libelle_article'] }}</td>
                    <td class="text-right">{{ $details['quantite_en_stocke'] }}</td>
                    <td class="text-right">{{ $details['quantite_denombree'] }}</td>
                    <td class="text-right">{{ $details['quantite_en_stocke'] - $details['quantite_denombree'] }}</td>
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
                <h3>QUANTITE TOTALE EN STOCK :</h3>
                <h3>{{ $qteTotalStock }} Unité(s) / Btle</h3>
            </div>
            <div>
                <h3>QUANTITE TOTALE DENOMBREE :</h3>
                <h3>{{ $qteTotalDenombree }} Unité(s) / Btle</h3>
            </div>
            <div>
                <h3>ECART TOTAL :</h3>
                <h3>{{ $ecartTotal }} Unité(s) / Btle</h3>
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
