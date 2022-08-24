<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockRepository;
use App\Services\FormValidation;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    public function show(): \Illuminate\Http\JsonResponse
    {
        $stock = ProductStock::all();
        foreach ($stock as $product_stock) {
            $product_stock['product'] = Product::where('id', $product_stock['product_id'])->first();
        }
        return response()->json(['product_stock' => $stock], 200);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        // input validation -> quantity, product['name'], product['um']
        $validator = FormValidation::validate(
            $request,
            [
                "quantity" => "required|numeric|lt:10000|gt:0",
                "product.name" => "required|max:30",
                "product.um" => "required|max:10",
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }
        // get the product, create if it does not exist
        $product = ProductRepository::createProduct($request->get('product')['name'], $request->get('product')['um']);

        // add product in stock
        ProductStockRepository::createProductStock($request->get('quantity'), $product['id']);

        return response()->json(['success' => true], 200);
    }
}
