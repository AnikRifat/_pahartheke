<?php

namespace App\Http\Controllers;

use App\Utility\PayfastUtility;
use Illuminate\Http\Request;
use Auth;
use App\Category;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\PaytmController;
use App\Order;
use App\BusinessSetting;
use App\Coupon;
use App\CouponUsage;
use App\CommissionHistory;
use App\User;
use App\Address;
use App\City;
use App\Product;
use App\OrderDetail;
use Session;
use DB;
use PDF;
use Mail;
use App\Utility\PayhereUtility;
use App\Supports\Api\FacebookPixelConversationApi;
use Illuminate\Support\Facades\Hash;
use App\DiscountRule;
use App\Mail\InvoiceEmailManager;

class CheckoutController extends Controller
{
    use FacebookPixelConversationApi;
    public function __construct()
    {
        //
    }

    public function get_user($phone)
    {
        $user = User::where('phone', $phone)->first();
        return response()->json([
            'user' => $user
        ]);
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        if ($request->payment_option != null) {

            $orderController = new OrderController;
            $orderController->store($request);

            $request->session()->put('payment_type', 'cart_payment');
            // dd($request->payment_option);
            if ($request->session()->get('order_id') != null) {
                if ($request->payment_option == 'paypal') {
                    $paypal = new PaypalController;
                    return $paypal->getCheckout();
                } elseif ($request->payment_option == 'stripe') {
                    $stripe = new StripePaymentController;
                    return $stripe->stripe();
                } elseif ($request->payment_option == 'sslcommerz') {
                    $sslcommerz = new PublicSslCommerzPaymentController;
                    return $sslcommerz->index($request);
                } elseif ($request->payment_option == 'instamojo') {
                    $instamojo = new InstamojoController;
                    return $instamojo->pay($request);
                } elseif ($request->payment_option == 'razorpay') {
                    $razorpay = new RazorpayController;
                    return $razorpay->payWithRazorpay($request);
                } elseif ($request->payment_option == 'paystack') {
                    $paystack = new PaystackController;
                    return $paystack->redirectToGateway($request);
                } elseif ($request->payment_option == 'voguepay') {
                    $voguePay = new VoguePayController;
                    return $voguePay->customer_showForm();
                } elseif ($request->payment_option == 'payhere') {
                    $order = Order::findOrFail($request->session()->get('order_id'));

                    $order_id = $order->id;
                    $amount = $order->grand_total;
                    $first_name = json_decode($order->shipping_address)->name;
                    $last_name = 'X';
                    $phone = json_decode($order->shipping_address)->phone;
                    $email = json_decode($order->shipping_address)->email;
                    $address = json_decode($order->shipping_address)->address;
                    $city = json_decode($order->shipping_address)->city;

                    return PayhereUtility::create_checkout_form($order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
                } elseif ($request->payment_option == 'payfast') {
                    $order = Order::findOrFail($request->session()->get('order_id'));

                    $order_id = $order->id;
                    $amount = $order->grand_total;

                    return PayfastUtility::create_checkout_form($order_id, $amount);
                } else if ($request->payment_option == 'ngenius') {
                    $ngenius = new NgeniusController();
                    return $ngenius->pay();
                } else if ($request->payment_option == 'iyzico') {
                    $iyzico = new IyzicoController();
                    return $iyzico->pay();
                } else if ($request->payment_option == 'nagad') {
                    $nagad = new NagadController;
                    return $nagad->getSession();
                } else if ($request->payment_option == 'bkash') {
                    $bkash = new BkashController;
                    return $bkash->pay();
                } else if ($request->payment_option == 'flutterwave') {
                    $flutterwave = new FlutterwaveController();
                    return $flutterwave->pay();
                } else if ($request->payment_option == 'mpesa') {
                    $mpesa = new MpesaController();
                    return $mpesa->pay();
                } elseif ($request->payment_option == 'paytm') {
                    $paytm = new PaytmController;
                    return $paytm->index();
                } elseif ($request->payment_option == 'cash_on_delivery') {
                    // dd(Session::all());
                    // $request->session()->put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
                    // $request->session()->forget('owner_id');
                    $request->session()->forget('cart');
                    $request->session()->forget('delivery_info');
                    $request->session()->forget('coupon_id');
                    $request->session()->forget('coupon_discount');
                    $request->session()->forget('payment_type');

                    flash(translate("Your order has been placed successfully"))->success();
                    return redirect()->route('order_confirmed');
                } elseif ($request->payment_option == 'wallet') {
                    $user = Auth::user();
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    if ($user->balance >= $order->grand_total) {
                        $user->balance -= $order->grand_total;
                        $user->save();
                        return $this->checkout_done($request->session()->get('order_id'), null);
                    }
                } else {
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    $order->manual_payment = 1;
                    $order->save();

                    // $request->session()->put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
                    // $request->session()->forget('owner_id');
                    $request->session()->forget('cart');
                    $request->session()->forget('delivery_info');
                    $request->session()->forget('coupon_id');
                    $request->session()->forget('coupon_discount');
                    $request->session()->forget('payment_type');
                    $request->session()->forget('ec_shipping_info');

                    flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                    return redirect()->route('order_confirmed');
                }
            }
        } else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    /**
     * [easy_checkout process]
     * @param  Request $request 
     * @return [view]       
     */
    public function easy_checkout(Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }
        return view('frontend.easy_checkout', compact('user'));
    }

    public function easy_order_confirm(Request $request)
    {
        // dd($request->all());
        $name = $request->name;
        $phone = $request->phone;
        $address = $request->address;
        $city = City::findOrFail($request->city)->name;
        $shipping_address = [
            "name" => $name,
            "email" => "",
            "address" => $address,
            "country" => "Bangladesh",
            "city" => $city,
            "postal_code" => "",
            "phone" => $phone,
            "checkout_type" => "logged"
        ];

        $request->session()->put('shipping_info', $shipping_address);
        $order = new Order;
        if (Auth::check()) {
            $user = Auth::user();
            $order->user_id = $user->id;
        } else {
            // $order->guest_id = mt_rand(100000, 999999);
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                $user = new User();
                $user->name = $name;
                $user->phone = $phone;
                $user->city = $city;
                $user->address = $address;
                $user->password = Hash::make(str_replace('+88', '', $phone));
                $user->save();
            }
            $order->user_id = $user->id;
        }
        $order->shipping_address = json_encode($shipping_address);
        $order->payment_type = $request->payment_option;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->date = strtotime('now');

        if ($order->save()) {
            $subtotal = 0;
            $tax = 0;
            $total_discount = 0;
            $shipping = 0;

            //calculate shipping is to get shipping costs of different types
            $admin_products = array();
            $seller_products = array();

            //Order Details Storing
            $count_ = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
                $product = Product::find($cartItem['id']);

                if ($product->added_by == 'admin') {
                    array_push($admin_products, $cartItem['id']);
                } else {
                    $product_ids = array();
                    if (array_key_exists($product->user_id, $seller_products)) {
                        $product_ids = $seller_products[$product->user_id];
                    }
                    array_push($product_ids, $cartItem['id']);
                    $seller_products[$product->user_id] = $product_ids;
                }

                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $total_discount += $cartItem['discount'] * $cartItem['quantity'];

                $product_variation = $cartItem['variant'];

                if ($product_variation != null) {
                    $product_stock = $product->stocks->where('variant', $product_variation)->first();
                    if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }
                } else {
                    if ($product->digital != 1 && $cartItem['quantity'] > $product->current_stock) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product->current_stock -= $cartItem['quantity'];
                        $product->save();
                    }
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                $order_detail->discount = $cartItem['discount'] * $cartItem['quantity'];
                $order_detail->profit = $cartItem['price'] * $cartItem['quantity'] - $cartItem['purchase_price'] * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                $order_detail->shipping_type = 'home_delivery';
                $order_detail->product_referral_code = $cartItem['product_referral_code'];

                //Dividing Shipping Costs
                if ($order_detail->shipping_type == 'home_delivery') {
                    $order_detail->shipping_cost = getShippingCost($key);
                } else {
                    $order_detail->shipping_cost = 0;
                }

                if ($count_ == 0) {
                    $shipping = $order_detail->shipping_cost;
                }
                $count_++;


                //End of storing shipping cost

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale++;
                $product->save();
            }

