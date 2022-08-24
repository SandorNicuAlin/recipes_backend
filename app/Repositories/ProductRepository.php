<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public static function createProduct($name, $um) : Product
    {
        // check if the product already exist
        if(Product::where('name', $name)->where('um', $um)->exists())
        {
            return Product::where('name', $name)->where('um', $um)->first();
        }

        return Product::create([
            'name' => $name,
            'um' => $um,
        ])->fresh();
    }
}
