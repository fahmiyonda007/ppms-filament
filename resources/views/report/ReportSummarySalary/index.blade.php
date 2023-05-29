<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">
    <title>Cash Flow Summary</title>

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
        <div id="details">
            <div id="invoice">
                <h1>SUMMARY GAJIAN TUKANG 54 DEVELOPMENT</h1>
                <div>{{ "Periode : {$reportData['startDate']} - {$reportData['endDate']}" }}</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="headerdesc"><b>Project</b></th>
                    <th style="width : 130px;" class="headerright"><b>Total Gajian</b></th>
                    <th style="width : 130px;" class="headerright"><b>Total Kasbon Tukang</b></th>
                    <th style="width : 130px;" class="headerright"><b>Total Kasbon Vendor</b></th>
                    <th style="width : 130px;" class="headerright"><b>Total</b></th>

                </tr>

            </thead>
            <tbody>
                @foreach ($record as $item)
                    @if ($item->name != 'TOTAL')
                        <tr>
                            <td class="desc">{{ $item->name }}</td>
                            <td>{{ $item->total_gajian }}</td>
                            <td>{{ $item->total_kasbon_tukang }}</td>
                            <td>{{ $item->total_kasbon_vendor }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                    @elseif ($item->name == 'TOTAL')
                        <tr class="total" style="background-color : #feb24c;">
                            <td class="desc">{{ $item->name }}</td>
                            <td>{{ $item->total_gajian }}</td>
                            <td>{{ $item->total_kasbon_tukang }}</td>
                            <td>{{ $item->total_kasbon_vendor }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </main>
</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
