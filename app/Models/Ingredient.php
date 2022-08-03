<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    protected $guarded = [];

    public function products() {
        return $this->belongsToMany(Product::class);
    }

    public function recipe() {
        return $this->belongsToMany(Recipe::class);
    }
}
