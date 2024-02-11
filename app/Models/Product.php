<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "category_id",
        "description",
        "price",
        "is_favorite",
        "status",
        "stock",
        "image"
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
