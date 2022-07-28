<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeStepIngredient extends Model
{
    use HasFactory;
    protected $table = 'recipe_step_ingredients';
    protected $guarded = [];

    public function recipe_step() {
        return $this->belongsTo(RecipeStep::class);
    }

    public function ingredient() {
        return $this->belongsTo(Ingredient::class);
    }

}
