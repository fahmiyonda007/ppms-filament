<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">

    <title>Journal Voucher</title>

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
                <h1>JOURNAL VOUCHER</h1>
            </div>

        </div>
        <table>
            <tr>
                <td style="background-color : #fff; vertical-align: top;">
                    <table style="width : 350px;">
                        <tr style="width: 25px;">
                            <td class="desc" style="width : 150px; background-color : #fff;">Reference Code</td>
                            <td class="desc" style="width : 5px; background-color : #fff;">:</td>
                            <td class="desc" style="width : 100px; background-color : #fff;">
                                {{ $record[0]->reference_code }}
                            </td>
                        </tr>
                        <tr style="width: 25px;">
                            <td class="desc" style="width : 150px; background-color : #fff;">Journal Source</td>
                            <td class="desc" style="width : 5px; background-color : #fff;">:</td>
                            <td class="desc" style="width : 100px; background-color : #fff;">
                                {{ $record[0]->journal_souce }}
                            </td>
                        </tr>
                        <tr style="width: 25px;">
                            <td class="desc" style="width : 150px; background-color : #fff;">Transaction Date</td>
                            <td class="desc" style="width : 5px; background-color : #fff;">:</td>
                            <td class="desc" style="width : 100px; background-color : #fff;">
                                {{ $record[0]->transaction_date }}</td>
                        </tr>
                    </table>
                </td>
                <td style="background-color : #fff; vertical-align: top;">
                    <table style="width : 350px;">
                        <tr style="width: 25px;">
                            <td class="desc" style="width : 150px; background-color : #fff;">Project</td>
                            <td class="desc" style="width : 5px; background-color : #fff;">:</td>
                            <td class="desc" style="width : 100px; background-color : #fff;">
                                {{ $record[0]->project_name }}
                            </td>
                        </tr>
                        <tr style="width: 25px;">
                            <td class="desc" style="width : 150px; background-color : #fff;">Description</td>
                            <td class="desc" style="width : 5px; background-color : #fff;">:</td>
                            <td class="desc" style="width : 100px; background-color : #fff;">
                                {{ $record[0]->jurnal_description }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width : 100px; background-color : #feb24c;" class="headercenter"><b>Account Code</b></th>
                    <th style="width : 150px; background-color : #feb24c;" class="headerdesc"><b>Account Name</b></th>
                    <th style="width : 150px; background-color : #feb24c;" class="headerright"><b>Debet</th>
                    <th style="width : 150px; background-color : #feb24c;" class="headerright"><b>Credit</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                    @if ($item->reference_code != 'Total')
                        <tr>
                            <td class="center">{{ $item->account_code }}</td>
                            <td class="desc">{{ $item->account_name }}</td>
                            <td>{{ $item->debet_amount }}</td>
                            <td>{{ $item->credit_amount }}</td>
                        </tr>
                    @else
                        <tr class="total">
                            <td colspan="2" class="center" style="width : 100px; background-color : #feb24c;">
                                {{ $item->reference_code }}</td>
                            <td style="background-color : #feb24c;">{{ $item->debet_amount }}</td>
                            <td style="background-color : #feb24c;">{{ $item->credit_amount }}</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </main>

</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
