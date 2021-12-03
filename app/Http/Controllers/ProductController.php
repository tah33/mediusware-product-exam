<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $data = [
            'products' => Product::with('variantPrices')->latest()->paginate(2),
            'variants' => Variant::with('productVariants')->get()
        ];
        return view('products.index',$data);
    }

    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    public function store(Request $request)
    {

    }

    public function show($product)
    {

    }

    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    public function update(Request $request, Product $product)
    {
        //
    }

    public function destroy(Product $product)
    {
        //
    }

    public function filter(Request $request)
    {
        $request->validate([
            'title' => 'required_without_all:variant,price_from,price_to,date',
            'variant' => 'required_without_all:title,price_from,price_to,date',
            'price_from' => 'required_without_all:title,variant,price_to,date',
            'price_to' => 'required_without_all:title,variant,price_from,date',
            'date' => 'required_without_all:title,variant,price_from,price_to',
        ]);
        $products = Product::query();
        $price_from = $price_to =  0;
        if ($request->title) {
            $products->where('title','LIKE','%'.$request->title.'%');
        }

        if ($request->variant) {
            $products->whereHas('variants',function ($query) use ($request){
                $query->where('variant',$request->variant);
            });
        }
        if ($request->price_from) {
            $price_from = $request->price_from;
        }
        if ($request->price_to) {
            $price_to = $request->price_to;
        }
        if ($request->price_from || $request->price_to) {
            $products->whereHas('variantPrices',function ($query) use ($price_from,$price_to)
            {
                $query->whereBetween('price',[$price_from,$price_to]);
            });
        }

        if ($request->date) {
            $products->whereDate('created_at',Carbon::parse($request->date));
        }
        $data = [
            'products' => $products->with('variantPrices')->latest()->paginate(2),
            'variants' => Variant::with('productVariants')->get(),
            'title' => $request->title,
            'variant' => $request->variant,
            'price_from' => $request->price_from,
            'price_to' => $request->price_to,
            'date' => $request->date,
        ];

        return view('products.index',$data);
    }
}