            $order->grand_total = $subtotal + $tax + $shipping;

            if (Session::has('coupon_discount')) {
                $order->grand_total -= Session::get('coupon_discount');
                $order->coupon_discount = Session::get('coupon_discount');

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Session::get('coupon_id');
                $coupon_usage->save();
            }

            $condition_discount = false;
            if ($condition_discount) {
                $discountRule = DiscountRule::where('status', 1)
                    ->where('condition_key', 1) // Total Amount condition
                    ->where('conditon_oprator', '<=', $subtotal)
                    ->where('expire_date', '>=', date('Y-m-d')) // Check if the rule is not expired
                    ->orderBy('conditon_value', 'desc')
                    ->first();
            }
            if (Session::has('total_rules_discount')) {
                \Log::info(['total_rules_discount', Session::get('total_rules_discount')]);
                $total_discount += Session::get('total_rules_discount');
            }
            $order->total_discount = $total_discount;
            if (!is_null($user) && !is_null($city)) {
                $user->city = $city;
                $user->address = $address;
                $user->save();
            }

            $order->save();
            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_USERNAME');
            $array['order'] = $order;

            foreach ($seller_products as $key => $seller_product) {
                try {
                    Mail::to(\App\User::find($key)->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }

            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_order_code($order);
                } catch (\Exception $e) {

                }
            }

