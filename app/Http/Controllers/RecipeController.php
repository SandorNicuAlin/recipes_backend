<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Recipe;
use App\Repositories\RecipeRepository;
use App\Services\FormValidation;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['recipes' => Recipe::whereIn('group_id', $request->get('groups'))->get()], 200);
    }

    public function add(Request $request): \Illuminate\Http\JsonResponse
    {
        // check if the group exist
        if(!Group::where('id', $request->get('group_id'))->exists()) {
            return response()->json(['success' => false, 'error' => 'This group no longer exist'], 400);
        }
        // check if the logged-in user is an administrator of this group
        if(GroupUser::where('group_id', $request->get('group_id'))->where('user_id', $request->user()['id'])->first()->is_administrator === 0) {
            return response()->json(['success' => false, 'error' => 'You are not an administrator of this group'], 400);
        }

        // input validation -> name, description, recipe_steps[i].name, recipe_steps[i].description
        $validator = FormValidation::validate(
            $request,
            [
                "name" => "required|max:15",
                "recipe_steps.*.name" => "required|max:25",
                "recipe_steps.*.description" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        // create recipe with its recipe-steps
        RecipeRepository::createRecipeWithSteps($request->get('name'), $request->get('description'), $request->get('recipe_steps'), $request->get('group_id'));

        return response()->jsoN(['success' => true], 200);
    }
}
