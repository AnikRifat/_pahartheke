<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Seller;
use App\User;
use App\Search;
use App\Order;
use App\OrderDetail;
use Excel;
use Auth;
use App\CommissionHistory;
use App\Exports\OrdersExport;
use App\Exports\ProdWiseSalesReportExport;
use DB;
use URL;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sale_report(Request $request)
    {
        $date = $request->date;
        $net = 0;
        $profit = 0;
        $items = 0;
        $num_orders = 0;
        $tax = 0;
        $shipping = 0;
        $coupon = 0;

        $orders = Order::query()->where('payment_status', 'paid')->where('cancelled', 0);
        if ($date != null) {
            $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        foreach ($orders->with('orderDetails')->get() as $key => $order) {
            $net += $order->grand_total;
            $num_orders += 1;
            $coupon += $order->coupon_discount;
            if ($order->orderDetails != null) {
                $items += $order->orderDetails->count();
                $shipping += $order->orderDetails->sum('shipping_cost');
                $tax += $order->orderDetails->sum('tax');
                $profit += $order->orderDetails->sum('profit');
            }
        }
        // dd($net);

        if ($request->button == 'export') {
            return Excel::download(new OrdersExport($orders->latest()->get()), 'orders.xlsx');
        }
        return view('backend.reports.sale_report', compact('date', 'net', 'profit', 'items', 'num_orders', 'tax', 'shipping', 'coupon'));
    }




    public function prodwise_sale_report(Request $request)
    {
        
        $query = OrderDetail::with('product')
        ->whereIn('delivery_status', ['on_delivery', 'delivered', 'confirmed'])->orderBy('updated_at','desc');
    
    if ($request->date && !is_null($request->date)) {
        $query->whereDate('updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $request->date)[0])))
              ->whereDate('updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $request->date)[1])));
    }

        if ($request->product_id && !is_null($request->product_id)) {
            $query->where('product_id', $request->product_id);
        }

        //  $query->selectRaw('SUM(price) as total_order_amount');
        //  $query->selectRaw('SUM(quantity) as total_sale_quantity');
   
        //  $query->selectRaw('SUM(CASE WHEN order_details.pos = 1 THEN order_details.quantity ELSE 0 END) as total_pos_quantity');
        //  $query->selectRaw('SUM(CASE WHEN order_details.pos = 0 THEN order_details.quantity ELSE 0 END) as total_web_quantity');
        //  $query->groupBy('id');
        // dd($query->pluck('updated_at'));

        $sales = $query->paginate(30);
        // if ($request->date && !is_null($request->date)) {
        //     $sales->appends(['date' => $request->date]);
        //     $sales->setPath(URL::current());
        // }

        if ($request->export) {
            return Excel::download(new ProdWiseSalesReportExport($query->get()), 'product_wise_sales.xlsx');
        }
        $all_products = Product::orderBy('updated_at', 'desc')->get(['id', 'name']);

        return view('backend.reports.prodwise_sale_report', compact('sales', 'all_products'));
    }
    public function stock_report(Request $request)
    {
        $sort_by = null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(15);
        return view('backend.reports.stock_report', compact('products', 'sort_by'));
    }

    public function in_house_sale_report(Request $request)
    {
        $sort_by = null;
        $products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('category_id')) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(15);
        return view('backend.reports.in_house_sale_report', compact('products', 'sort_by'));
    }

    public function seller_sale_report(Request $request)
    {
        $seller_id = null;
        $date = $request->date;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;

        $order_details = OrderDetail::where('seller_id', '!=', $admin_user_id);

        if ($request->seller_id != null) {
            $seller_id = $request->seller_id;
            $order_details = $order_details->where('seller_id', $seller_id);
        }
        if ($date != null) {
            $order_details = $order_details->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        $order_details = $order_details->latest()->paginate(15);
        return view('backend.reports.seller_sale_report', compact('order_details', 'seller_id', 'date'));
    }

    public function wish_report(Request $request)
    {
        $sort_by = null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(10);
        return view('backend.reports.wish_report', compact('products', 'sort_by'));
    }

    public function user_search_report(Request $request)
    {
        $searches = Search::orderBy('count', 'desc')->paginate(10);
        return view('backend.reports.user_search_report', compact('searches'));
    }

    public function commission_history(Request $request)
    {
        $seller_id = null;
        $date_range = null;

        if (Auth::user()->user_type == 'seller') {
            $seller_id = $request->seller_id;
        }
        if ($request->seller_id) {
            $seller_id = $request->seller_id;
        }

        $commission_history = CommissionHistory::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id) {

            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }

        $commission_history = $commission_history->paginate(10);
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
        }
        return view('backend.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
    }
}
