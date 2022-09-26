<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Recipe;
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
                "quantity" => "required|numeric|gt:0|lt:10000",
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

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {

        $product_stock_id = $request->get('product_stock_id');

        // check if the quantity is between 0 and 10000
        if($request->get('value') <= 0 || $request->get('value') >= 10000) {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 400);
        }

        // check if the product is not in stock
        if(!ProductStock::where('id', $product_stock_id)->exists())
        {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 400);
        }

        ProductStockRepository::updateQuantity($request->get('value'), $product_stock_id);

        return response()->json(['success' => true], 200);
    }

    public function incrementDecrementQuantity(Request $request): \Illuminate\Http\JsonResponse
    {
        $product_stock_id = $request->get('product_stock_id');

        // check if the product is not in stock
        if(!ProductStock::where('id', $product_stock_id)->exists())
        {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 400);
        }

        // check if incrementing or decrementing
        if($request->get('is_incrementing')) {
            ProductStockRepository::incrementQuantity($product_stock_id);
            if(ProductStock::find($product_stock_id)->quantity > 10000) {
                return response()->json(['success' => false, 'error' => 'Quantity can have a value of maximum 10000'], 400);
            }
        } else {
            ProductStockRepository::decrementQuantity($product_stock_id);
        }

        return response()->json(['success' => true], 200);
    }

    public function remove(Request $request): \Illuminate\Http\JsonResponse
    {
        $product_stock_id = $request->get('product_stock_id');

        // check if the product is not in stock
        if(!ProductStock::where('id', $product_stock_id)->exists())
        {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 400);
        }

        ProductStockRepository::deleteProductStock($product_stock_id);

        return response()->json(['success' => true], 200);
    }

    public function removeForRecipe(Request $request): \Illuminate\Http\JsonResponse
    {
        $recipe_id = $request->get('recipe_id');
        // check if the recipe does exist
        if(!Recipe::where('id', $request->get('recipe_id'))->exists()) {
            return response()->json(['success' => false, 'error' => 'This recipe does not exist']);
        }

        foreach(Ingredient::where('recipe_id', $recipe_id)->get() as $ingredient) {
            $product_stock = ProductStock::where('product_id', IngredientProduct::where('ingredient_id', $ingredient['id'])->first()->product_id);
            // check if the product exists in stock or the quantity is grater than the quantity required by the recipe (if not, the recipe is not available)
            if(!$product_stock->exists() || $product_stock->first()->quantity < $ingredient['quantity']) {
                return response()->json(['success' => false, 'error' => 'This recipe is not available']);
            }
            // check if the product quantity is equal or grater than the quantity required by the recipe
            ProductStockRepository::deleteOrDecreaseQuantity($product_stock, $ingredient);
        }
        return response()->json(['success' => true], 200);
    }
}
