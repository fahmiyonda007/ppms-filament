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
                <h1>PROFIT AND LOSS </h1>
                <div>{{"Periode : {$reportData['startDate']} - {$reportData['endDate']}"}}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width : 100px;" class="headerdesc"><b>Account Code</b></th>
                    <th style="width : 150px;" class="headerdesc"><b>Account Name</b></th>
                    <th style="width : 150px;" class="headerright"><b>Debet</th>
                    <th style="width : 150px;" class="headerright"><b>Credit</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)

                        <tr>
                            <td class="desc">{{ $item->account_code }}</td>
                            <td class="desc">{{ $item->account_name }}</td>
                            <td>{{ $item->debet_amount }}</td>
                            <td>{{ $item->credit_amount }}</td>
                        </tr>


                @endforeach

            </tbody>
        </table>
    </main>

</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
