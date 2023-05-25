<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:38 GMT -->

<head>
    <meta charset="utf-8">
    <title>Cash Flow</title>

    {{-- <link href="css/report.css" rel="stylesheet"> --}}
    @include('report/style')
    @livewireStyles
    {{-- <style>
        
    </style> --}}
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <img src="logo.png">
        </div>
        <div id="company">
            <h2 class="name">54 Property</h2>
            <div>Jln. Tomang Raya Kav. 21-23</div>
            <div>(021) 519-0450</div>
        </div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="invoice">
                <h1>DAILY COST REPORT</h1>
                <div>{{"Periode : {$reportData['periodDate']}"}}</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width : 20px;" class="headercenter"><b>No</b></th>
                    <th style="width : 150px;" class="headerdesc"><b>Item</b></th>
                    <th style="width : 70px;" class="headerdesc"><b>Order Date</th>
                    <th style="width : 70px;" class="headerdesc"><b>Payment Date</b></th>
                    <th style="width : 150px;" class="headerdesc"><b>Vendor</b></th>
                    <th style="width : 150px;" class="headerdesc"><b>Project</b></th>
                    <th style="width : 60px;" class="headercenter"><b>Uom</b></th>
                    <th style="width : 60px;" class="headerright"><b>Qty</b></th>
                    <th style="width : 100px;" class="headerright"><b>Unit Price</b></th>
                    <th style="width : 100px;" class="headerright"><b>Total Price</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                     @if ($item->rowcode == 'detail')
                        <tr>
                            <td class="center">{{ $item->inc }}</td>
                            <td class="desc">{{ $item->item }}</td>
                            <td class="desc">{{ $item->order_date }}</td>
                            <td class="desc">{{ $item->payment_date }}</td>
                            <td class="desc">{{ $item->vendor }}</td>
                            <td class="desc">{{ $item->project }}</td>
                            <td class="center">{{ $item->uom }}</td>
                            <td >{{ $item->qty }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>{{ $item->total_price }}</td>
                        </tr>
                    @elseif ($item->rowcode == 'total')
                        <tr  class="total">
                            <td colspan="9" class="desc">{{ $item->item }}</td>
                            <td>{{ $item->total_price }}</td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>       
    </main>    
</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
