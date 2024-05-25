<?php

namespace App\Http\Controllers;

use App\DeliveryMan;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliverymenController extends Controller
{
    public function index()
    {
        $deliveryMen = DeliveryMan::paginate(10);
        // dd($deliveryMen);
        return view('backend.deliveryman.index', compact('deliveryMen'));
    }
    public function create()
    {
        return view('backend.deliveryman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:delivery_men',
        ]);

        DeliveryMan::create($request->all());

        return redirect()->route('deliveryman.index')
            ->with('success', 'Delivery man created successfully.');
    }

    public function edit($id)
    {
        $deliveryman = DeliveryMan::find($id); 
        return view('backend.deliveryman.edit', compact('deliveryman'));
    }
    public function update(Request $request, $id)
    {
        $deliveryMan = DeliveryMan::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:delivery_men,phone,' . $deliveryMan->id,
        ]);

        $deliveryMan->update($request->all());

        return redirect()->route('deliveryman.index')
            ->with('success', 'Delivery man updated successfully.');
    }

    public function destroy(DeliveryMan $deliveryMan)
    {
        $deliveryMan->delete();

        return redirect()->route('deliveryman.index')
            ->with('success', 'Delivery man deleted successfully.');
    }

    public function assignDeliveryMan(Request $request, Order $order)
    {
        $request->validate([
            'delivery_man_id' => 'required|exists:delivery_men,id',
        ]);
        // dd($order->deliveryBoy);
        $order->delivery_man_id = $request->delivery_man_id;
        $order->save();

        return redirect()->back()->with('success', 'Delivery Man assigned successfully!');
    }
}
