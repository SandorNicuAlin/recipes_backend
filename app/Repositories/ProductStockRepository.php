<?php

namespace App\Repositories;

use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class ProductStockRepository
{
    public static function createProductStock($quantity, $product_id) : void
    {
        // check if the product is already in stock
        if(ProductStock::where('product_id', $product_id)->exists())
        {
            // add to quantity
            ProductStock::where('product_id', $product_id)->update([
                'quantity' => ProductStock::where('product_id', $product_id)->first()->quantity + $quantity
            ]);
        } else {
            DB::table('product_stocks')->insert([
                'quantity' => $quantity,
                'product_id' => $product_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public static function updateQuantity($value, $product_stock_id) : void
    {
        ProductStock::find($product_stock_id)->update(['quantity' => $value]);
    }

    public static function incrementQuantity($product_stock_id) : void
    {
        ProductStock::find($product_stock_id)->increment('quantity');
    }

    public static function decrementQuantity($product_stock_id) : void
    {
        $product_stock = ProductStock::find($product_stock_id);
        $product_stock->decrement('quantity');
        if($product_stock->quantity <= 0){
            $product_stock->delete();
        }
    }

    public static function deleteProductStock($product_stock_id) : void
    {
        ProductStock::find($product_stock_id)->delete();
    }
}
