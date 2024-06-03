@php
	if(Session::has('currency_code')){
        $currency_code = Session::get('currency_code');
    }
    else{
        $currency_code = \App\Currency::findOrFail(get_setting('system_default_currency'))->code;
    }
    $language_code = Session::get('locale', Config::get('app.locale'));

    if(\App\Language::where('code', $language_code)->first()->rtl == 1){
        $direction = 'rtl';
        $text_align = 'right';
        $not_text_align = 'left';
    }else{
        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'right';
    }

    if($currency_code == 'BDT' || $language_code == 'bd'){
        $font_family = "'Hind Siliguri','sans-serif'";
    }elseif($currency_code == 'KHR' || $language_code == 'kh'){
        $font_family = "'Hanuman','sans-serif'";
    }elseif($currency_code == 'AMD'){
        $font_family = "'arnamu','sans-serif'";
    }elseif($currency_code == 'ILS'){
        $font_family = "'Varela Round','sans-serif'";
    }elseif($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa'){
        $font_family = "'XBRiyaz','sans-serif'";
    }else{
        $font_family = "'Roboto','sans-serif'";
    }
@endphp
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
	<style media="all">
		@php
            if(Session::has('currency_code')){
                $currency_code = Session::get('currency_code');
            }
            else{
                $currency_code = \App\Currency::findOrFail(get_setting('system_default_currency'))->code;
            }
        @endphp
		@if($currency_code == 'BDT')
			@font-face {
	            font-family: 'Hind Siliguri';
	            src: url("{{ static_asset('assets/fonts/HindSiliguri-Regular.ttf') }}") format("truetype");
	            font-weight: normal;
	            font-style: normal;
	        }
	        *{
	            margin: 0;
	            padding: 0;
	            line-height: 1;
	            font-family: 'Hind Siliguri';
	            color: #333542;
	        }
	    @elseif($currency_code == 'ILS')
			@font-face {
	            font-family: 'Cairo';
	            src: url("{{ static_asset('assets/fonts/Cairo-Regular.ttf') }}") format("truetype");
	            font-weight: normal;
	            font-style: normal;
	        }
	        *{
	            margin: 0;
	            padding: 0;
	            line-height: 1.1;
	            font-family: 'Cairo';
	            color: #333542;
	        }
        @else
        	@font-face {
	            font-family: 'Roboto';
	            src: url("{{ static_asset('assets/fonts/Roboto-Regular.ttf') }}") format("truetype");
	            font-weight: normal;
	            font-style: normal;
	        }
	        *{
	            margin: 0;
	            padding: 0;
	            line-height: 1.1;
	            font-family: 'Roboto';
	            color: #333542;
	        }
        @endif
		body{
			font-size: 0.488rem;
		}
		.gry-color *,
		.gry-color{
			color:#878f9c;
		}
		table{
			width: 100%;
		}
		table th{
			font-weight: normal;
		}
		table.padding th{
			padding: .20rem .5rem;
		}
		table.padding td{
			padding: .20rem .5rem;
		}
		table.sm-padding td{
			padding: .1rem .5rem;
		}
		.border-bottom td,
		.border-bottom th{
			border-bottom:1px solid #eceff4;
		}
		.text-left{
			text-align:left;
		}
		.text-right{
			text-align:right;
		}
        		* {
          box-sizing: border-box;
        }

        /* Create two equal columns that floats next to each other */
        .column {
          float: left;
          width: 100%;
          padding: 10px;
          height: 300px; /* Should be removed. Only for demonstration */
        }

        /* Clear floats after the columns */
        .row:after {
          content: "";
          display: table;
          clear: both;
        }
	</style>

