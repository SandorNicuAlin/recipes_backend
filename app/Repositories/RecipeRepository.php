<?php

namespace App\Repositories;


use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Ingredient;
use App\Models\Product;

class RecipeRepository
{
    public static function createRecipeWithSteps(string $name, string $description, array $steps, string $group_id): void
    {
        // create recipe
        $recipe = Recipe::create(['name' => $name, 'description' => $description, 'group_id' => $group_id])->fresh();

        foreach($steps as $step) {
            // create recipe_steps
            $recipe_step = RecipeStep::create(['name' => $step['name'], 'description' => $step['description'], 'order' => $step['order'], 'recipe_id' => $recipe['id']]);
            // create ingredients, skip if step does not have ingredients
            if(!$step['ingredients']) {
                continue;
            }
            foreach($step['ingredients'] as $ingredient) {
                // get the product, create if it does not exist
                $product = ProductRepository::createProduct($ingredient['name'], $ingredient['um']);
                // create ingredient
                $ingredient_eloquent = Ingredient::create(['name' => $product['name'], 'quantity' => $ingredient['quantity'], 'um' => $product['um'], 'recipe_id' => $recipe['id']]);
                // create many to many connection between ingredient and product
                $product->ingredients()->attach($ingredient_eloquent->fresh(), ['created_at' => now(), 'updated_at' => now()]);
//                $ingredient->products()->attach($product);
                // create many to many connection between recipe_step and ingredient
//                $ingredient_eloquent->recipe_steps()->attach($recipe_step->fresh());
                $recipe_step->ingredients()->attach($ingredient_eloquent->fresh(), ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
