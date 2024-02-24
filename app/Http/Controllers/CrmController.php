<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Services\CrmService;
use Illuminate\Http\Request;
use App\User;
use Yajra\DataTables\DataTables;

class CrmController extends Controller
{
    public function index(CrmService $crmService)
    {
        $categoryData = $crmService->ordersGroupedByCategory();
        // dd($categoryData);
        $all_cities = $crmService->getAllCities();
        $products = Product::all();
        $categories = Category::all();
// dd($categories);
        $cityData = $crmService->ordersGroupedByCity();

        return view('backend.crm.customers_list', compact('all_cities', 'categories','products', 'cityData', 'categoryData'));
    }

    public function prodwise_sale_report(Request $request, CrmService $crmService)
    {
        $ordersGroupedByCity = $crmService->ordersGroupedByCity();

        return view('backend.crm.orders_by_city', compact('ordersGroupedByCity'));
    }

    public function customersByCity(CrmService $crmService, $city)
    {
        $all_cities = $crmService->getAllCities();
        $all_categories = Category::all();


        return view('backend.crm.customers_list', compact('city', 'all_cities', 'all_categories'));
    }

    public function customerGet(Request $request)
    {
        $data = $request->validate([
            'content' => 'required',
            'selected_customers' => 'required|array',
            'selected_customers.*' => 'exists:users,id',
        ]);

        $selectedCustomers = $request->input('selected_customers');
        $customers = User::with('orders')->whereIn('id', $selectedCustomers)->get();


        foreach ($customers as $customer) {
            sendSMS($customer->phone, env('APP_NAME'), $data['content']);
        }
        flash(translate('Sms has been Send successfully'))->success();
        return redirect()->back();
    }

    public function productsByCategory(Category $category)
    {
        $products = $category->products->pluck('name', 'id');

        $response = $products->map(function ($productName, $productId) {
            return [
                'id' => $productId,
                'product' => $productName,
            ];
        });
    // dd($response);
        return $response->toJson();

    }

    public function customersDataTable(CrmService $crmService, $city, $product,$categoryId, $date = null)
    {

        $summery = $crmService->getOrdersByFilter($city, $product, $categoryId,$date);
        // dd($summery);
        $data = $this->formatCustomersDataTable($summery);
        return $data;
    }

    public function getFilteredCustomers($city, $category)
    {
        if ($city == 0 && $category == 0) {
            return User::with('orders')->get();
        }

        $cityId = ($city == 0) ? null : $city;
        $categoryId = ($category == 0) ? null : $category;
        $query = User::with('orders');

        if ($cityId !== null) {
            $query->whereHas('orders', function ($innerQuery) use ($cityId) {
                $innerQuery->where('cancelled', 0)
                    ->where('payment_status', 'paid')
                    ->where('shipping_address', 'like', '%' . $cityId . '%');
            });
        }

        if ($categoryId !== null) {
            $query->whereHas('orders', function ($innerQuery) use ($categoryId) {
                $innerQuery->where('cancelled', 0)
                    ->where('payment_status', 'paid')
                    ->whereHas('orderDetails', function ($innerInnerQuery) use ($categoryId) {
                        $innerInnerQuery->where('category_id', $categoryId);
                    });
            });
        }

        return $query->get();
    }

    public function customersDataTablePrint(CrmService $crmService,Request $request)
    {

        $summery = $crmService->getOrdersByFilter($request->city, $request->product, $request->categoryId,$request->date);

        return view('backend.crm.print',compact('summery'));
    }
    public function formatCustomersDataTable($summery)
    {
        $customerSummary = $summery['customer_summary'];

        $totalOrders = $summery['orders_summery']['total_orders'];
        $totalSale = $summery['orders_summery']['total_sale'];
        $totalDiscount = $summery['orders_summery']['total_discount'];

        return DataTables::of($customerSummary)
            ->addColumn('select', function ($customer) {
                return '<input type="checkbox" name="selected_customers[]" value="' . $customer['customer_id'] . '">';
            })
            ->addColumn('sl_no', function ($customer) {
                return $customer['customer_id'];
            })
            ->with([
                'sum_of_total_orders' => $totalOrders,
                'sum_of_purchase_amount' => single_price($totalSale),
                'sum_of_discount' => single_price($totalDiscount),
            ])
            ->rawColumns(['select'])
            ->make(true);
    }







    // public function formatCustomersDataTable($summery)
    // {
    //     $totalPurchaseAmount = 0;
    //     $totalDiscount = 0;
    //     $sum_of_total_orders = 0;

    //     foreach ($customers as $customer) {
    //         $customerOrders = $customer->getCustomerOrdersByDate($date)->where('payment_status', 'paid');
    //         $totalPurchaseAmount += $customerOrders->sum('grand_total');
    //         $totalDiscount += $customerOrders->sum('total_discount');
    //         $sum_of_total_orders += $customerOrders->count();
    //     }
    //     // dd($totalPurchaseAmount, $sum_of_total_orders);
    //     return DataTables::of($customers)
    //         ->addColumn('select', function ($customer) {
    //             return '<input type="checkbox" name="selected_customers[]" value="' . $customer->id . '">';
    //         })
    //         ->addColumn('sl_no', function ($customer) {
    //             return $customer->id;
    //         })
    //         ->addColumn('total_orders', function ($customer) use ($date) {
    //             return $customer->getCustomerOrdersByDate($date)->count();
    //         })
    //         ->addColumn('total_purchase_amount', function ($customer) use ($date) {
    //             return single_price($customer->getCustomerOrdersByDate($date)->sum('grand_total'));
    //         })
    //         ->addColumn('total_discount', function ($customer) use ($date) {
    //             return single_price($customer->getCustomerOrdersByDate($date)->sum('total_discount'));
    //         })
    //         ->with([
    //             'sum_of_purchase_amount' => single_price($totalPurchaseAmount),
    //             'sum_of_discount' => single_price($totalDiscount),
    //             'sum_of_total_orders' => $sum_of_total_orders,
    //         ])
    //         ->rawColumns(['select'])
    //         ->make(true);
    // }



}