</head>
<body>
    <div class= "row">
		<div class="column">

		@php
			$logo = get_setting('header_logo');
		@endphp

		<div style="border-bottom:1px solid #eceff4;padding: 1rem;">
			<table style="font-size: 12px">
				<tr>
					<td>
						@if($logo != null)
							<img src="{{ uploaded_asset($logo) }}" height="45" style="display:inline-block;">
						@else
							<img src="{{ static_asset('assets/img/logo.png') }}" height="45" style="display:inline-block;">
						@endif
					</td>
					<td style="font-size: 0.5rem;" class="text-right strong">{{  translate('INVOICE') }}</td>
				</tr>
			</table>
			<table style="font-size: 12px">
				<tr>
					<td style="font-size: 1rem;" class="strong">{{ get_setting('site_name') }}</td>
					<td class="text-right font-weight-bold">{{  translate('Life Time Order') }}: {{ count(optional($order->user)->orders ?? []) }} </td>
				</tr>
				<tr>
					<td class="gry-color small">{{ get_setting('contact_address') }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{  translate('Email') }}: {{ get_setting('contact_email') }}</td>
					<td class="text-right small"><span class="gry-color small">{{  translate('Order ID') }}:</span> <span class="strong">{{ $order->code }}</span></td>
				</tr>
				<tr>
					<td class="gry-color small">{{  translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
					<td class="text-right small"><span class="gry-color small">{{  translate('Order Date') }}:</span> <span class=" strong">{{ date('d-m-Y', $order->date) }}</span></td>

				</tr>
			</table>

		</div>

		<div style="padding: 0.5rem ;padding-bottom: 0">
            <table style="font-size: 12px">
				@php
					$shipping_address = json_decode($order->shipping_address);
				@endphp
				<tr><td class="strong small gry-color">{{ translate('Bill to') }}:</td></tr>
				<tr><td class="strong" style="font-size: 12px">{{ $shipping_address->name }}</td></tr>
				<tr><td class="gry-color small" style="font-size: 12px">{{ $shipping_address->address }}, {{ $shipping_address->city }}, {{ $shipping_address->country }}</td></tr>
				<tr><td class="gry-color small" style="font-size: 12px">{{ translate('Email') }}: {{ $shipping_address->email }}</td></tr>
				<tr><td class="gry-color small" style="font-size: 12px">{{ translate('Phone') }}: {{ $shipping_address->phone }}</td></tr>
			</table>
		</div>

	    <div style="padding: 0.1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
	                <tr class="gry-color" style="" style="font-size: 12px">
	                    <th width="35%" class="text-left" style="font-size: 12px">{{ translate('Product Name') }}</th>
						<th width="15%" class="text-left" style="font-size: 12px">{{ translate('Delivery Type') }}</th>
	                    <th width="10%" class="text-left" style="font-size: 12px">{{ translate('Qty') }}</th>
	                    <th width="15%" class="text-left"style="font-size: 12px">{{ translate('Unit Price') }}</th>
	                    <th width="15%" class="text-left">{{ translate(discount_col_name(0)) }}</th>
	                    <th width="10%" class="text-left" style="font-size: 12px">{{ translate('Tax') }}</th>
	                    <th width="15%" class="text-right" style="font-size: 12px">{{ translate('Total') }}</th>
	                </tr>
				</thead>
				<tbody class="strong">
	                @foreach ($order->orderDetails as $key => $orderDetail)
		                @if ($orderDetail->product != null)
							<tr class="" style="font-size: 12px">
								<td style="font-size: 12px">{{ $orderDetail->product->name }} @if($orderDetail->variation != null) ({{ $orderDetail->variation }}) @endif</td>
								<td style="font-size: 12px">
									@if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
										{{ translate('Home Delivery') }}
									@elseif ($orderDetail->shipping_type == 'pickup_point')
										@if ($orderDetail->pickup_point != null)
											{{ $orderDetail->pickup_point->getTranslation('name') }} ({{ translate('Pickip Point') }})
										@endif
									@endif
								</td >
								<td class="gry-color" style="font-size: 12px">{{ $orderDetail->quantity }}</td>
								<td class="gry-color currency">{{ single_price($orderDetail->price/$orderDetail->quantity) }}</td>
								<td class="currency">{{ single_price($orderDetail->discount) }}</td>
								<td class="gry-color currency" style="font-size: 12px">{{ single_price($orderDetail->tax/$orderDetail->quantity) }}</td>
			                    <td class="text-right currency" style="font-size: 12px">{{ single_price(( $orderDetail->price - $orderDetail->discount )+$orderDetail->tax) }}</td>
							</tr>
		                @endif
					@endforeach
	            </tbody>
			</table>
		</div>

	    <div style="padding:0 0.2rem;">
	        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
		        <tbody>
			        <tr style="font-size: 12px">
			            <th class="gry-color text-left" style="font-size: 12px">{{ translate('Sub Total') }}</th>
			            <td class="currency">{{ single_price($order->orderDetails->sum('price') - $order->total_discount) }}</td>
			        </tr>
			        <tr style="font-size: 12px">
			            <th class="gry-color text-left" style="font-size: 12px">{{ translate('Shipping Cost') }}</th>
			            <td class="currency">{{ single_price($delivery_charge->shipping_cost) }}</td>
			        </tr>
			        <tr class="border-bottom" style="font-size: 12px">
			            <th class="gry-color text-left" style="font-size: 12px">{{ translate('Total Tax') }}</th>
			            <td class="currency" style="font-size: 12px">{{ single_price($order->orderDetails->sum('tax')) }}</td>
			        </tr>
                    <tr class="border-bottom" style="font-size: 12px">
			            <th class="gry-color text-left" style="font-size: 12px">{{ translate('Coupon Discount') }}</th>
			            <td class="currency" style="font-size: 12px">{{ single_price($order->coupon_discount) }}</td>
			        </tr>
                    <tr class="border-bottom" style="font-size: 12px">
			            <th class="gry-color text-left" style="font-size: 13px">{{ translate('Advance Paid') }}</th>
			            <td class="currency" style="font-size: 12px">{{ single_price($order->advance_payment) }}</td>
			        </tr>
                    <tr class="border-bottom" style="font-size: 12px">
			            <th class="gry-color text-left" style="font-size: 13px">{{ translate('Previous Due') }}</th>
			            <td class="currency" style="font-size: 12px">{{ single_price($order->previous_due_payment) }}</td>
			        </tr>
			        <tr style="font-size: 12px">
			            <th class="text-left strong" style="font-size: 14px">{{ translate('Grand Total') }}</th>
			            <td class="currency">{{ single_price($order->grand_total - $order->total_discount - $order->advance_payment + $order->previous_due_payment)  }}</td>
			        </tr>
		        </tbody>
		    </table>
	    </div>

        <div style="padding: 0.1rem;">
			<table class="padding border-bottom">
				<thead>
	                <tr class="gry-color" style="" style="font-size: 12px">
	                    <th width="35%" class="text-left" style="font-size: 12px">{{ translate('Instructions') }}</th>
	                </tr>
				</thead>
				<tbody class="strong">

							<tr class="" style="font-size: 12px">
								<td style="font-size: 12px"> {!! $order->order_note !!}</td>
							</tr>

	            </tbody>
			</table>
		</div>






    </div>
 </div>

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
