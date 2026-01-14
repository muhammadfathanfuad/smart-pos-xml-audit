<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['category_id', 'sku', 'name', 'stock', 'price', 'min_stock_threshold'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}