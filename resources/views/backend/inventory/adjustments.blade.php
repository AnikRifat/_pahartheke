@extends('backend.layouts.app')

@section('content')
{{-- {{ dd($product) }} --}}
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="mb-0 h4">{{ translate('Adjust Product') }}</h1>
    <h1 class="mb-0 h5">{{$product->getTranslation('name')}}</h1>
</div>

@php
//    dd($product->stocks);
$qty = 0;
if ($product->variant_product) {
    foreach ($product->stocks as $key => $stock) {
        echo $stock->variant . ' - ' . $stock->qty . '<br>';
    }
} else {
    echo $product->current_stock;
}
@endphp
@if ($product->variant_product)
<div class="col-lg-8 mx-auto">
    <form class="form form-horizontal mar-top" action="{{ route('update.stock', $product->id) }}" method="POST"
        enctype="multipart/form-data" id="choice_form">
        <input name="_method" type="hidden" value="POST">
        <input type="hidden" name="id" value="{{ $product->id }}">
        @csrf

        @foreach (json_decode($product->choice_options) as $key => $choice_option)
        <div class="form-group row d-none">
            <div class="col-lg-3">
                <input type="hidden" name="choice_no[]" value="{{ $choice_option->attribute_id }}">
                <input type="text" class="form-control" name="choice[]"
                value="{{ \App\Attribute::find($choice_option->attribute_id)->getTranslation('name') }}"
                placeholder="{{ translate('Choice Title') }}" disabled>
            </div>
            <div class="col-lg-8">
                <input type="text" class="form-control aiz-tag-input"
                name="choice_options_{{ $choice_option->attribute_id }}[]"
                placeholder="{{ translate('Enter choice values') }}"
                value="{{ implode(',', $choice_option->values) }}" data-on-change="update_sku">
            </div>
        </div>
        @endforeach
        <div class="sku_combination" id="sku_combination">

        </div>
        <div class="mb-3 text-right">
            <button type="submit" name="button" class="btn btn-info">{{ translate('Update Product') }}</button>
        </div>
    </form>
</div>
@else

<form action="{{ route('adjust_inventory') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input  type="number" step="0.1" value="0" name="quantity" class="form-control"
    style="text-align: center;max-width: 100px!important;"
    value="1" >
    <button type="submit" class="btn btn-primary">
        {{ translate('Adjust') }}
    </button>
</form>
@endif

@endsection

@section('script')
<script type="text/javascript">
    function add_more_customer_choice_option(i, name) {
        $('#customer_choice_options').append(
        '<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' +
            i + '"><input type="text" class="form-control" name="choice[]" value="' + name +
            '" placeholder="{{ translate('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control aiz-tag-input" name="choice_options_' +
                i +
                '[]" placeholder="{{ translate('Enter choice values') }}" data-on-change="update_sku"></div></div>');

                AIZ.plugins.tagify();
            }
            function update_sku() {
                $.ajax({
                    type: "POST",
                    url: '{{ route('products.sku_combination_adjust') }}',
                    data: $('#choice_form').serialize(),
                    success: function(data) {
                        $('#sku_combination').html(data);
                        if (data.length > 1) {
                            $('#quantity').hide();
                        } else {
                            $('#quantity').show();
                        }
                    }
                });
            }

            function delete_row(em) {
                $(em).closest('.form-group').remove();
                update_sku();
            }

            function delete_variant(em) {
                $(em).closest('.variant').remove();
            }



            AIZ.plugins.tagify();

            $(document).ready(function() {
                update_sku();

                $('.remove-files').on('click', function() {
                    $(this).parents(".col-md-4").remove();
                });
            });

            $('#choice_attributes').on('change', function() {
                $.each($("#choice_attributes option:selected"), function(j, attribute) {
                    flag = false;
                    $('input[name="choice_no[]"]').each(function(i, choice_no) {
                        if ($(attribute).val() == $(choice_no).val()) {
                            flag = true;
                        }
                    });
                    if (!flag) {
                        add_more_customer_choice_option($(attribute).val(), $(attribute).text());
                    }
                });

                var str = @php echo $product->attributes @endphp;

                $.each(str, function(index, value) {
                    flag = false;
                    $.each($("#choice_attributes option:selected"), function(j, attribute) {
                        if (value == $(attribute).val()) {
                            flag = true;
                        }
                    });
                    if (!flag) {
                        $('input[name="choice_no[]"][value="' + value + '"]').parent().parent().remove();
                    }
                });

                update_sku();
            });
        </script>
        @endsection