            //sends email to customer with the invoice pdf attached
            if (env('MAIL_USERNAME') != null) {
                try {
                    Mail::to($request->session()->get('shipping_info')['email'])->queue(new InvoiceEmailManager($array));
                    Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }
            $request->session()->put('order_id', $order->id);

        }
        /**
         * Redirect user if the want to pay via sslcommerz else complete the order here
         */
        if ($request->payment_option == 'sslcommerz') {
            $request->session()->put('payment_type', 'cart_payment');
            $sslcommerz = new PublicSslCommerzPaymentController;
            return $sslcommerz->index($request);
        } elseif ($request->payment_option == 'aamar_pay') {
            $request->session()->put('payment_type', 'cart_payment');
            $aamarpay = new PublicAmarPayPaymentController;
            return $aamarpay->index($request);
        } else {
            $request->session()->forget('cart');
            $request->session()->forget('delivery_info');
            $request->session()->forget('coupon_id');
            $request->session()->forget('coupon_discount');
            $request->session()->forget('payment_type');
            $request->session()->forget('ec_shipping_info');
            $request->session()->forget('total_rules_discount');

            flash(translate('Your order has been placed successfully'))->success();
            return redirect()->route('order_confirmed');
        }
    }

    public function easy_checkout_up_shipping(Request $request, $id)
    {
        $city = City::findOrFail($id);
        Session::forget('ec_shipping_info');
        $request->session()->put('ec_shipping_info', $city);
        return $city;
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($order_id, $payment)
    {
        $order = Order::findOrFail($order_id);
        $order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            if (Auth::check()) {
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
                    $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                    $seller->save();

                    $commission_history = new CommissionHistory;
                    $commission_history->order_id = $order->id;
                    $commission_history->order_detail_id = $orderDetail->id;
                    $commission_history->seller_id = $orderDetail->seller_id;
                    $commission_history->seller_earning = ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                    $commission_history->admin_commission = ($orderDetail->price * $commission_percentage) / 100 + $orderDetail->shipping_cost;
                    $commission_history->save();
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = 'paid';
                $orderDetail->save();
                if ($orderDetail->product->user->user_type == 'seller') {
                    $commission_percentage = $orderDetail->product->category->commision_rate;
                    $seller = $orderDetail->product->user->seller;
                    $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                    $seller->save();

                    $commission_history = new CommissionHistory;
                    $commission_history->order_id = $order->id;
                    $commission_history->order_detail_id = $orderDetail->id;
                    $commission_history->seller_id = $orderDetail->seller_id;
                    $commission_history->seller_earning = ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                    $commission_history->admin_commission = ($orderDetail->price * $commission_percentage) / 100 + $orderDetail->shipping_cost;
                    $commission_history->save();
                }
            }
        }

        $order->commission_calculated = 1;
        $order->save();

        // if (Session::has('cart')) {
        //     Session::put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
        // }
        Session::forget('cart');
        // Session::forget('owner_id');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');
        Session::forget('ec_shipping_info');
        Session::forget('total_rules_discount');

        // flash(translate('Payment completed'))->success();
        return view('frontend.order_confirmed', compact('order'));
    }

    public function get_shipping_info(Request $request)
    {
        if (Session::has('cart') && count(Session::get('cart')) > 0) {
            return redirect()->route('checkout.easy_checkout');
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if ($request->address_id == null) {
            flash(translate("Please add shipping address"))->warning();
            return redirect()->route('checkout.shipping_info');
        }
        if (Auth::check()) {
            $address = Address::findOrFail($request->address_id);
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
            $data['checkout_type'] = $request->checkout_type;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
            $data['checkout_type'] = $request->checkout_type;
        }

        $shipping_info = $data;
        $request->session()->put('shipping_info', $shipping_info);

        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem) {
            $subtotal += $cartItem['price'] * $cartItem['quantity'];
            $tax += $cartItem['tax'] * $cartItem['quantity'];
            //$shipping += $cartItem['shipping'] * $cartItem['quantity'];

            if ($key == 0) {
                $shipping = $cartItem['shipping'];
            }
        }

        $total = $subtotal + $tax + $shipping;

        if (Session::has('coupon_discount')) {
            $total -= Session::get('coupon_discount');
        }
        // return view('frontend.delivery_info');
        return view('frontend.payment_select', compact('total'));
    }

    public function store_delivery_info(Request $request)
    {
        $request->session()->put('owner_id', $request->owner_id);

        if (Session::has('cart') && count(Session::get('cart')) > 0) {
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key) use ($request) {
                if (\App\Product::find($object['id'])->user_id == $request->owner_id) {
                    if ($request['shipping_type_' . $request->owner_id] == 'pickup_point') {
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request['pickup_point_id_' . $request->owner_id];
                    } else {
                        $object['shipping_type'] = 'home_delivery';
                    }
                }
                return $object;
            });

            $request->session()->put('cart', $cart);

            $cart = $cart->map(function ($object, $key) use ($request) {
                if (\App\Product::find($object['id'])->user_id == $request->owner_id) {
                    if ($object['shipping_type'] == 'home_delivery') {
                        $object['shipping'] = getShippingCost($key);
                    } else {
                        $object['shipping'] = 0;
                    }
                } else {
                    $object['shipping'] = 0;
                }
                return $object;
            });

            $request->session()->put('cart', $cart);

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            }

            $total = $subtotal + $tax + $shipping;

            if (Session::has('coupon_discount')) {
                $total -= Session::get('coupon_discount');
            }

            return view('frontend.payment_select', compact('total'));
        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function get_payment_info(Request $request)
    {
        if (!Session::has('cart')) {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
        if (!Session::has('shipping_info')) {
            flash(translate("Please add shipping address"))->warning();
            return redirect()->route('checkout.shipping_info');
        }
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem) {
            $subtotal += $cartItem['price'] * $cartItem['quantity'];
            $tax += $cartItem['tax'] * $cartItem['quantity'];
            $shipping += $cartItem['shipping'] * $cartItem['quantity'];
        }

        $total = $subtotal + $tax + $shipping;

        if (Session::has('coupon_discount')) {
            $total -= Session::get('coupon_discount');
        }

        return view('frontend.payment_select', compact('total'));
    }

    public function apply_coupon_code(Request $request)
    {
        //dd($request->all());
        $coupon = Coupon::where('code', $request->code)->first();

        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);

                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach (Session::get('cart') as $key => $cartItem) {
                            $subtotal += $cartItem['price'] * $cartItem['quantity'];
                            $tax += $cartItem['tax'] * $cartItem['quantity'];
                            $shipping += $cartItem['shipping'] * $cartItem['quantity'];
                        }
                        $sum = $subtotal + $tax + $shipping;

                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                            $request->session()->put('coupon_id', $coupon->id);
                            $request->session()->put('coupon_discount', $coupon_discount);
                            flash(translate('Coupon has been applied'))->success();
                        }
                    } elseif ($coupon->type == 'product_base') {
                        $coupon_discount = 0;
                        foreach (Session::get('cart') as $key => $cartItem) {
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += $cartItem['price'] * $coupon->discount / 100;
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount;
                                    }
                                }
                            }
                        }
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_discount', $coupon_discount);
                        flash(translate('Coupon has been applied'))->success();
                    }
                } else {
                    flash(translate('You already used this coupon!'))->warning();
                }
            } else {
                flash(translate('Coupon expired!'))->warning();
            }
        } else {
            flash(translate('Invalid coupon!'))->warning();
        }
        return back();
    }

    public function remove_coupon_code(Request $request)
    {
        $request->session()->forget('coupon_id');
        $request->session()->forget('coupon_discount');
        return back();
    }

    public function order_confirmed()
    {
        $order = Order::findOrFail(Session::get('order_id'));
        $user = User::findOrFail($order->user_id);
        $total_quantity = $order->orderDetails->sum('quantity');


        $event_info = [
            'event_type' => 'Purchase',
            'event_source_url' => 'https://pahartheke.com'
        ];

        $user_info = [
            'email' => [],
            'phone' => [],
        ];

        $event_content_data = [
            'id' => $order['id'],
            'qty' => $total_quantity,
            'value' => $order['grand_total'],
            'currency' => 'BDT',
        ];

        if (Auth::user()) {
            $user_info['email'] = [Auth::user()->email];
            $user_info['phone'] = [Auth::user()->phone];
        } elseif (!is_null($user && !is_null($user->email) && !is_null($user->phone))) {
            $user_info['email'] = [$user->email];
            $user_info['phone'] = [$user->phone];
        }

        $response = $this->send_event($event_info, $user_info, $event_content_data);
        return view('frontend.order_confirmed', compact('order'));
    }
}