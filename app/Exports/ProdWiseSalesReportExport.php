<?php
namespace App\Exports;

use App\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProdWiseSalesReportExport implements FromView
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $orders;
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function view(): View
    {
        return view('exports.prod_wise_sales', [
            'orders' => $this->orders
        ]);
    }
}
