<table class="table table-bordered aiz-table mb-0">
    <thead>
        <tr>
            {{-- <th>Selling Date</th> --}}
            <th width="30%">Product Name</th>
            {{-- <th>Unit Price</th> --}}
            <th>Quantity</th>
            <th>Total Sales pos</th>
            <th>Total Sales web</th>
            {{-- <th>Total Discount</th> --}}
            <th>Total </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            @if ($order['product_name'])
                <tr>
                    {{-- <td>{{ date('d M Y', strtotime($order['updated_at'])) }}</td> --}}
                    <td>{{ $order['product_name'] }}</td>
                    {{-- <td>{{ format_price($order['unit_price']) }}</td> --}}
                    <td>{{ $order['total_quantity'] }} ({{ $order['unit'] }}) </td>
                    <td>{{ format_price($order['pos']) }}</td>
                    <td>{{ format_price($order['web']) }}</td>
                    {{-- <td>{{ format_price($order['total_discount']) }}</td> --}}
                    <td>{{ format_price($order['total_price_sale']) }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
