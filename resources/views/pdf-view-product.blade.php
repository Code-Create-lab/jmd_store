<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 10px;
        text-align: center;
    }
</style>
<h1>{{ $productData['product']->name }}</h1>

<h2> <b> Product Added Date : </b> {{ \Carbon\Carbon::parse($productData['product']['date'])->format('M d, Y') ?? '' }}
</h2>

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
                {{-- @dd($productData) --}}

                @if ($key == 'product')
                    <td>{{ \Carbon\Carbon::parse($data['date'])->format('M d, Y') ?? '' }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $data['box'] }}</td>
                    <td>0</td>
                    <td>{{ $data['box'] }}</td>
                    @php
                        $balance = $data['box'];
                    @endphp
                @else
                    @foreach ($data as $key => $gatepassProduct)
            <tr>
                {{-- @dd($data) --}}
                <td></td>
                <td>{{ \Carbon\Carbon::parse($gatepassProduct->date)->format('M d, Y') }}</td>
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
