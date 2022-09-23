<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Services\WorkWithArrays;

class RecipeStepController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $recipe_id = $request->get('recipe_id');
        return response()->json([
            'recipe_steps' => RecipeStep::where('recipe_id', $recipe_id)->orderBy('order', 'ASC')->get(),
            'ingredients' => WorkWithArrays::merge_quantities_for_duplicate_name(
                Ingredient::where('recipe_id', $recipe_id)->get()->toArray()
            ),
        ], 200);
    }
}
