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


<table>
    <thead>
        <tr>
            <th>DATE</th>
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
                    <td>{{ \Carbon\Carbon::parse($data['created_at'])->format('d/m/Y') ?? '' }}</td>
                    <td>{{ $data['box'] }}</td>
                    <td></td>
                    <td></td>
                    @php
                        $balance = $data['box'];
                    @endphp
                @else
                    @foreach ($data as $key => $gatepassProduct)
            <tr>
                {{-- @dd($data) --}}
                <td>{{ \Carbon\Carbon::parse($gatepassProduct['created_at'])->format('d/m/Y') }}</td>
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
