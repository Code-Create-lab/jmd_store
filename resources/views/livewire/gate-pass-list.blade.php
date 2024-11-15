<div class="container mt-5">
    <h2>Gate Pass List</h2>

    <div>
        <label for="dateFrom">Date From:</label>
        <input type="date" id="dateFrom" wire:change="filterGatepassData" wire:model="dateFrom">
    
        <label for="dateTo">Date To:</label>
        <input type="date" id="dateTo" wire:change="filterGatepassData" wire:model="dateTo">
    </div>
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Attached Product</th>
                <th>Added Date</th>
                <th>Total Boxes</th>
                <th>Box</th>
                <th>Slip No</th>
                <th>Total Amount</th>
                <th>Pass Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gatepass as $item)
                <tr>
                    <td>
                        @foreach ($item->product as $p)
                            <li>{{ $p->name }}</li>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($item->product as $p)
                            <li>{{ \Carbon\Carbon::parse($p->date)->diffForHumans() }}</li>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($item->product as $p)
                            <li>{{ $p->box }}</li>
                        @endforeach
                    </td>
                    <td>{{ $item->box }}</td>
                    <td>{{ $item->slip_no }}</td>
                    <td>{{ $item->total_amount }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}</td>
                </tr>
            @endforeach


            <!-- Add more rows as needed -->
        </tbody>
        <tfoot>
            <tr>
                <th>Summary</th>
                <th></th>
                <th></th>
                <th id="total-quantity">Total Box: {{ collect($gatepass)->sum('box') }}</th>
                <th>{{ $message }}</th>
                <th id="total-price">Total Amount: ₹{{ $totalAmount }} x {{ $gst }} % </th>
                <th> {{ $totalAmountGST }} </th>
                {{-- <th> <th> --}}
            </tr>

            {{-- <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td id="total-quantity">Total Box</td>
                <td></td>
                <td id="total-price">Total Amount</td>
                <td></td>
            </tr> --}}
        </tfoot>
    </table>
    <input type="text" wire:model='gst' wire:keydown="calculateAmount" placeholder="Enter Gst %">
</div>
