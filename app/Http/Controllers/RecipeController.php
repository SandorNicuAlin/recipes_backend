<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Recipe;
use App\Repositories\RecipeRepository;
use App\Services\FormValidation;
use App\Services\CompareArrays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['recipes' => Recipe::whereIn('group_id', $request->get('groups'))->get()], 200);
    }

    public function getAvailable(Request $request): \Illuminate\Http\JsonResponse
    {
        $available_recipes = [];
        // get all the recipes available for the group that the user is part of
        $recipes = Recipe::whereIn('group_id', $request->get('groups'))->get();
        Log::alert($recipes);
        // get all the products in stock
        $products_stock = ProductStock::all()->map(function($product) {return ['name' => Product::where('id', $product['product_id'])->get('name')[0]['name'], 'quantity' => $product['quantity']];})->toArray();

        foreach($recipes as $recipe) {
            // get a recipe and compare its ingredients to the products in stock and if they are available in stock add it to the available_recipes array
            if(CompareArrays::compare_name_and_quantity(Ingredient::where('recipe_id', $recipe['id'])->get()->toArray(), $products_stock)) {
                $available_recipes[] = $recipe;
            }
        }
        return response()->json(['recipes' => $available_recipes], 200);
    }

    public function add(Request $request): \Illuminate\Http\JsonResponse
    {
//        error_log($request->get('description'));
        // check if the group exist
        if(!Group::where('id', $request->get('group_id'))->exists()) {
            return response()->json(['success' => false, 'error' => 'This group no longer exist'], 400);
        }
        // check if the logged-in user is an administrator of this group
        if(GroupUser::where('group_id', $request->get('group_id'))->where('user_id', $request->user()['id'])->first()->is_administrator === 0) {
            return response()->json(['success' => false, 'error' => 'You are not an administrator of this group'], 400);
        }

        if(count($request->get('recipe_steps')) === 0) {
            return response()->json(['success' => false, 'error' => 'Please add some steps to your recipe'], 400);
        }

        // input validation -> name, description, recipe_steps[i].name, recipe_steps[i].description
        $validator = FormValidation::validate(
            $request,
            [
                "name" => "required|max:30|min:2",
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }
        // create recipe with its recipe-steps
        RecipeRepository::createRecipeWithSteps($request->get('name'), $request->get('description'), $request->get('recipe_steps'), $request->get('group_id'));

        return response()->json(['success' => true], 200);
    }
}
