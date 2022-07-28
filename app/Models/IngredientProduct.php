<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientProduct extends Model
{
    use HasFactory;

    protected $table = 'ingredient_products';
    protected $guarded = [];

    public function ingredient() {
        return $this->belongsTo(Ingredient::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
