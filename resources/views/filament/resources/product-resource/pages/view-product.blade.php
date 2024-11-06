<x-filament-panels::page>

    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
            text-align: center;
        }

        .print_now {

            background-color: #145fb7;
            padding: 10px;
            border-radius: 6px;
            color: white
        }
    </style>
    <h1>{{ $record->name }}</h1>

    <h2> <b> Product Added Date : </b> {{ \Carbon\Carbon::parse($productData['product']['date'])->format('M d, Y') ?? '' }}</h2>

    <a href="{{ route('download_pdf', $record->id) }}">
        <button class="btn button print_now">Print Now</button>

    </a>

    <table>
        <thead>
            <tr>
                <th>In Warehouse</th>
                <th>In Slip</th>
                <th>SLIP NO</th>
                <th>RECEIVED</th>
                <th>ISSUE</th>
                <th>BALANCED</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productData as $key => $data)
                <tr>

                    {{-- @dd($data->get_single_gatePass) --}}
                    @if ($key == 'product')
                        <td>{{ \Carbon\Carbon::parse($data['date'])->format('M d, Y') ?? '' }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $record['box'] }}</td>
                        <td>0</td>
                        <td>{{ $record['box'] }}</td>
                        @php
                            $balance = $record['box'];
                        @endphp
                    @else
                        {{-- @dd($data, $key) --}}
                        @foreach ($data as $key => $gatepassProduct)
                <tr>
                    {{-- @dd() --}}
                    <td></td>
                    <td>{{ \Carbon\Carbon::parse($gatepassProduct->pivot->in_slip_date)->format('M d, Y') }}</td>
                    <td>{{ $gatepassProduct->slip_no }}</td>
                    <td></td>
                    <td>{{ $gatepassProduct->pivot->box }}</td>
                    @php
                        // $balance = 0;

                        $balance -= $gatepassProduct->pivot->box;

                    @endphp
                    <td>{{ $balance }}</td>
                </tr>
            @endforeach
            @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</x-filament-panels::page>
