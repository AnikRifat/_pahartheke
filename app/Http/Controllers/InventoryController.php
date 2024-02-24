<?php

namespace App\Http\Controllers;

use App\OrderDetail;
use App\Product;
use App\ProductPurchase;
use App\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laracon21\Combinations\Combinations;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request['button'] == 'adjust') {
            return $this->adjustStock($request);
        }

        $date = $request->date;
        $product_id = $request->product_id;
        // dd($product_id);
        $sort_search = null;

        $products = Product::query();

        $reports = ProductPurchase::query();



        // if ($request['button'] == 'filter') {

        // }

        // dd($request->date);

        if ($request['button'] == 'reset') {
            $product_id = null;
            $date = null;
        }

        if ($product_id != null) {
            $reports = $reports->where('product_id', $product_id);
            $products = $products->where('id', $product_id);
        }
        // dd($reports->get());
        if ($date != null) {
            $reports = $reports->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        $products = $products->where('digital', 0)->where('published', 1)->orderBy('current_stock', 'desc')->get();
        $reports = $reports->orderBy('created_at', 'desc')->paginate(10);
        // dd($reports);

        return view('backend.inventory.index', compact('reports', 'date', 'products', 'sort_search'));
    }
    public function adjustStock($request)
    {

        $productId = $request->input('product_id');

        $product = Product::findOrFail($productId);

        return view('backend.inventory.adjustments', compact('product'));
    }
    // public function warehouse()
    // {$request


    //     // dd($reports);
    //     return view('backend.inventory.purchase', compact('reports'));
    // }

    public function productMetrics()
    {
        $monthlyDemandByProduct = [];
        $monthlyTotalAmountByProduct = [];
        $startDate = Carbon::now()->subMonths(2);
        $orders = OrderDetail::where('created_at', '>=', $startDate)->with('product')->get();

        foreach ($orders as $order) {
            if ($order->product) {
                $productName = $order->product->name;
                $month = $order->created_at->format('Y-m');
                $monthlyDemandByProduct[$productName][$month] = ($monthlyDemandByProduct[$productName][$month] ?? 0) + 1;
                $monthlyTotalAmountByProduct[$productName][$month] = ($monthlyTotalAmountByProduct[$productName][$month] ?? 0) + $order->price;
            }
        }

        $productMetrics = [];

        foreach ($monthlyDemandByProduct as $product => $monthlyDemand) {
            $totalDemand = array_sum($monthlyDemand);
            $totalAmount = array_sum($monthlyTotalAmountByProduct[$product]);
            $orderContributionRatio = ($totalDemand > 0) ? ($totalDemand / count($orders)) * 100 : 0;
            $salesAmountContributionRatio = ($totalAmount > 0) ? ($totalAmount / $orders->sum('price')) * 100 : 0;
            $currentStock = Product::where('name', $product)->value('current_stock');
            $demandRatio = $orderContributionRatio > 0 ? $salesAmountContributionRatio / $orderContributionRatio : 0;

            // Calculate average per month stock
            $totalStock = Product::where('name', $product)->sum('current_stock');
            $months = count(array_keys($monthlyDemand));
            $PerMonthNeedRate = ($months > 0) ? $totalStock / $months : 0;

            $productMetrics[$product] = [
                'productName' => $product,
                'PerMonthNeedRate' => $PerMonthNeedRate,
                'orderContributionRatio' => $orderContributionRatio,
                'salesAmountContributionRatio' => $salesAmountContributionRatio,
                'demandRatio' => $demandRatio,
                'current_stock' => $currentStock,
                'restockSuggestionQuantity' => $PerMonthNeedRate - $currentStock,
            ];
        }

        $productMetrics = collect($productMetrics)->sortByDesc('restockSuggestionQuantity')->values()->all();
        return view('backend.inventory.product_metrics', compact('productMetrics'));
    }



    public function adjustInventory(Request $request)
    {

        $data = $this->increaseInventory($request);

        if ($data) {
            flash(translate('Product has been updated successfully'))->success();
            return redirect()->route('inventory.index');
        }

    }

    public function increaseInventory($request)
    {

        $product = $request->input('product_id');
        $quantity = $request->input('quantity');

        $this->updateProductStock($product, $quantity);


        return true;
    }

    private function updateProductStock($productId, $quantity)
    {
        $product = Product::find($productId);
        $data = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'user_id' => Auth::user()->id,
        ];
        if ($product) {
            $product->current_stock += $quantity;
            $product->save();
            ProductPurchase::create($data);
            return true;
        }
    }




    public function updateStock(Request $request)
    {
        $product = Product::findOrFail($request['id']);
        $stocks = ProductStock::where('product_id', $product->id)->get();

        $choice_options = array();
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;

                $item['attribute_id'] = $no;

                $data = array();
                foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                    array_push($data, $eachValue->value);
                }

                $item['values'] = $data;
                array_push($choice_options, $item);
            }
        }

        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        } else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options);


        //combinations start
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach (json_decode($request[$name][0]) as $key => $item) {
                    array_push($data, $item->value);
                }
                array_push($options, $data);
            }
        }

        $combinations = Combinations::makeCombinations($options);
        $varients = [];
        $test = [];
        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $key => $item) {
                    if ($key > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }

                $varients[] = [
                    'product_id' => $product->id,
                    'sku' => $str,
                    'quantity' => $request['qty_' . str_replace('.', '_', $str)],
                    'user_id' => Auth::user()->id,
                ];

            }
        }


        foreach ($varients as $variant) {
            $productVariant = ProductStock::where('product_id', $variant['product_id'])
                ->where('sku', $variant['sku'])
                ->first();
            if ($productVariant && $variant['quantity'] != 0) {
                $newstock = $productVariant->qty + ($variant['quantity']);
                $productVariant->qty = $newstock;
                $productVariant->save();
                // dd($variant);
                ProductPurchase::create($variant);
            }
        }

        flash(translate('Product has been updated successfully'))->success();
        return redirect()->route('inventory.index');
    }

}
