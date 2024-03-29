<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use PDF;
use Session;
use Auth;
use Config;

class InvoiceController extends Controller
{
    public function invoice_download($id)
    {
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
            // bengali font
            $font_family = "'Hind Siliguri','sans-serif'";
        }elseif($currency_code == 'KHR' || $language_code == 'kh'){
            // khmer font
            $font_family = "'Hanuman','sans-serif'";
        }elseif($currency_code == 'AMD'){
            // Armenia font
            $font_family = "'arnamu','sans-serif'";
        }elseif($currency_code == 'ILS'){
            // Israeli font
            $font_family = "'Varela Round','sans-serif'";
        }elseif($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa'){
            // middle east/arabic font
            $font_family = "'XBRiyaz','sans-serif'";
        }else{
            // general for all
            $font_family = "'Roboto','sans-serif'";
        }

        $order = Order::findOrFail($id);

        $delivery_charge = \DB::table('order_details')->where('order_id', $id)->first('shipping_cost');
        return PDF::loadView('backend.invoices.invoice',[
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'delivery_charge'   => $delivery_charge,
        ], [], [])->download('order-'.$order->code.'.pdf');
    }

    //downloads customer invoice
    public function customer_invoice_download($id)
    {
        $order = Order::findOrFail($id);
        $delivery_charge = \DB::table('order_details')->where('order_id', $id)->first('shipping_cost');
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.invoices.customer_invoice', compact('order', 'delivery_charge'));
        return $pdf->download('order-'.$order->code.'.pdf');
    }

    //downloads seller invoice
    public function seller_invoice_download($id)
    {
        $delivery_charge = \DB::table('order_details')->where('order_id', $id)->first('shipping_cost');
        $order = Order::findOrFail($id);
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.invoices.seller_invoice', compact('order', 'delivery_charge'));
        return $pdf->download('order-'.$order->code.'.pdf');
    }

    //downloads admin invoice
    public function admin_invoice_download($id)
    {
        $delivery_charge = \DB::table('order_details')->where('order_id', $id)->first('shipping_cost');
        $order = Order::findOrFail($id);
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.invoices.admin_invoice', compact('order', 'delivery_charge'));
        return $pdf->download('order-'.$order->code.'.pdf');
    }
    public function admin_invoice_print($order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('backend.invoices.small_invoice', compact('order'));
    }
    public function admin_invoice_print_a4($order_id)
    {
        $delivery_charge = \DB::table('order_details')->where('order_id', $order_id)->first('shipping_cost');
        $order = Order::findOrFail($order_id);
        return view('backend.invoices.invoice_a4', compact('order', 'delivery_charge'));
        //return view('backend.invoices.small_invoice', compact('order'));
    }
}
