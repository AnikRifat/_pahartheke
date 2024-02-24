<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DiscountRule;
use Illuminate\Validation\Rule;

class DiscountRuleController extends Controller
{
    public function index(Request $request)
    {
        $discountRules = DiscountRule::orderBy('order_by', 'desc')->get();
        return view('backend.discount_rule.index', compact('discountRules'));
    }

    public function create(){
        return view('backend.discount_rule.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required',
            'type'              => 'required',
            'discount_amount'   => Rule::requiredIf(function () use ($request) {
                return $request->type != 1;
            }),
            'condition_key'     => 'required',
            'conditon_oprator'  => 'required',
            'conditon_value'    => 'required',
            'expire_date'       => 'nullable',

        ]);

        $rule = new DiscountRule();
        $rule->name             = $request->name;
        $rule->type             = $request->type;
        $rule->discount_amount  = $request->discount_amount;
        $rule->condition_key    = $request->condition_key;
        $rule->conditon_oprator = $request->conditon_oprator;
        $rule->conditon_value   = $request->conditon_value;
        $rule->expire_date      = $request->expire_date;
        $rule->status           = $request->status ? 1 : 0;
        $rule->order_by           = 1;
        $rule->save();

        flash(translate('Discount Rule Created Successfully'))->success();
        return redirect()->route('discount.index');
    }

    public function edit(Request $request, $id)
    {
        $discount_rule = DiscountRule::findOrFail($id);
        return view('backend.discount_rule.edit', compact('discount_rule'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'              => 'required',
            'type'              => 'required',
            'discount_amount'   => Rule::requiredIf(function () use ($request) {
                return $request->type != 1;
            }),
            'condition_key'     => 'required',
            'conditon_oprator'  => 'required',
            'conditon_value'    => 'required',
            'expire_date'       => 'nullable',
        ]);

        $rule = DiscountRule::findOrFail($id);
        $rule->name             = $request->name;
        $rule->type             = $request->type;
        $rule->discount_amount  = $request->discount_amount;
        $rule->condition_key    = $request->condition_key;
        $rule->conditon_oprator = $request->conditon_oprator;
        $rule->conditon_value   = $request->conditon_value;
        $rule->expire_date      = $request->expire_date;
        $rule->status           = $request->status ? 1 : 0;
        $rule->order_by           = 1;
        $rule->update();

        flash(translate('Discount Rule Updated Successfully'))->success();
        return redirect()->route('discount.index');
    }

    public function delete(Request $request, $id)
    {
        $rule = DiscountRule::findOrFail($id)->delete();
        flash(translate('Discount Rule Deleted Successfully'))->success();
        return redirect()->back();
    }
}
