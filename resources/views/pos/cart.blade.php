<div class="panel-body card-body">
    <div class="aiz-pos-cart-list c-scrollbar c-scrollbar-light"  style="overflow-x: scroll;">
        <table class="table table-bordered mb-0 mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="50%">{{translate('Product')}}</th>
                    <th width="15%">{{translate('QTY')}}</th>
                    <th>{{translate('Price')}}</th>
                    <th>{{translate('Discount')}}</th>
                    <th>{{translate('Discount Type')}}</th>
                    <th>{{translate('Subtotal')}}</th>
                    <th class="text-right">{{translate('Remove')}}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $tax = 0;
                    $shipping = 0;
                    if(Session::get('pos_shipping', 0) != 0 && Session::has('pos_shipping_info') && Session::get('pos_shipping_info')['city'] != null){
                        $city = \App\City::where('name', Session::get('pos_shipping_info')['city'])->first();
                        if($city != null){
                            $shipping = $city->cost;
                        }
                    }
                @endphp
                @if (Session::has('posCart'))
                    @forelse (Session::get('posCart') as $key => $cartItem)
                        @php
                            $subtotal += $cartItem['price']*$cartItem['quantity'];
                            $tax += $cartItem['tax']*$cartItem['quantity'];
                        @endphp
                        <tr>
                            <td>
                                <span class="media">
                                    <div class="media-left">
                                        <img class="mr-3" height="60" src="{{ uploaded_asset(\App\Product::find($cartItem['id'])->thumbnail_img) }}" >
                                    </div>
                                    <div class="media-body">
                                        {{ \App\Product::find($cartItem['id'])->name }}
                                    </div>
                                </span>
                            </td>
                            <td>
                                <div class="">
                                    <input type="number" class="form-control px-0 text-center" placeholder="1" id="qty-{{ $key }}" value="{{ $cartItem['quantity'] }}" onchange="updateQuantity({{ $key }})" min="1">
                                </div>
                            </td>
                            <td>{{ single_price($cartItem['price']) }}</td>
                            <td>
                                <div class="prod_discount_cont" style="width: 75px; display: flex; align-items: center;">
                                    <span>৳</span>
                                    <?php 
                                        /*$the_discount = 0;
                                        if($cartItem['discount_type'] == 'amount'){
                                            $the_discount = $cartItem['discount'];
                                        }elseif($cartItem['discount_type'] == 'percent' && $cartItem['discount_percent']){
                                            $the_discount = $cartItem['discount_percent'];
                                        }*/
                                        
                                     ?>
                                    <input type="text" class="form-control" value="{{ $cartItem['discount'] }}" name="discount" id="discount-{{ $key }}" onchange="updateDiscount({{ $key }})">
                                </div>
                            </td>
                            <td>
                                <?php //$pord_dis_type = \DB::table('products')->where('id', '=', $cartItem['product_id'])->select('discount_type')->get(); 
                                //echo $pord_dis_type[0]->discount_type;
                            ?>
                                {{-- {{ $pord_dis_type}} --}}
                                <select class="form-control" name="discount_type" id="discount_type-{{ $key }}" onchange="updateDiscountType({{ $key }})" style="width: 120px;">
                                    <option value="none" @if($cartItem['discount_type'] == 'none' || $cartItem['discount_type'] == ''): selected @endif >None</option>
                                    <option value="amount" @if($cartItem['discount_type'] == 'amount') selected @endif>Amount</option>
                                    <option value="percent" @if($cartItem['discount_type'] == 'percent') selected @endif>Percent</option>
                                </select>
                            </td>
                            <td>{{ single_price($cartItem['price']*$cartItem['quantity']) }}</td>
                            <td class="text-right">
                                <button type="button" class="btn btn-circle btn-icon btn-sm btn-danger" onclick="removeFromCart({{ $key }})"><i class="las la-trash-alt"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <i class="las la-frown la-3x opacity-50"></i>
                                <p>No Product Added</p>
                            </td>
                        </tr>
                    @endforelse
                @endif
            </tbody>
        </table>
    </div>
</div>
{{-- <div class="card-footer bord-top">
    <table class="table mar-no" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-center">{{translate('Sub Total')}}</th>
                <th class="text-center">{{translate('Total Tax')}}</th>
                <th class="text-center">{{translate('Total Shipping')}}</th>
                <th class="text-center">{{translate('Discount')}}</th>
                <th class="text-center">{{translate('Total')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ single_price($subtotal) }}</td>
                <td class="text-center">{{ single_price($tax) }}</td>
                <td class="text-center">{{ single_price(Session::get('pos_shipping', 0)) }}</td>
                <td class="text-center">{{ single_price(Session::get('pos_discount', 0)) }}</td>
                <td class="text-center">{{ single_price($subtotal+$tax+Session::get('pos_shipping', 0) - Session::get('pos_discount', 0)) }}</td>
            </tr>
        </tbody>
    </table>
</div> --}}
