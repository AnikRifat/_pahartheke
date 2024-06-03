<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\OtpConfiguration;
use App\BusinessSetting;
use App\OrderDetail;
use App\ProductStock;
use App\Product;
use App\Order;
use App\Color;
use App\User;
use App\Customer;
use App\Address;
use Session;
use Auth;
use DB;
use PDF;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Http\Resources\PosProductCollection;
use App\Utility\CategoryUtility;

class PosController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('pos.index');
        } else {
            $pos_activation = BusinessSetting::where('type', 'pos_activation_for_seller')->first();
            if ($pos_activation != null && $pos_activation->value == 1) {
                return view('pos.frontend.seller.pos.index');
            } else {
                flash(translate('POS is disable for Sellers!!!'))->error();
                return back();
            }
        }
    }

    public function search(Request $request)
    {
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $products = Product::where('added_by', 'admin')->where('published', '1');
        } else {
            $products = Product::where('user_id', Auth::user()->id)->where('published', '1');
        }

        if ($request->category != null) {
            $arr = explode('-', $request->category);
            if ($arr[0] == 'category') {
                $category_ids = CategoryUtility::children_ids($arr[1]);
                $category_ids[] = $arr[1];
                $products = $products->whereIn('category_id', $category_ids);
            }
        }

        if ($request->brand != null) {
            $products = $products->where('brand_id', $request->brand);
        }

        if ($request->keyword != null) {
            $products = $products->where('name', 'like', '%' . $request->keyword . '%')->orWhere('barcode', $request->keyword)->orderBy('created_at', 'desc');
        }

        $stocks = new PosProductCollection($products->paginate(16));
        $stocks->appends(['keyword' => $request->keyword]);
        return $stocks;
    }

    public function getVarinats(Request $request)
    {
        $product = Product::find($request->id);
        $stocks = $product->stocks;
        if ($product->variant_product) {
            return view('pos.variants', compact('stocks'));
        } elseif ($product->current_stock > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->product_id);

        $data = array();
        $data['id'] = $product->id;
        $tax = 0;
        $data['variant'] = $request->variant;

        if ($request->variant != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $request->variant)->first();
            $price = $product_stock->price;
            $purchase_price = $product_stock->purchase_price;
            $quantity = $product_stock->qty;

            if ($request['quantity'] > $quantity) {
                return 0;
            }
        } else {
            $price = $product->unit_price;
            $purchase_price = $product->purchase_price;
        }

        //discount calculation based on flash deal and regular discount
        //calculation of taxes
        $flash_deals = \App\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $tax = ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $tax = $product->tax;
        }

        $data['purchase_price'] = $purchase_price;
        $data['quantity'] = $request->quantity;
        $data['price'] = $price;
        $data['discount'] = $request->discount;
        $data['discount_type'] = $request->discount_type;
        $data['tax'] = $tax;
        $data['shipping'] = $product->shipping_cost;

        if ($request->session()->has('posCart')) {
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('posCart') as $key => $cartItem) {
                if ($cartItem['id'] == $request->product_id) {
                    if ($cartItem['variant'] == $request->variant) {
                        $foundInCart = true;
                        $product = \App\Product::find($cartItem['id']);
                        if ($cartItem['variant'] != null && $product->variant_product) {
                            $product_stock = $product->stocks->where('variant', $cartItem['variant'])->first();
                            $quantity = $product_stock->qty;
                            if ($quantity >= $request->quantity) {
                                if ($request->quantity >= $product->min_qty) {
                                    $cartItem['quantity'] = $request->quantity;
                                }
                            }
                        } elseif ($product->current_stock >= $request->quantity) {
                            if ($request->quantity >= $product->min_qty) {
                                $cartItem['quantity'] = $request->quantity;
                            }
                        }
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }

            $request->session()->put('posCart', $cart);
        } else {
            $cart = collect([$data]);
            $request->session()->put('posCart', $cart);
        }

        return view('pos.cart');
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('posCart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $product = \App\Product::find($object['id']);
                if ($object['variant'] != null && $product->variant_product) {
                    $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->qty;
                    if ($quantity >= $request->quantity) {
                        $object['quantity'] = $request->quantity;
                    }
                } elseif ($product->current_stock >= $request->quantity) {
                    $object['quantity'] = $request->quantity;
                }
            }
            return $object;
        });
        $request->session()->put('posCart', $cart);

        return view('pos.cart');
    }
    //updated the discount for a cart item
    public function updateDiscount(Request $request)
    {
        $cart = $request->session()->get('posCart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['discount'] = $request->discount ? $request->discount : 0;
            }
            return $object;
        });
        $request->session()->put('posCart', $cart);
        return view('pos.cart');
    }
    //updated the discount type for a cart item
    public function updateDiscountType(Request $request)
    {
        $cart = $request->session()->get('posCart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['discount_type'] = $request->discount_type ? $request->discount_type : 0;
            }
            return $object;
        });
        $request->session()->put('posCart', $cart);
        return view('pos.cart');
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if (Session::has('posCart')) {
            $cart = Session::get('posCart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('posCart', $cart);
        }

        return view('pos.cart');
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request)
    {
        $user_id = $request->id;
        if ($user_id == '') {
            return view('pos.guest_shipping_address');
        } else {
            return view('pos.shipping_address', compact('user_id'));
        }
    }

    //Shipping Address for seller
    public function getShippingAddressForSeller(Request $request)
    {
        $user_id = $request->id;
        if ($user_id == '') {
            return view('pos.frontend.seller.pos.guest_shipping_address');
        } else {
            return view('pos.frontend.seller.pos.shipping_address', compact('user_id'));
        }
    }

    //set Discount
    public function setDiscount(Request $request)
    {
        if ($request->discount >= 0) {
            $request->session()->put('pos_discount', $request->discount);
        }
        return view('pos.cart');
    }
    public function setAdvance(Request $request)
    {
        if ($request->advance >= 0) {
            $request->session()->put('pos_advance', $request->advance);
        }
        return view('pos.cart');
    }
    public function setPreviousDue(Request $request)
    {
        if ($request->previous_due >= 0) {
            $request->session()->put('pos_previous_due', $request->previous_due);
        }
        return view('pos.cart');
    }
    //set Shipping Cost
    public function setShipping(Request $request)
    {
        if ($request->shipping != null) {
            $request->session()->put('pos_shipping', $request->shipping);
        }
        return view('pos.cart');
    }
    public function get_order_summary(Request $request)
    {
        return view('pos.order_summary');
    }
    public function set_shipping_address(Request $request)
    {
        if ($request->address_id != null) {
            $address = Address::findOrFail($request->address_id);
            $data['name'] = $address->user->name;
            $data['email'] = $address->user->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
        }

        $shipping_info = $data;
        $request->session()->put('pos_shipping_info', $shipping_info);
        return view('pos.cart');
    }

    //order place
    public function order_store(Request $request)
    {
        if (Session::has('posCart') && count(Session::get('posCart')) > 0) {
            $order = new Order;
            $name = '';
            $email = '';
            $address = '';
            $country = '';
            $city = '';
            $postal_code = '';
            $phone = '';


            if ($request->user_id == null) {
                if (Session::has('pos_shipping_info')) {
                    $pos_shipping_info = Session::get('pos_shipping_info');

                    if (($pos_shipping_info['name'] != null && $pos_shipping_info['phone'] != null) || ($pos_shipping_info['name'] != null && $pos_shipping_info['email'] != null)) {

                        $new_user = new User;
                        $new_user->name = $pos_shipping_info['name'];
                        $new_user->email = $pos_shipping_info['email'];
                        $new_user->phone = $pos_shipping_info['phone'];
                        $new_user->user_type = "customer";
                        $new_user->save();

                        $new_customer = new Customer;
                        $new_customer->user_id = $new_user->id;
                        $new_customer->save();

                        $new_address = new Address;
                        $new_address->user_id = $new_user->id;
                        $new_address->address = $pos_shipping_info['address'];
                        $new_address->country = $pos_shipping_info['country'];
                        $new_address->city = $pos_shipping_info['city'];
                        $new_address->postal_code = $pos_shipping_info['postal_code'];
                        $new_address->phone = $pos_shipping_info['phone'];
                        $new_address->save();

                        $order->user_id = $new_user->id;
                    } else {
                        $order->guest_id = mt_rand(100000, 999999);
                    }

                    $name = $pos_shipping_info['name'];
                    $email = $pos_shipping_info['email'];
                    $address = $pos_shipping_info['address'];
                    $country = $pos_shipping_info['country'];
                    $city = $pos_shipping_info['city'];
                    $postal_code = $pos_shipping_info['postal_code'];
                    $phone = $pos_shipping_info['phone'];
                }

            } else {
                $order->user_id = $request->user_id;
                $user = User::findOrFail($request->user_id);
                $name = $user->name;
                $email = $user->email;

                if ($request->shipping_address != null) {
                    $address_data = Address::findOrFail($request->shipping_address);
                    $address = $address_data->address;
                    $country = $address_data->country;
                    $city = $address_data->city;
                    $postal_code = $address_data->postal_code;
                    $phone = $address_data->phone;
                }
            }

            $data['name'] = $name;
            $data['email'] = $email;
            $data['address'] = $address;
            $data['country'] = $country;
            $data['city'] = $city;
            $data['postal_code'] = $postal_code;
            $data['phone'] = $phone;

            $order->shipping_address = json_encode($data);

            $order->payment_type = $request->payment_type;
            $order->advance_payment = $request->advance_payment;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');
            $order->payment_status = 'paid';
            $order->payment_details = $request->payment_type;
            $order->pos_order = 1;

            if ($order->save()) {
                $subtotal = 0;
                $tax = 0;
                $shipping = 0;
                $total_discount = 0;
                foreach (Session::get('posCart') as $key => $cartItem) {
                    $product = Product::find($cartItem['id']);

                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];

                    $product_variation = $cartItem['variant'];

                    if ($product_variation != null) {
                        $product_stock = $product->stocks->where('variant', $product_variation)->first();
                        if ($cartItem['quantity'] > $product_stock->qty) {
                            $order->delete();
                            return 0;
                        } else {
                            $product_stock->qty -= $cartItem['quantity'];
                            $product_stock->save();
                        }
                    } else {
                        if ($cartItem['quantity'] > $product->current_stock) {
                            $order->delete();
                            return 0;
                        } else {
                            $product->current_stock -= $cartItem['quantity'];
                            $product->save();
                        }
                    }

                    $discount_type = $cartItem['discount_type'] ? $cartItem['discount_type'] : 'none';
                    $discount = $cartItem['discount'] ? $cartItem['discount'] : 0;

                    if ($discount_type == 'percent') {
                        $total_discount += (($cartItem['price'] * $cartItem['quantity']) * $discount) / 100;
                        $discount_amount = (($cartItem['price'] * $cartItem['quantity']) * $discount) / 100;
                    } elseif ($discount_type == 'amount') {
                        $total_discount += $discount;
                        $discount_amount = $discount;
                    } else {
                        $total_discount += $discount;
                        $discount_amount = $discount;
                    }

                    $order_detail = new OrderDetail;
                    $order_detail->order_id = $order->id;
                    $order_detail->seller_id = Auth::user()->id;
                    $order_detail->product_id = $product->id;
                    $order_detail->payment_status = 'paid';
                    $order_detail->variation = $product_variation;
                    $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    $order_detail->discount = $discount_amount;
                    $order_detail->discount_percent = ($discount_type == 'percent') ? $discount : 0;
                    $order_detail->discount_type = $discount_type;
                    $order_detail->shipping_type = null;
                    $order_detail->pos = 1;
                    $order_detail->profit = $cartItem['price'] * $cartItem['quantity'] - $cartItem['purchase_price'] * $cartItem['quantity'];

                    if (Session::get('pos_shipping', 0) > 0) {
                        $order_detail->shipping_cost = Session::get('pos_shipping', 0);
                    } else {
                        $order_detail->shipping_cost = 0;
                    }

                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->save();

                    $product->num_of_sale++;
                    $product->save();
                }

                $order->grand_total = $subtotal + $tax + Session::get('pos_shipping', 0);

                if (Session::has('pos_discount')) {
                    $order->grand_total -= Session::get('pos_discount');
                    $order->coupon_discount = Session::get('pos_discount');
                }
                if (Session::has('pos_advance')) {
                    $order->advance_payment = Session::get('pos_advance');
                    if ($order->advance_payment > 0 && $order->advance_payment < $order->grand_total) {
                        $order->payment_status = 'advance';
                    }
                }

                if (Session::has('pos_previous_due')) {
                    $order->previous_due_payment = Session::get('pos_previous_due');
                
                }

                $order->total_discount = $total_discount ? $total_discount : 0;
                $order->save();

                $array['view'] = 'emails.invoice';
                $array['subject'] = 'Your order has been placed - ' . $order->code;
                $array['from'] = env('MAIL_USERNAME');
                $array['order'] = $order;

                $admin_products = array();
                $seller_products = array();
                foreach ($order->orderDetails as $key => $orderDetail) {
                    if ($orderDetail->product->added_by == 'admin') {
                        array_push($admin_products, $orderDetail->product->id);
                    } else {
                        $product_ids = array();
                        if (array_key_exists($orderDetail->product->user_id, $seller_products)) {
                            $product_ids = $seller_products[$orderDetail->product->user_id];
                        }
                        array_push($product_ids, $orderDetail->product->id);
                        $seller_products[$orderDetail->product->user_id] = $product_ids;
                    }
                }

                foreach ($seller_products as $key => $seller_product) {
                    try {
                        Mail::to(\App\User::find($key)->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {

                    }
                }

                //sends email to customer with the invoice pdf attached
                if (env('MAIL_USERNAME') != null) {
                    try {
                        Mail::to($request->session()->get('pos_shipping_info')['email'])->queue(new InvoiceEmailManager($array));
                        Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {

                    }
                }
                if (isset($request->session()->get('pos_shipping_info')['phone'])) {
                    if ($request->session()->get('pos_shipping_info')['phone'] != '' && \App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                        try {
                            $otpController = new OTPVerificationController;
                            $otpController->send_order_code($order);
                        } catch (\Exception $e) {

                        }
                    }
                }


                if ($request->user_id != NULL) {
                    if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                        $clubpointController = new ClubPointController;
                        $clubpointController->processClubPoints($order);
                    }
                }

                if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                    $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                    foreach ($order->orderDetails as $key => $orderDetail) {
                        $orderDetail->payment_status = 'paid';
                        $orderDetail->save();
                        if ($orderDetail->product->user->user_type == 'seller') {
                            $seller = $orderDetail->product->user->seller;
                            $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                            $seller->save();
                        }
                    }
                } else {
                    foreach ($order->orderDetails as $key => $orderDetail) {
                        $orderDetail->payment_status = 'paid';
                        $orderDetail->save();
                        if ($orderDetail->product->user->user_type == 'seller') {
                            $commission_percentage = $orderDetail->product->category->commision_rate;
                            $seller = $orderDetail->product->user->seller;
                            $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                            $seller->save();
                        }
                    }
                }

                $order->commission_calculated = 1;
                $order->save();

                $request->session()->put('order_id', $order->id);

                Session::forget('pos_shipping_info');
                Session::forget('pos_shipping');
                Session::forget('pos_discount');
                Session::forget('pos_advance');
                Session::forget('posCart');
                Session::forget('pos_shipping_info');
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function pos_activation()
    {
        $pos_activation = BusinessSetting::where('type', 'pos_activation_for_seller')->first();
        return view('pos.pos_activation', compact('pos_activation'));
    }
}
