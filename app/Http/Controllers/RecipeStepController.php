<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use Illuminate\Http\Request;

class RecipeStepController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['recipe_steps' => RecipeStep::where('recipe_id', $request->get('recipe_id'))->orderBy('order', 'ASC')->get()], 200);
    }
}
