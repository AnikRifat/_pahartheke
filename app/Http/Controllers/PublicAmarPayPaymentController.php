<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use Auth;
use Illuminate\Routing\UrlGenerator;
use App\Http\Controllers;
use App\Order;
use App\BusinessSetting;
use App\Seller;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\WalletController;
use App\CustomerPackage;
use App\SellerPackage;
use App\Http\Controllers\CustomerPackageController;
session_start();

class PublicAmarPayPaymentController extends Controller
{
    public function index(Request $request){
        if(Session::get('payment_type') == 'cart_payment'){
            $order = Order::findOrFail($request->session()->get('order_id'));
            $tran_id = substr(md5($request->session()->get('order_id')), 0, 10);

            $currency= "BDT";
            $amount = $order->grand_total;
            if(BusinessSetting::where('type', 'aamar_pay_sandbox')->first()->value == 1){
                $store_id = "aamarpaytest";
                $signature_key = "dbb74894e82415a2f7ff0ec3a97e4183";
                $url = "https://sandbox.aamarpay.com/jsonpost.php";
            }else{
                $store_id = env('AAMARPAY_STORE_ID');
                $signature_key = env('AAMARPAY_SIGNATURE_KEY');
                $url = "https://secure.aamarpay.com/jsonpost.php";
            }

            $cus_name = $request->session()->get('shipping_info')['name'];
            $cus_phone = $request->session()->get('shipping_info')['phone'];
            $cus_email = $request->session()->get('shipping_info')['email'];
            $cus_add1 = $request->session()->get('shipping_info')['address'];
            $cus_city = $request->session()->get('shipping_info')['city'];
            $cus_postcode = $request->session()->get('shipping_info')['postal_code'];
            $cus_country = $request->session()->get('shipping_info')['country'];
            $payment_type = $request->session()->get('payment_type');
            $order_id = $request->session()->get('order_id');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "store_id": "'.$store_id.'",
                    "signature_key": "'.$signature_key.'",
                    "tran_id": "'.$tran_id.'",
                    "amount": "'.$amount.'",
                    "currency": "'.$currency.'",
                    "desc": "Pahar theke site payment",
                    "success_url": "'.route('aamarpay.success').'",
                    "fail_url": "'.route('aamarpay.fail').'",
                    "cancel_url": "'.route('aamarpay.cancel').'",
                    "desc": "Merchant Registration Payment",
                    "cus_name": "'.$cus_name.'",
                    "cus_email": "'.$cus_phone.'@pahartheke.com'.'",
                    "cus_phone": "'.$cus_phone.'",
                    "cus_add1": "'.$cus_add1.'",
                    "cus_city": "'.$cus_city.'",
                    "cus_postcode": "'.$cus_postcode.'",
                    "cus_country": "'.$cus_country.'",
                    "opt_a": "'.$payment_type.'",
                    "opt_b": "'.$order_id.'",
                    "type": "json"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $responseObj = json_decode($response);
            if(isset($responseObj->payment_url) && !empty($responseObj->payment_url)) {
                $paymentUrl = $responseObj->payment_url;
                return redirect()->away($paymentUrl);
            }else{
                return redirect()->back();
            }
        }

    }

    public function success(Request $request){

        $request_id = $request->mer_txnid;

        //verify the transection using Search Transection API

        $url = "http://sandbox.aamarpay.com/api/v1/trxcheck/request.php?request_id=$request_id&store_id=aamarpaytest&signature_key=dbb74894e82415a2f7ff0ec3a97e4183&type=json";

        //For Live Transection Use "http://secure.aamarpay.com/api/v1/trxcheck/request.php"

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resObj = (object) json_decode($response, true);
        if(isset($resObj->opt_a) && isset($resObj->opt_b)){
            if($resObj->opt_a == 'cart_payment'){
                $checkoutController = new CheckoutController;
                return $checkoutController->checkout_done($resObj->opt_b, $response);
            }
        }
    }

    public function fail(Request $request){
        $request->session()->forget('order_id');
        $request->session()->forget('payment_data');
        Session::forget('cart');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');
        Session::forget('ec_shipping_info');
        Session::forget('total_rules_discount');
        flash(translate('Payment failed but order saved'))->success();
        return redirect()->route('home');
    }

    public function cancel(Request $request){
        $request->session()->forget('order_id');
        $request->session()->forget('payment_data');
        Session::forget('cart');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');
        Session::forget('ec_shipping_info');
        Session::forget('total_rules_discount');
        flash(translate('Payment cancelled but order saved'))->success();
        return redirect()->route('home');
    }
}