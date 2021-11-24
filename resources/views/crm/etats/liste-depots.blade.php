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
                    Liste des dépôts
                    </strong>
                </h3>
                <div class="infos-destockage">
                    <h4><strong>Date :</strong></h4>
                    <h4>{{ date('d-m-Y') }}</h4>
                </div>
            </div>
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">N°</th>
                        <th scope="col" class="text-left">Dépôt</th>
                        <th scope="col" class="text-left">Adresse du dépôt</th>
                        <th scope="col" class="text-left">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($depots as $depot)
                    <tr>
                        <td class="text-left">{{ $depot['index'] }}</td>
                        <td class="text-left">{{ $depot['libelle_depot'] }}</td>
                        <td class="text-left">{{ $depot['adresse_depot'] }}</td>
                        <td class="text-left">{{ $depot['contact_depot'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($Total, 0, ',', ' ') }} dépôt(s)</h3>
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
