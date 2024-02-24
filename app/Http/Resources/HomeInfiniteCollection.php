<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HomeInfiniteCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->getTranslation('name'),
                    'photos' => explode(',', $data->photos),
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'base_price' => home_base_price($data->id),
                    'base_discounted_price' => home_discounted_base_price($data->id),
                    'todays_deal' => (integer) $data->todays_deal,
                    'featured' =>(integer) $data->featured,
                    'unit' => $data->unit,
                    'discount' => (double) $data->discount,
                    'discount_type' => $data->discount_type,
                    'category_id' => $data->category->id,
                    'variant_product' => $data->variant_product,
                    'category' => $data->category->name,
                    'link' => route('product', $data->slug)
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
