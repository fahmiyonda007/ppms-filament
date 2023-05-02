<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $fileName }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body style="font-size: 10px;">
    <h1>{{ $record[0]->code }} - {{ $record[0]->name }}</h1>
    <h2>Journal Details</h2>
    <table class="table table-bordered border-primary">
        <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>Journal</th>
                <th colspan='3'>COA</th>
                <th>Debet</th>
                <th>Credit</th>
            </tr>
        </thead>
        <tbody>
            {{-- @php dd($record->groupBy('jurnal_code')) @endphp --}}
            @foreach ($record as $key => $item)
                <tr>
                    {{-- @php dd($items) @endphp --}}
                    <td>{{ $key + 1 }}</td>
                    {{-- @foreach ($items as $item) --}}
                    <td>
                        {{ $item->jurnal_code }}
                    </td>
                    <td colspan="5">
                        {{ $item->level_first_name }}
                    </td>
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="5">
                        {{ $item->level_second_name }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="3">
                        {{ $item->level_thirds_name }}
                    </td>
                    <td class="text-right">{{ number_format($item->debet_amount, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->credit_amount, 0, ',', '.') }}</td>
                </tr>

                {{-- @php $total = $total + $item->unit_price; @endphp --}}
                {{-- @endforeach --}}
                </tr>
            @endforeach
            <tr class="text-center">
                <td colspan="5"><b>TOTAL</b></td>
                <td><b>{{ number_format($record->sum('debet_amount'), 0, ',', '.') }}</b></td>
                <td><b>{{ number_format($record->sum('credit_amount'), 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>




    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
</body>

</html>
