<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientRecipeStep extends Model
{
    use HasFactory;
    protected $table = 'recipe_step_ingredients';
    protected $guarded = [];

}
