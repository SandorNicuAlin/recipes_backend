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
}
