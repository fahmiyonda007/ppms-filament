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
                <h1>CASH FLOW - {{ $reportData['projectName'] }}</h1>
                <div>{{"Periode : {$reportData['startDate']} - {$reportData['endDate']}"}}</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="headerdesc"><b>Trx Date</b></th>
                    <th class="headerdesc"><b>Trx Code</b></th>
                    <th class="headerdesc"><b>Description</b></th>
                    <th class="headerdesc"><b>Start Balance</b></th>
                    <th class="headerdesc"><b>Cash IN</b></th>
                    <th class="headerdesc"><b>Cash OUT</b></th>
                    <th class="headerdesc"><b>End Balance</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record as $item)
                    
                        <tr>
                            <td class="desc">{{ $item->transaction_date }}</td>
                            <td class="desc">{{ $item->transaction_code }}</td>
                            <td class="desc">{{ $item->description }}</td>
                            <td>{{ $item->start_balance }}</td>
                            <td>{{ $item->cash_in }}</td>
                            <td>{{ $item->cash_out }}</td>
                            <td>{{ $item->end_balance }}</td>
                            <td></td>
                        </tr>
                    
                      
                  
                @endforeach

            </tbody>
        </table>       
    </main>    
</body>

<!-- Mirrored from s3-eu-west-1.amazonaws.com/htmlpdfapi.production/free_html5_invoice_templates/example2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 May 2023 02:36:40 GMT -->

</html>
