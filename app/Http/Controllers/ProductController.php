<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        // filter the products by text input
        return response()->json(['products' => Product::where('name', 'LIKE', '%'.$request->get('filter_text').'%')->get()], 200);
    }
}
