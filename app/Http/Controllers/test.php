<?php

public function update(Request $request, $id)
{
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
    $product = Product::findOrFail($id);
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id;
    $product->current_stock = $request->current_stock;
    $product->barcode = $request->barcode;
    if ($product->barcode == null) {
        $product->barcode = rand(100000, 999999) . date('mis');
    }

    if ($request->show_stock != null) {
        $product->show_stock = 1;
    } else {
        $product->show_stock = 0;
    }

    if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
        if ($request->refundable != null) {
            $product->refundable = 1;
        } else {
            $product->refundable = 0;
        }
    }

    if ($request->lang == env("DEFAULT_LANGUAGE")) {
        $product->name = $request->name;
        $product->unit = $request->unit;
        $product->description = $request->description;
        $product->slug = strtolower($request->slug);
    }

    $product->photos = $request->photos;
    $product->thumbnail_img = $request->thumbnail_img;
    $product->min_qty = $request->min_qty;

    $tags = array();
    if ($request->tags[0] != null) {
        foreach (json_decode($request->tags[0]) as $key => $tag) {
            array_push($tags, $tag->value);
        }
    }
    $product->tags = implode(',', $tags);

    $product->video_provider = $request->video_provider;
    $product->video_link = $request->video_link;
    $product->unit_price = $request->unit_price;
    $product->purchase_price = $request->purchase_price;
    $product->tax = $request->tax;
    $product->tax_type = $request->tax_type;
    $product->discount = $request->discount;
    $product->shipping_type = $request->shipping_type;
    if ($request->has('shipping_type')) {
        if ($request->shipping_type == 'free') {
            $product->shipping_cost = 0;
        } elseif ($request->shipping_type == 'flat_rate') {
            $product->shipping_cost = $request->flat_shipping_cost;
        }
    }
    $product->discount_type = $request->discount_type;
    $product->meta_title = $request->meta_title;
    $product->meta_description = $request->meta_description;
    $product->meta_img = $request->meta_img;

    if ($product->meta_title == null) {
        $product->meta_title = $product->name;
    }

    if ($product->meta_description == null) {
        $product->meta_description = $product->description;
    }
    $product->pdf = $request->pdf;

    if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
        $product->colors = json_encode($request->colors);
    } else {
        $colors = array();
        $product->colors = json_encode($colors);
    }

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

    foreach ($product->stocks as $key => $stock) {
        $stock->delete();
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

            $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
            if ($product_stock == null) {
                $product_stock = new ProductStock;
                $product_stock->product_id = $product->id;
            }

            $product_stock->variant = $str;
            // $product_stock->purchase_price = $request['purchase_price_' . str_replace('.', '_', $str)];
            $product_stock->price = $request['price_' . str_replace('.', '_', $str)];
            $product_stock->sku = $request['sku_' . str_replace('.', '_', $str)];
            $product_stock->qty = $request['qty_' . str_replace('.', '_', $str)];
            $product_stock->save();
        }
    } else {
        $product_stock = new ProductStock;
        $product_stock->product_id = $product->id;
        $product_stock->price = $request->unit_price;
        $product_stock->qty = $request->current_stock;
        
        $product_stock->save();
    }

    $product->save();

    // Product Translations
    $product_translation = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
    $product_translation->name = $request->name;
    $product_translation->unit = $request->unit;
    $product_translation->description = $request->description;
    $product_translation->save();

    flash(translate('Product has been updated successfully'))->success();

    Artisan::call('view:clear');
    Artisan::call('cache:clear');

    return back();
}
?>