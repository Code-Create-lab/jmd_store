<!-- resources/views/filament/tables/custom-footer.blade.php -->

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
<table class="filament-tables-table w-full">
    <thead>
        <tr>
            <!-- Define your table headers -->
            <th class="text-left">Column 1</th>
            <th class="text-left">Column 2</th>
        </tr>
    </thead>
    <tbody>
        <!-- Loop through your records -->
        @foreach ($this->getTableRecords() as $record)
            <tr>
                <td>{{ $record->column1 }}</td>
                <td>{{ $record->column2 }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total Records:</td>
            <td>Total Amount:</td>
        </tr>
        <tr>
            <td colspan="2">
                <x-filament::button wire:click="performSummaryAction" color="primary">
                    Perform Summary Action
                </x-filament::button>
            </td>
        </tr>
    </tfoot>
</table>
