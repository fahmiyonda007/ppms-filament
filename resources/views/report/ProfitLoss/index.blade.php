<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">
    <title>Profit & Loss</title>

    {{-- <link href="css/report.css" rel="stylesheet"> --}}
    @include('report/style')
    @livewireStyles
    {{-- <style>
        @font-face {
            font-family: SourceSansPro;
            src: url(SourceSansPro-Regular.ttf);
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-family: SourceSansPro;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
            color: #0087C3;
        }

        #invoice {
            float: center;
            text-align: center;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 1.5em;
            line-height: 0em;
            font-weight: normal;
            margin: 0 0 15px 0;
            font-weight: bold;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            text-align: center;

        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table .headerdesc {
            text-align: center;
        }

        table td {
            padding: 3px 20px;

            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table tr.total {
            text-align: left;
            border-bottom: 1px solid #5D6975;
            border-top: 1px solid #5D6975;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
    </style> --}}
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <img src="logo.jpeg">
        </div>
        <div id="company">
            <h2 class="name">54 Property</h2>
            <div>Jln. Pepaya Raya No 7 Rt. 3 Rw. 5</div>
            <div>Kel. Jagakarsa, Kec. Jagakarsa</div>
            <div>Jakarta Selatan, DKI Jakarta 12620</div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="invoice">
                <h1>PROFIT AND LOSS - {{ $reportData['projectName'] }}</h1>
                <div>{{"Periode : {$reportData['startDate']} - {$reportData['endDate']}"}}</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th colspan="3" class="headerdesc"><b>DESCRIPTION</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                    @if ($item->rowcode == 'detail')
                        <tr>
                            <td class="desc">{{ $item->description }}</td>
                            <td>{{ $item->amount }}</td>
                            <td></td>
                        </tr>
                    @elseif ($item->rowcode == 'total')
                        <tr class="total">
                            <td class="desc" style="padding-left: 40px;background-color : #feb24c;"><b>&emsp;&emsp;{{ $item->description }}</td>
                            <td style="background-color : #feb24c;"></td>
                            <td style="background-color : #feb24c;">{{ $item->total }}</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>


        <!-- <div id="notices">
            <div>Note:</div>
            <div class="notice">Mau dikasih kata2 gak?</div>
        </div> -->
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal.
    </footer>
</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
