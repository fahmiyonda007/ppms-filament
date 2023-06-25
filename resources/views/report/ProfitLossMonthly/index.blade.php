<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">
    <title>General Journal</title>

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
                <h1>PROFIT LOSS MONTHLY - {{ $reportData['yearPeriod'] }}</h1>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 150px; background-color : #feb24c;" class="headerdesc">
                        <b>Description</b>
                    </th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Jan</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Feb</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Mar</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Apr</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>May</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Jun</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Jul</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Aug</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Sep</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Oct</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Nov</b></th>
                    <th style="width: 70px; background-color : #feb24c;" class="headerright"><b>Dec</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                    @if ($item->rowcode == 'project')
                        <tr class="total">
                            <td style="background-color : #ffeda0;" class="desc">{{ $item->coa_desc }}
                            </td>
                            <td style="background-color : #ffeda0;">{{ $item->Jan }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Feb }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Mar }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Apr }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->May }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Jun }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Jul }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Aug }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Sep }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Oct }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Nov }}</td>
                            <td style="background-color : #ffeda0;">{{ $item->Dec }}</td>
                        </tr>
                    @elseif ($item->rowcode == 'total')
                        <tr class="total">
                            <td class="desc">{{ $item->coa_desc }}</td>
                            <td>{{ $item->Jan }}</td>
                            <td>{{ $item->Feb }}</td>
                            <td>{{ $item->Mar }}</td>
                            <td>{{ $item->Apr }}</td>
                            <td>{{ $item->May }}</td>
                            <td>{{ $item->Jun }}</td>
                            <td>{{ $item->Jul }}</td>
                            <td>{{ $item->Aug }}</td>
                            <td>{{ $item->Sep }}</td>
                            <td>{{ $item->Oct }}</td>
                            <td>{{ $item->Nov }}</td>
                            <td>{{ $item->Dec }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="desc">{{ $item->coa_desc }}</td>
                            <td>{{ $item->Jan }}</td>
                            <td>{{ $item->Feb }}</td>
                            <td>{{ $item->Mar }}</td>
                            <td>{{ $item->Apr }}</td>
                            <td>{{ $item->May }}</td>
                            <td>{{ $item->Jun }}</td>
                            <td>{{ $item->Jul }}</td>
                            <td>{{ $item->Aug }}</td>
                            <td>{{ $item->Sep }}</td>
                            <td>{{ $item->Oct }}</td>
                            <td>{{ $item->Nov }}</td>
                            <td>{{ $item->Dec }}</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </main>

</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
