<html>
<head>
    <title>Print Invoice</title>
    <style type="text/css" media='all'>
        #invoice-POS {
        padding: 2mm;
        margin: 0 auto;
        width: 100%;
        background: #FFF;
        }
        #invoice-POS ::selection {
        background: #f31544;
        color: #FFF;
        }
        #invoice-POS ::moz-selection {
        background: #f31544;
        color: #FFF;
        }
        #invoice-POS h1 {
        font-size: 1.5em;
        color: #222;
        }
        #invoice-POS h2 {
        font-size: 0.9em;
        }
        #invoice-POS h3 {
        font-size: 1.2em;
        font-weight: 300;
        line-height: 2em;
        }
        #invoice-POS p {
        font-size: 0.7em;
        color: #666;
        line-height: 1.2em;
        }
        #invoice-POS #top, #invoice-POS #mid, #invoice-POS #bot {
        /* Targets all id with 'col-' */
        border-bottom: 1px solid #EEE;
        }
        #invoice-POS #mid {
        min-height: 80px;
        }
        #invoice-POS #bot {
        min-height: 50px;
        }
        #invoice-POS #top .logo img{
            max-height: 60px;
            height:auto;
            max-width:100%;
        }
        #invoice-POS .info {
        display: block;
        margin-left: 0;
        }
        #invoice-POS .info h6{
            margin:0
        }
        #invoice-POS .title {
        float: right;
        }
        #invoice-POS .title p {
        text-align: right;
        }
        #invoice-POS table {
        width: 100%;
        border-collapse: collapse;
        }
        #invoice-POS .tabletitle {
        font-size: 0.5em;
        background: #EEE;
        }
        #invoice-POS .service {
        border-bottom: 1px solid #EEE;
        }
        #invoice-POS .item {
        width: 24mm;
        }
        #invoice-POS .itemtext {
        font-size: 0.5em;
        }
        #invoice-POS #legalcopy {
        margin-top: 5mm;
        }

    </style>
</head>
<body>

    <div id="invoice-POS">
    
        <center id="top">
        <div class="logo">
            <img src="{{ uploaded_asset(get_setting('header_logo')) }}" alt="">
        </div>
        <div class="info"> 
            <h2>{{ get_setting('site_name') }}</h2>
            <small>Order Id: {{ $order->code }}</small>
        </div><!--End Info-->
        </center><!--End InvoiceTop-->
        
        <div id="mid">
        <div class="info">
            <h6>Contact Info</h6>
            <p> 
                Address : {{ get_setting('contact_address') }}</br>
                Email   : {{ get_setting('contact_email') }}</br>
                Phone   : {{ get_setting('contact_phone') }}</br>
            </p>
        </div>
        </div><!--End Invoice Mid-->
        
        <div id="bot">

            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="item"><h2>Item</h2></td>
                        <td class="Hours"><h2>Qty</h2></td>
                        <td class="Rate"><h2>Sub Total</h2></td>
                    </tr>
                    @foreach ($order->orderDetails as $key => $orderDetail)
                    <tr class="service">
                        <td class="tableitem">
                            <p class="itemtext">{{ $orderDetail->product->name }} @if($orderDetail->variation != null) ({{ $orderDetail->variation }}) @endif</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{ $orderDetail->quantity }}</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{ single_price(( $orderDetail->price - $orderDetail->discount )+$orderDetail->tax) }}</p>
                        </td>
                    </tr>
                    @endforeach


                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>tax</h2></td>
                        <td class="payment">
                            <h2>{{ single_price($order->orderDetails->sum('tax')) }}</h2>
                        </td>
                    </tr>

                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>Total</h2></td>
                        <td class="payment">
                            <h2>{{ single_price($order->grand_total - $order->total_discount) }}</h2>
                        </td>
                    </tr>

                </table>
            </div><!--End Table-->

            <div id="legalcopy">
                <p class="legal">
                    <strong>Thank you for your business!</strong>
                </p>
            </div>

        </div><!--End InvoiceBot-->
    </div><!--End Invoice-->

    <script type="text/javascript">
        try { this.print(); } catch (e) { window.onload = window.print; }
        window.onbeforeprint = function() {
            setTimeout(function(){
                window.close();
            }, 1500);
        }
    </script>
</body>
</html>
