<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">
    <title>Cash Flow Detail</title>

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
                <h1>CASH FLOW DETAIL</h1>
                <div>{{ "Periode : {$reportData['startDate']} - {$reportData['endDate']}" }}</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="headerdesc"><b>Description</b></th>
                    @foreach ($reportData['dateArray'] as $item)
                        <th class="headerright"><b>{{ $item }}</b></th>
                    @endforeach

                </tr>

            </thead>
            <tbody>

                @foreach ($record as $key => $item)
                    <tr>
                        @if ($key == 'DATA KAS' || $key == 'TOTAL PENGELUARAN')
                        <td colspan="10" style="background-color : #fc8d59;" class="center">{{ $key }}</td>
                        @else
                        <td colspan="10" style="background-color : #fee090;" class="center">{{ $key }}</td>
                        @endif
                    </tr>

                    @for ($i = 0; $i < $item->count(); $i++)
                        @if ($item[$i]->coa_name == 'TOTAL INFLOWS' || $item[$i]->coa_name == 'TOTAL OUTFLOWS')
                        <tr class="total">
                            <td class="desc"> {{ $item[$i]->coa_name }}</td>
                            @foreach ($reportData['dateArray'] as $detail)
                                <td> {{ $item[$i]->$detail }}</td>
                            @endforeach

                        </tr>
                        @else
                        <tr>
                            <td class="desc"> {{ $item[$i]->coa_name }}</td>
                            @foreach ($reportData['dateArray'] as $detail)
                                <td> {{ $item[$i]->$detail }}</td>
                            @endforeach

                        </tr>
                        @endif
                    @endfor

                @endforeach

            </tbody>
        </table>
    </main>
</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
