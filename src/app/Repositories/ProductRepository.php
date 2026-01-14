<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findBySku(string $sku)
    {
        return Product::where('sku', $sku)->first();
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        $product = Product::findOrFail($productId);
        $product->stock -= $quantity;
        return $product->save();
    }
}