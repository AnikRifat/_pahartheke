<?php
namespace App\Exports;

use App\Subscriber;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SubscribersExport implements FromView
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($subscribers)
    {
        $this->subscribers = $subscribers;
    }

    public function view(): View
    {
        return view('exports.subscribers', [
            'subscribers' => $this->subscribers
        ]);
    }
}
