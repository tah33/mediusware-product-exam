@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div id="app">
        <create-product :product="{{ $product }}" :variants="{{ $variants }}" :variant_prices="{{ $product->variant_price }}"
                        :product_variants="{{ $product->total_variants }}" :product_images="{{ $product->images }}">Loading</create-product>
    </div>
@endsection
