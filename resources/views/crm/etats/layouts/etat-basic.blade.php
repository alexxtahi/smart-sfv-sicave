<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        @if (isset($title) && $title != null)
            <title>{{ $title }}</title>
        @endif
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
            .etat-logo {
                object-fit: contain;
            }
            .container-table {
                margin-left: auto;
                margin-right: auto;
                width: 90% !important;
            }
            .title {
                margin-bottom: 30px;
                text-transform: uppercase;
            }
            footer {
                display: flex;
                justify-content: space-between;
                page-break-after: always;
                /*page-break-after: auto;*/
            }
            @page{
                margin: 10mm;
            }
            .fixed-footer{
                position: fixed;
                bottom: 20px;
                width : 90%;
                height: auto;
            }
            .middle-footer {
                position: fixed;
                bottom: 20px;
                width : 90%;
                margin-left: auto !important;
                margin-right: auto !important;
                text-align: center;
            }
            .fixed-footer-left {
                width: 90%;
                text-align: left;
            }
            .page-number:before {
            }
            header {
                margin-left: 5%;$
            }
            .under-table div {
                display: flex;
                justify-content: space-between;
            }
            .under-table div h3 {
                margin-top: 5px;
            }
            .montant-total h3 {
                font-weight: 900 !important;
            }
            .title-container {
                margin-bottom: 5mm;
            }
            .custom-table, .custom-table td, .custom-table th {
                border-color: black !important;
            }
            .custom-table {
                max-height: 10mm !important;
                font-size: 20px;
            }
            .custom-table thead tr {
                margin-top: 50mm !important;
            }.custom-table tbody {
                max-height: 10mm !important;
            }
            .qte {
                width: min-content !important;
                min-width: min-content !important;
                max-width: min-content !important;
            }
            .facture-title {
                display: flex;
                justify-content: space-between;
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
    @yield('body')
</html>
