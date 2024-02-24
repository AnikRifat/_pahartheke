<?php
namespace App\Services;

use App\Category;
use App\City;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CrmService
{
    public function getAllCities()
    {
        // $data = Order::with('orderDetails')->with('user')->get();
        // $allCities = [];

        // foreach ($data as $order) {
        //     $shippingAddress = json_decode($order->shipping_address);
        //     if (isset($shippingAddress->city) && !empty($shippingAddress->city) && !is_null($shippingAddress->city)) {
        //         $city = $shippingAddress->city;
        //         $allCities[] = $city;
        //     }
        // }
        return City::all()->pluck('name');
    }

    public function getCustomersByCityAndCategoryAndDate($city, $categoryId, $date = null)
    {
        if ($categoryId != 0) {
            $customers1 = $this->getCustomersByCategory($categoryId);
        } else {
            $customers1 = User::with('orders')->get();
        }
        if ($city != "0") {
            $customers2 = $this->getCustomersByCity($city)->get();
        } else {
            $customers2 = User::with('orders')->get();
        }

        if ($date != "0" && $date != null) {
            $customers3 = $this->getCustomersByDate($date)->get();
        } else {
            $customers3 = User::with('orders')->get();
        }

        $customers = $customers1->intersect($customers2)->intersect($customers3)->unique('id')->values();
        $customers = $customers->sortByDesc(function ($customer) {
            return $customer->orders->sum('grand_total');
        });
        return $customers;
    }


    public function getCustomerOrdersByFilter($orders, $city, $categoryId, $date = 0)
    {

        $category = Category::find($categoryId);
        $categoryOrders = $category ? $this->orderByCategory($category)->get() : Order::where('cancelled', 0)->where('payment_status', 'paid')->get();
        $cityOrders = $category ? $this->getOrdersByCity($city) : Order::where('cancelled', 0)->where('payment_status', 'paid')->get();
        $sortedOrders = $categoryOrders->merge($cityOrders)->unique('id')->sortByDesc('grand_total');
        $customerOrders = [];
        if ($date != 0) {
            foreach ($sortedOrders as $order) {
                if ($order->created_at >= date('Y-m-d', strtotime(explode(" to ", $date)[0])) && $order->created_at <= date('Y-m-d', strtotime(explode(" to ", $date)[1]))) {
                    $customerOrders[] = $order;
                }

            }
        }
        return $customerOrders;
    }


    public function getOrdersByFilter($city, $productId,$categoryId, $date = null)
    {
        $orders = Order::query()->where('payment_status', 'paid')->where('cancelled', 0);

        $orders->when($date, function ($query) use ($date) {
            $dates = explode(" to ", $date);
            $query->whereDate('created_at', '>=', Carbon::parse($dates[0]))
                ->whereDate('created_at', '<=', Carbon::parse($dates[1]));
        });

        $orders->when($city != '0', function ($query) use ($city) {
            $query->whereHas('orderDetails', function ($query) use ($city) {
                $query->where('shipping_address', 'like', '%' . $city . '%');
            });
        });

        $orders->when($productId != 0, function ($query) use ($productId) {
            $query->whereHas('orderDetails', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            });
        });


        $orders->when($categoryId != 0, function ($query) use ($categoryId) {
            $query->whereHas('orderDetails.product.category', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            });
        });

        $orders = $orders->with('user', 'orderDetails')->get();

        $orderSummary = [
            'total_sale' => $orders->sum('grand_total'),
            'discount' => $orders->sum('total_discount'),
            'total_orders' => $orders->count(),
            'total_discount' => $orders->sum('coupon_discount'),
            'items' => $orders->pluck('orderDetails')->flatten()->count(),
            'shipping' => $orders->pluck('orderDetails')->flatten()->sum('shipping_cost'),
            'profit' => $orders->pluck('orderDetails')->flatten()->sum('profit'),
        ];

        $customerOrdersSummary = [];

        foreach ($orders as $order) {

            if ($order->user) {
                $customerId = $order->user->id;

                foreach ($order->orderDetails as $ods) {
                    if ($productId != 0) {
                        if ($ods->product_id == $productId) {
                            $totalOrders = ($customerOrdersSummary[$customerId]['total_orders'] ?? 0) + 1;
                            $totalPurchased = ($customerOrdersSummary[$customerId]['total_purchase_amount'] ?? 0) + $ods->price;
                            $totalDiscount = ($customerOrdersSummary[$customerId]['total_discount'] ?? 0) + $ods->discount;
                        }

                    } else {
                        $totalOrders = ($customerOrdersSummary[$customerId]['total_orders'] ?? 0) + 1;
                        $totalPurchased = ($customerOrdersSummary[$customerId]['total_purchase_amount'] ?? 0) + $ods->price;
                        $totalDiscount = ($customerOrdersSummary[$customerId]['total_discount'] ?? 0) + $ods->discount;
                    }


                }
                $customerOrdersSummary[$customerId] = [
                    'customer_id' => $customerId,
                    'name' => $order->user->name,
                    'address' => json_decode($order->shipping_address),
                    'total_orders' => $totalOrders,
                    'total_purchase_amount' => $totalPurchased,
                    'total_discount' => $totalDiscount,
                    'order_ids' => array_merge($customerOrdersSummary[$customerId]['order_ids'] ?? [], [$order->id]),
                ];
            }


        }


        $summery = [
            'customer_summary' => $customerOrdersSummary,
            'orders_summery' => $orderSummary,
        ];
        return $summery;
        // dd($summery);
    }

    private function getFilteredCustomers($city, $categoryId, $date)
    {
        $customers = User::with('orders');

        if ($categoryId != 0) {
            $customers->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            });
        }

        if ($city != "0") {
            $customers->where('city', $city);
        }

        if ($date != "0" && $date != null) {
            $customers->whereHas('orders', function ($query) use ($date) {
                $query->whereBetween('created_at', [date('Y-m-d', strtotime(explode(" to ", $date)[0])), date('Y-m-d', strtotime(explode(" to ", $date)[1]))]);
            });
        }

        return $customers->get();
    }


    public function getCustomersByCategory($categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return null;
        }

        $customers = User::whereHas('orders', function (Builder $query) use ($category) {
            $query->where('cancelled', 0)
                ->where('payment_status', 'paid')
                ->whereHas('orderDetails.product.category', function (Builder $innerQuery) use ($category) {
                    $innerQuery->where('id', $category->id);
                });
        })->get();

        return $customers;
    }

    public function getCustomersByDate($date)
    {
        // Split the date range into start and end dates


        $customers = User::whereHas('orders', function ($query) use ($date) {
            $query->where('cancelled', 0)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))
                ->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        });
        return $customers;
    }

    public function orderByCategory($category)
    {
        return Order::where('cancelled', 0)
            ->where('payment_status', 'paid')
            ->whereHas('orderDetails.product.category', function ($query) use ($category) {
                $query->where('id', $category->id);
            });
    }
    public function getCustomersByCity($city)
    {

        $customers = User::whereHas('orders', function ($query) use ($city) {
            $query->where('cancelled', 0)
                ->where('payment_status', 'paid')
                ->whereHas('orderDetails', function ($innerQuery) use ($city) {
                    $innerQuery->where('shipping_address', 'like', '%' . $city . '%');
                });
        });
        return $customers;
    }

    public function getOrdersByCity($city)
    {
        $orders = Order::with('user')->where('cancelled', 0)
            ->where('payment_status', 'paid')
            ->whereHas('orderDetails', function ($query) use ($city) {
                $query->where('shipping_address', 'like', '%' . $city . '%');
            });


        return $orders;
    }
    public function ordersGroupedByCity()
    {

        $cities = $this->getAllCities();

        foreach ($cities as $city) {
            $ordersGroupedByCity[$city] = $this->ordersByCity($city);
        }

        return $ordersGroupedByCity;
    }



    public function ordersByCity($city)
    {
        $query = Order::with('orderDetails')
            ->where('cancelled', 0)
            ->where('payment_status', 'paid');

        if ($city == 'Unlocated') {
            $query->whereDoesntHave('orderDetails', function ($query) {
                $query->where('shipping_address', 'like', '%"city":%');
            });
        } else {
            $query->whereHas('orderDetails', function ($query) use ($city) {
                $query->where('shipping_address', 'like', '%' . $city . '%');
            });
        }

        $orders = $query->get();

        $totalCustomers = $orders->pluck('user_id')->unique()->count();

        $ordersGroupedByCity = [
            'orders' => $orders,
            'totalOrders' => $orders->count(),
            'totalSaleAmount' => $orders->sum('grand_total'),
            'totalOrderQuantity' => $this->quantity($orders),
            'totalCustomers' => $totalCustomers,
        ];

        return $ordersGroupedByCity;
    }

    public function quantity($orders)
    {
        $count = 0;

        foreach ($orders as $order) {
            $count += $order->orderDetails->count();
        }
        return $count;
    }


    public function ordersGroupedByCategory()
    {
        $categories = Category::all();
        $ordersGroupedByCategory = [];

        foreach ($categories as $category) {
            $orders = Order::where('cancelled', 0)
                ->where('payment_status', 'paid')
                ->whereHas('orderDetails.product.category', function ($query) use ($category) {
                    $query->where('id', $category->id);
                })->get();

            $totalCustomers = $orders->pluck('user_id')->unique()->count();
            $totalOrders = $orders->count();
            $totalSaleAmount = $orders->sum('grand_total');

            $ordersGroupedByCategory[$category->name] = [
                'orders' => $orders,
                'totalCustomers' => $totalCustomers,
                'totalOrders' => $totalOrders,
                'totalSaleAmount' => $totalSaleAmount,
            ];
        }

        return $ordersGroupedByCategory;
    }


    public function ordersByCategory($id)
    {
        $category = Category::find($id);
        $ordersByCategory = [];

        $ordersByCategory[$category->name] = $this->orderByCategory($category)->get();


        return $ordersByCategory;
    }


}