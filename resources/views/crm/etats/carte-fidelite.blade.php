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
                size: 148mm 100mm;
                margin: 5mm;
            }
            .main-container {
                position: relative;
                margin: 5mm;
                z-index: 10;
            }
            .etat-logo {
                position: fixed;
                top: 5mm;
                right: 5mm;
                object-fit: contain;
            }
            .custom-table {
                border: none !important;
                background: none !important;
            }
            .container-table {
                margin-top: 20mm;
                margin-left: 5mm;
                width: 90% !important;
                display: flex;
                justify-content: space-evenly
            }
            .container-table div {

            }
            .title {
                position: fixed;
                top: 2.5mm;
                display: flex;
                justify-content: center;
                width: 100%;
            }
            footer {
                display: flex;
                justify-content: space-between;
            }
            .fixed-footer{
                width : 90%;
                position: fixed;
                bottom: 30px;
                height: 50px;
                display: flex;
                justify-content: space-between;
            }
            .solde-div {
                position: fixed;
                bottom: 10mm;
                left: 10mm;
            }
            .solde-title {
                font-size: 20px;
                font-weight: bold;
                text-decoration: underline;
                margin-bottom: 0px !important;
            }
            .solde-value {
                margin-top: 0px !important;
                font-weight: 900;
                font-size: 40px;
            }
            table h3 {
                font-size: 15px !important;
            }
            table td {
                border: none !important;
                background: none !important;
            }
            .background {
                position: fixed;
                top: 0;
                left: 0;
            }
            .barcode {
                position: fixed;
                bottom: 5mm;
                left: 10mm;
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
        <img src="{{ asset('assets/img/carte-fidelite.jpg') }}" class="background" alt="background">
        <main class="main-container">
            <img src="{{ asset($configs->logo) }}" class="etat-logo" alt="logo" width="100" height="50">
            <div class="title">
                <h4>- <strong>Carte de fidélité</strong> -</h4>
            </div>
            <div class="container-table">
                <div>
                    <h3><strong>N° du compte : </strong></h3>
                    <h3><strong>Carte : </strong></h3>
                    <h3><strong>Client : </strong></h3>
                </div>
                <div>
                    <h3>{{ $compte->numero_compte }}</h3>
                    <h3>{{ $compte->carte->libelle_carte_fidelite }}</h3>
                    <h3>{{ $compte->client->full_name_client }}</h3>
                </div>
                <div class="solde-div">
                    <h2 class="solde-title">Solde :</h2>
                    <h1 class="solde-value"><strong>{{  number_format($compte->entree, 0, ',', ' ') }} FCFA<strong></h1>
                </div>
            </div>
            <img src="data:image/png;base64,{{ $barcode }}" class="barcode" alt="Code barre">
        </main>
        <script>
            // Lancer l'impression
            imprimeEtat();
        </script>
    </body>
</html>
