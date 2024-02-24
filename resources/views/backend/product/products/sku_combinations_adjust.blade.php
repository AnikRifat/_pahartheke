@if(count($combinations[0]) > 0)
    <table class="table table-bordered">
        <thead>
            <tr>

                <td class="text-center">
                    <label for="" class="control-label">{{translate('Varient')}}</label>
                </td>
                <td class="text-center">
                    <label for="" class="control-label">{{translate('Quantity')}}</label>
                </td>


            </tr>
        </thead>
        <tbody>
            @foreach ($combinations as $key => $combination)
                @php
                    $sku = '';
                    foreach (explode(' ', $product_name) as $key => $value) {
                        $sku .= substr($value, 0, 1);
                    }

                    $str = '';
                    foreach ($combination as $key => $item){
                        if($key > 0 ){
                            $str .= '-'.str_replace(' ', '', $item);
                            $sku .='-'.str_replace(' ', '', $item);
                        }
                        else{
                            if($colors_active == 1){
                                $color_name = \App\Color::where('code', $item)->first()->name;
                                $str .= $color_name;
                                $sku .='-'.$color_name;
                            }
                            else{
                                $str .= str_replace(' ', '', $item);
                                $sku .='-'.str_replace(' ', '', $item);
                            }
                        }
                    }
                @endphp
                @if(strlen($str) > 0)
                    <tr class="variant">
                        <td>
                            <label for="" class="control-label">{{ $str }}</label>
                        </td>


                        <td>
                            <input type="number" step="0.1" lang="en" name="qty_{{ $str }}" value="0"  class="form-control" required>
                        </td>

                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endif
