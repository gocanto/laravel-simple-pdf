<html lang="en">

<head>
    <title>{{ $templateTitle ?? '' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>

        @page  {
            margin: 0.6cm;
            font-size: 14px;
        }

        @font-face {
            font-family: 'Roboto';
            font-style: normal;
            font-weight: bold;
            src: url('https://fonts.googleapis.com/css?family=Roboto&display=swap') format('truetype');
        }

        * {
            float:none !important;
        }

        .footer {
            position: fixed;
            bottom: 100px;
        }

        div {
            page-break-after: unset !important;
            page-break-before: unset !important;
        }

        div.voucher-page + div.voucher-page {
            page-break-before: always !important;
        }

        td, th, table {
            float:none !important;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .divider {
            float:none !important;
            width: 100% !important;
        }

        .bold {
            font-weight: bold !important;
        }

        .mobileView {
            display: none;
        }

        .semiBold {
            font-weight: bold !important;
        }

        .marginBottom {
            margin-bottom: 12px;
        }

        .row {
            display: block !important;
        }

        .row > div {
            display: block !important;
        }
    </style>

    <style media="print">
        body p {
            margin: 0;
        }

        .p0{
            padding: 0;
        }

        .col-xs-12{
            width: 100%;
        }

        .col-xs-4{
            width: 33.333333%;
        }

        .col-xs-6{
            width: 50%;
        }
    </style>
</head>

<body class="{{ $templateName ?? '' }}">
    {!! $pdfContent !!}
</body>

</html>
