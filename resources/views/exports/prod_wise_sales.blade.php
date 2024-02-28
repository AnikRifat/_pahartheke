<table class="table table-bordered aiz-table mb-0">
    <thead>
        <tr>
            <th>Selling Date</th>
            <th width="30%">Product Name</th>
            <th>Unit Price</th>
            {{-- <th>In House Order Quantity <br /> (Product unit like PCS, KG)</th> --}}
            {{-- <th>Website Order Quantity <br /> (Product unit like PCS, KG)</th> --}}
            <th>Total Sales Quantity <br /> (Product unit like PCS, KG)</th>
            <th>Discount</th>
            <th>Total Sales Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ date('d M Y', strtotime($order->updated_at)) }}</td>
                <td> <span>{{ $order->product?$order->product->name : ' '  }}</span> </td>
                <td>{{ format_price($order->price?$order->price : 0 ) }}</td>
                {{-- <td>
                    {{ $order->total_pos_quantity }}
                    @if (is_numeric($order->unit))
                        (Unit not set)
                    @else
                        ({{ $order->unit }})
                    @endif
                </td>
                <td>
                    {{ $order->total_web_quantity }}
                    @if (is_numeric($order->unit))
                        (Unit not set)
                    @else
                        ({{ $order->unit }})
                    @endif
                </td> --}}
                <td>
                    {{ $order->quantity }}
                    @if (is_numeric($order->unit))
                        (Unit not set)
                    @else
                    ({{ $order->product?$order->product->unit : ' '  }})
                    @endif
                </td>
                <td>
                    @if ($order->discount_type == 'percent')
                        {{ format_price(($order->price / 100) * $order->discount) }}
                    @else
                        {{ format_price($order->discount) }}
                    @endif
                </td>

                <td>{{ format_price($order->price * $order->quantity) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
