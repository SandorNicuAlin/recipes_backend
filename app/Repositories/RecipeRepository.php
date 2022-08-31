<?php

namespace App\Repositories;


use App\Models\Recipe;
use App\Models\RecipeStep;

class RecipeRepository
{
    public static function createRecipeWithSteps(string $name, string $description, array $steps, string $group_id) {
        // create recipe
        $recipe = Recipe::create(['name' => $name, 'description' => $description, 'group_id' => $group_id])->fresh();
        // create recipe_steps
        foreach($steps as $step) {
            RecipeStep::create(['name' => $step['name'], 'description' => $step['description'], 'order' => $step['order'], 'recipe_id' => $recipe['id']]);
        }
    }
}
