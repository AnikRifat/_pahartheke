<table>
    <thead>
        <tr>
            <th>Sl. No</th>
            <th>Selling Date</th>
            <th width="30%">Product Name</th>
            <th>Unit Price</th>
            <th>Sales Quantity (Product unit like PCS, KG)</th>
            <th>Total Sales Amount</th>
        </tr>
    </thead>
    <tbody>
    	@php
    		$count = 1;
    	@endphp
        @foreach ($orders as $order)
            <tr>
                <td>{{ $count }}</td>
                <td>{{ date("d M Y", strtotime($order->created_at)) }}</td>
				<td>{{ $order->name }}</td>
                <td>{{ format_price($order->unit_price) }}</td>
                <td>
                    {{ $order->total_sale_quantity }}
                    @php
                        if(is_numeric($order->unit)){
                            echo "(Unit not set)";
                        }else{
                            echo $order->unit;
                        }
                    @endphp
                </td>
				<td>{{ format_price( $order->total_order_amount) }}</td>
            </tr>
            @php
            	$count++
            @endphp
        @endforeach
    </tbody>
</table>
