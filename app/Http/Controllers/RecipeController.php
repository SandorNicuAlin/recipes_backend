<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['recipes' => Recipe::whereIn('group_id', $request->get('groups'))->get()], 200);
    }
}
