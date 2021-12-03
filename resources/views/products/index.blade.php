@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.filter') }}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" value="{{ isset($title) ? $title : old('title') }}"
                           placeholder="Product Title" class="form-control">
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                </div>

                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        @foreach ($variants as $variant)
                            <optgroup label="{{ $variant->title }}">
                                @foreach($variant->productVariants as $product_variant)
                                    <option value="{{ $product_variant->variant }}" {{ isset($variant) && $product_variant->variant == $variant ? 'selected' : '' }}>{{ $product_variant->variant }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('variant') }}</span>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" value="{{ isset($price_from) ? $price_from : old('price_from') }}"
                               aria-label="First name" placeholder="From"
                               class="form-control">
                        <span class="text-danger">{{ $errors->first('price_from') }}</span>

                        <input type="text" name="price_to" aria-label="Last name"
                               value="{{ isset($price_to) ? $price_to : old('price_to') }}" placeholder="To" class="form-control">
                        <span class="text-danger">{{ $errors->first('price_to') }}</span>

                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" value="{{ isset($date) ? $date : old('date') }}"
                           class="form-control">
                    <span class="text-danger">{{ $errors->first('date') }}</span>

                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($products as $key=> $product)
                        <tr>
                            <td width="5%">{{ $key+1 }}</td>
                            <td width="10%">{{ $product->title }} <br> Created at
                                : {{ \Carbon\Carbon::parse($product->created_at)->diffForHumans() }}</td>
                            <td width="40%">{{ $product->description }}</td>
                            <td width="40%">
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                    <dt class="col-sm-3 pb-0">
                                        @foreach ($product->variantPrices as $k=> $variant_price)
                                            <p style="white-space: nowrap;margin-bottom: 8px">{{ @$variant_price->variantOne->variant }}
                                                / {{ @$variant_price->variantTwo->variant }}
                                                / {{ @$variant_price->variantThree->variant }}</p>
                                        @endforeach
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            @foreach ($product->variantPrices as $k=> $variant_price)
                                                <dt class="col-sm-4 pb-0">Price
                                                    : {{ number_format($variant_price->price,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock
                                                    : {{ number_format($variant_price->stock,2) }}</dd>
                                            @endforeach
                                        </dl>
                                    </dd>
                                </dl>
                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show
                                    more
                                </button>
                            </td>
                            <td width="5%">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing 1 to {{ $products->count() }} out of {{ $products->total() }} </p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        (function ($) {
            'use strict';
            $(document).ready(function () {
            });
        })(jQuery)
    </script>
@endpush
