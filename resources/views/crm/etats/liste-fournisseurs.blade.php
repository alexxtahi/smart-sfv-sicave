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
                    Liste des fournisseurs
                    </strong>
                </h3>
                <div class="infos-destockage">
                    @if (isset($searchBy))
                        @if ($searchBy['research'] == 'Pays')
                            <h4><strong>Date :</strong> {{ date('d-m-Y') }}</h4>
                            <h4><strong>Pays :</strong> {{ $searchBy['value'] }}</h4>
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
                        <th scope="col" class="text-left" width="50%">Fournisseur</th>
                        <th scope="col" class="text-left">Contact</th>
                        <th scope="col" class="text-left">Pays</th>
                        <th scope="col" class="text-left">Email</th>
                        <th scope="col" class="text-left">Adresse</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fournisseurs as $fournisseur)
                    <tr>
                        <td class="text-left">{{ $fournisseur['index'] }}</td>
                        <td class="text-left">{{ $fournisseur['full_name_fournisseur'] }}</td>
                        <td class="text-left">{{ $fournisseur['contact_fournisseur'] }}</td>
                        <td class="text-left">{{ $fournisseur['nation']['libelle_nation'] }}</td>
                        <!-- Email -->
                        @if ($fournisseur['email_fournisseur'] != null)
                            <td class="text-left">{{ $fournisseur['email_fournisseur'] }}</td>
                        @else
                            <td class="text-left"></td>
                        @endif
                        <!-- Adresse -->
                        @if ($fournisseur['adresse_fournisseur'] != null)
                            <td class="text-left">{{ $fournisseur['adresse_fournisseur'] }}</td>
                        @else
                            <td class="text-left"></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="under-table">
                <div>
                    <h3>TOTAL :</h3>
                    <h3>{{ number_format($Total, 0, ',', ' ') }} fournisseur(s)</h3>
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
