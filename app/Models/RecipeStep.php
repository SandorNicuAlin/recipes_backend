<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model
{
    use HasFactory;

    protected $table = 'recipe_steps';
    protected $guarded = [];

    public function recipe() {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients() {
        return $this->belongsToMany(Ingredient::class);
    }
}
