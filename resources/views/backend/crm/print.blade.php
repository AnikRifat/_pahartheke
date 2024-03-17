<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            /* Wrap long words */
        }

        th {
            background-color: #f2f2f2;
        }

        .customer-info {
            margin-top: 30px;
        }

        .customer-info h2 {
            margin-bottom: 10px;
        }

        /* Additional styles */
        .blank-field {
            height: 20px;
            /* Adjust height as needed */
        }
    </style>
</head>

<body>

    @if (!empty($summery['customer_summary']))
        <table>
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>Name</th>
                    <th>city</th>
                    <th>phone</th>
                    <th>Total Orders</th>
                    <th>Total Ammount</th>
                    <th>---</th>
                    <th>---</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $count = 1;
                @endphp
                @foreach ($summery['customer_summary'] as $customer)
                    <tr>
                        <td>{{ $count++ }}</td>

                        @foreach ($customer['address'] as $key => $item)
                            @if (($item && $key == 'name') || $key == 'city' || $key == 'phone')
                                <td>{{ $item }}</td>
                            @endif
                        @endforeach
                        <td><b>{{ $customer['total_orders'] }}</b>
                            <mark>{{ json_encode($customer['order_ids']) }}</mark>
                        </td>
                        <td>{{ $customer['total_purchase_amount'] }}</td>
                        <td class="blank-field"></td>
                        <td class="blank-field"></td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold">
                    <td>--</td>
                    <td></td>
                    <td></td>
                    <td>Summary</td>
                    <td>{{ $summery['orders_summery']['total_orders'] }}</td>
                    <td>{{ $summery['orders_summery']['total_sale'] }}</td>
                    <td class="blank-field"></td>
                    <td></td>



                </tr>
                <!-- Add more blank rows if needed -->
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold">
                    <td>-</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        <p>No data available.</p>
    @endif

</body>

</html>
