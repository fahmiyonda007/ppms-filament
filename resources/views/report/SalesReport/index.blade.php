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
                <h1>SALES REPORT</h1>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="background-color : #feb24c;" class="headercenter"><b>No</b></th>
                    <th style="background-color : #feb24c;" class="headerdesc"><b>Project</b></th>
                    <th style="background-color : #feb24c;" class="headerdesc"><b>Kavling</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Deal Price</b></th>
                    <th style="background-color : #feb24c;" class="headercenter"><b>Payment Type</b></th>
                    <th style="background-color : #feb24c;" class="headercenter"><b>Booking Date</b></th>
                    <th style="background-color : #feb24c;" class="headercenter"><b>Payment Date</th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Notary Fee</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Tax</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Commision</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Other Commision</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Added Bonus</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Net Price</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Payment Total</b></th>
                    <th style="background-color : #feb24c;" class="headerright"><b>Outstanding</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                    @if ($item->rowcode == '1')
                        <tr>
                            <td class="center">{{ $item->row_num }}</td>
                            <td class="desc">{{ $item->project }}</td>
                            <td class="desc">{{ $item->unit_kavling }}</td>
                            <td >{{ $item->deal_price }}</td>
                            <td class="center">{{ $item->payment_type }}</td>
                            <td class="center">{{ $item->booking_date }}</td>
                            <td class="center">{{ $item->payment_date }}</td>
                            <td>{{ $item->notary_fee }}</td>
                            <td>{{ $item->tax }}</td>
                            <td>{{ $item->commission }}</td>
                            <td>{{ $item->other_commission }}</td>
                            <td>{{ $item->added_bonus }}</td>
                            <td>{{ $item->net_price }}</td>
                            <td>{{ $item->payment_total }}</td>
                            <td>{{ $item->outstanding }}</td>
                        </tr>
                    @else
                        <tr  class="total">
                            <td colspan="3" class="center">TOTAL</td>
                            <td >{{ $item->deal_price }}</td>
                            <td class="center">{{ $item->payment_type }}</td>
                            <td class="center">{{ $item->booking_date }}</td>
                            <td class="center">{{ $item->payment_date }}</td>
                            <td>{{ $item->notary_fee }}</td>
                            <td>{{ $item->tax }}</td>
                            <td>{{ $item->commission }}</td>
                            <td>{{ $item->other_commission }}</td>
                            <td>{{ $item->added_bonus }}</td>
                            <td>{{ $item->net_price }}</td>
                            <td>{{ $item->payment_total }}</td>
                            <td>{{ $item->outstanding }}</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </main>

</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
