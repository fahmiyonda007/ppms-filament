<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">
    <title>Vendor Liabilities</title>

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
                <h1>VENDOR LIABILITIES STATUS</h1>
                <div>{{"Periode : {$reportData['startDate']} - {$reportData['endDate']}"}}</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                <th class="headerdesc"><b>Vendor</b></th>
                <th style="width : 80px;" class="headercenter"><b>Trx. Date</b></th>
                <th style="width : 150px;" class="headerdesc"><b>SOW</b></th>
                <th style="width : 80px;" class="headercenter"><b>Start Date</b></th>
                <th style="width : 80px;" class="headercenter"><b>Est. End Date</b></th>
                <th style="width : 80px;" class="headercenter"><b>End Date</b></th>
                <th style="width : 80px;" class="headerright"><b>Est. Price</b></th>
                <th style="width : 80px;" class="headerright"><b>Deal Price</b></th>
                <th style="width : 80px;" class="headerright"><b>Total Payment</b></th>
                <th style="width : 80px;" class="headerright"><b>Outstanding</b></th>
                <th style="width : 80px;" class="headercenter"><b>Project Status</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                    <tr>
                        <td class="desc">{{ $item->vendor_name }}</td>
                        <td class="center">{{ $item->transaction_date }}</td>
                        <td class="desc">{{ $item->SOW }}</td>
                        <td class="center">{{ $item->start_date }}</td>
                        <td class="center">{{ $item->est_end_date }}</td>
                        <td class="center">{{ $item->end_date }}</td>
                        <td >{{ $item->est_price }}</td>
                        <td >{{ $item->deal_price }}</td>
                        <td >{{ $item->total_payment }}</td>
                        <td >{{ $item->outstanding }}</td>
                        <td class="center">{{ $item->project_status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    </main>
</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
