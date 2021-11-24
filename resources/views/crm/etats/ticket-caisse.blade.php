<!DOCTYPE html>
<html>
    <head>
        <!-- Scripts -->
        <script src="{{asset('js/script.js')}}"></script>
        <script src="{{asset('assets/plugins/angular/angular.js')}}"></script>
        <script src="{{asset('assets/plugins/jQuery/jquery-3.1.0.min.js')}}"></script>
        <script src="{{asset('assets/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminLte/dist/js/adminlte.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminLte/plugins/pace/pace.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('assets/js/jquery.cookies.js')}}" type="text/javascript"></script>
        <script src="{{asset('assets/js/jquery.gritter.min.js')}}" type="text/javascript"></script>

        <!-- Favicon -->
        <link rel="stylesheet" type="text/css" href="{{asset('images/logo1.png') }}">

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/select2/select2-bootstrap.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/bootstrap/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/template/admin/css/AdminLTE.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/template/admin/css/skins/skin-blue.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/pace/pace.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/jquery.gritter.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/costumer-style.css') }}" rel="stylesheet">
        <link href="{{ asset('adminLte/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        <link href="{{ asset('adminLte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet">
        <link href="{{ asset('adminLte/plugins/pace/pace.min.css') }}" rel="stylesheet">
        <!-- Custom Style -->
        <style>
            @page {
                size: 80mm 397mm;
                margin: 2.5mm;
            }
            .company-info {
                margin-bottom: 30px;
            }
            .company-info h4 {
                margin-top: 0px;
            }
            .company-info h4:last-child {
                margin-bottom: 30px;
            }
            .header-text {
                margin: 0px 10px 20px 10px;
            }
            .header-text h3 {
                margin-top: 0px !important;
            }
            .logo {
                object-fit: contain;
            }
            .main-container {
                display: flex;
                justify-content: center;
            }
            .under-table {
                text-transform: uppercase;
                margin: 0px 10px 0px 10px;
            }
            .under-table-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }
            .under-table-info h5 {
                margin: 0px;
            }
            .under-table-info:first-child {
                margin-top: 20px;
            }
            .custom-table {
                margin: 0px 10px 0px 10px;
            }
            .montant-total h5 {
                font-weight: 900 !important;
            }
            footer {
                margin-top: 30px;
            }
            .barcode {
                width: 50mm;
                height: auto;
                object-fit: contain;
            }
        </style>
    </head>
    <script type="text/php">
        if (isset($pdf)){
            $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
    <body style="background: center / cover url('{{ asset("assets/img/carte-fidelite.jpg") }}');">
        <header>
            <div class="text-center company-info">
                <img src="{{ asset($configs->logo) }}" class="logo" alt="logo" width="200" height="100">
                <h5>{{ $infosVente['depot']['libelle_depot'] . ' ' . $infosVente['depot']['adresse_depot'] }}</h5>
                <h5>Tél : {{ $infosVente['depot']['contact_depot'] }}</h5>
            </div>
            <div class="header-text">
                <h4 class="text-center"><strong>{{ $infosVente['numero_ticket'] }}</strong></h4>
                <h4>Du {{ date('d-m-Y',strtotime($infosVente['date_vente'])) }} à {{ date('H:i:s',strtotime($infosVente['date_vente'])) }}</h4>
                <h4>Servi par {{ $infosVente['full_name'] }}</h4>
                <!--<h4><strong>Caisse :</strong> {{ $infosVente['libelle_caisse'] }}</h4>-->
                <h4><strong>Dépôt :</strong> {{ $infosVente['depot']['libelle_depot'] }}</h4>
            </div>
        </header>
        <main class="main-container">
            <table class="table table-striped table-bordered custom-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">Article</th>
                        <th scope="col" class="text-right">Qté / Btle</th>
                        <th scope="col" class="text-right">Prix</th>
                        <th scope="col" class="text-right">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articlesVente as $article)
                    <tr>
                        <td class="text-left">{{ $article['article']['libelle_article'] }}</td>
                        <td class="text-center">{{ $article['quantite'] }}</td>
                        <td class="text-right">{{ number_format($article['prix'], 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format(($article['prix'] * $article['quantite']), 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </main>
        <div class="under-table">
            <div class="under-table-info">
                <h5>Règlement :</h5>
                <h5>{{ $infosVente['moyen_reglement']['libelle_moyen_reglement'] }}</h5>
            </div>
            <div class="under-table-info montant-total">
                <h5>Montant à payer :</h5>
                <h5>{{ number_format($infosVente['montant_a_payer'], 0, ',', ' ') }} FCFA</h5>
            </div>
            <div class="under-table-info montant-total">
                <h5>Montant payé :</h5>
                <h5>{{ number_format($infosVente['montant_payer'], 0, ',', ' ') }} FCFA</h5>
            </div>
            <div class="under-table-info">
                <h5>Monnaie :</h5>
                <h5>{{ number_format(($infosVente['montant_payer'] - $infosVente['montant_a_payer']), 0, ',', ' ') }} FCFA</h5>
            </div>
        </div>
        <footer class="text-center">
            <img src="data:image/png;base64,{{ $barcode }}" class="barcode" alt="Code barre">
            <i>
                <h6>
                    Fait le <strong>{{ date('d-m-Y') }}</strong> à <strong>{{ date('H:i:s') }}</strong>
                </h6>
                <h6>
                    SMART-SFV - Logiciel de gestion
                </h6>
            </i>
        </footer>
        <script>
            // Lancer l'impression
            imprimeEtat();
        </script>
    </body>
</html>
