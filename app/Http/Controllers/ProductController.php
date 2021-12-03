<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $data = [
            'products' => Product::with('variantPrices')->latest()->paginate(2),
            'variants' => Variant::with('productVariants')->get()
        ];
        return view('products.index', $data);
    }

    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'title' => 'required',
                'sku' => 'required',
            ]);
            $product = Product::create($request->all());
            $variant_1 = $variant_2 = $variant_3 = null;
            $variants = [];
            foreach ($request->product_variant as $key => $variant) {
                foreach ($variant['tags'] as $k => $tag) {
                    $variants[] = [
                        'variant' => $tag,
                        'variant_id' => $variant['option'],
                        'product_id' => $product->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (count($variants) > 0) {
                ProductVariant::insert($variants);
            }
            $images = [];
            foreach ($request->product_image as $key => $image) {
                $images[$key] = [
                    'product_id' => $product->id,
                    'file_path' => $image,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (count($images) > 0) {
                ProductImage::insert($images);
            }
            $prices = [];
            foreach ($request->product_variant_prices as $key => $product_variant_price) {
                $variants = explode('/', $product_variant_price['title']);
                foreach ($variants as $variant) {
                    $product_variant = ProductVariant::where('variant', $variant)->where('product_id',$product->id)->first();
                    if (@$product_variant->variant_id == 1) {
                        $variant_1 = $product_variant->id;
                    }
                    if (@$product_variant->variant_id == 2) {
                        $variant_2 = $product_variant->id;
                    }
                    if (@$product_variant->variant_id == 6) {
                        $variant_3 = $product_variant->id;
                    }
                }
                $prices[$key] = [
                    'product_variant_one' => $variant_1,
                    'product_variant_two' => $variant_2,
                    'product_variant_three' => $variant_3,
                    'product_id' => $product->id,
                    'stock' => $product_variant_price['stock'],
                    'price' => $product_variant_price['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (count($prices) > 0) {
                ProductVariantPrice::insert($prices);
            }
            DB::commit();
            return response()->json([
                'success' => 'Product Create Successfully'
            ]);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json([
                'error' => 'Something Went Wrong'
            ]);
        }
    }

    public function show($product)
    {

    }

    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants','product'));
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
        $price_from = $price_to = 0;
        if ($request->title) {
            $products->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if ($request->variant) {
            $products->whereHas('variants', function ($query) use ($request) {
                $query->where('variant', $request->variant);
            });
        }
        if ($request->price_from) {
            $price_from = $request->price_from;
        }
        if ($request->price_to) {
            $price_to = $request->price_to;
        }
        if ($request->price_from || $request->price_to) {
            $products->whereHas('variantPrices', function ($query) use ($price_from, $price_to) {
                $query->whereBetween('price', [$price_from, $price_to]);
            });
        }

        if ($request->date) {
            $products->whereDate('created_at', Carbon::parse($request->date));
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

        return view('products.index', $data);
    }

    public function images(Request $request)
    {
        $file = $request->file;
        $name = 'product-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move('uploads/products/', $name);
        return 'public/uploads/document/' . $name;
    }
}
