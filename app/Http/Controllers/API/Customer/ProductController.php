<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
     public function index(Request $request)
    {
        $products = Product::with(['restaurant', 'images', 'variants'])->latest()->paginate(10);

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Product::with(['restaurant', 'images', 'variants'])->findOrFail($id);

        return new ProductResource($product);
    }
}
