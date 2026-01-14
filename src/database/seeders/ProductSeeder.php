<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'category_id' => 1,
            'sku' => 'BRG-001',
            'name' => 'Roti Cokelat Spesial',
            'stock' => 50,
            'price' => 15000,
            'min_stock_threshold' => 10
        ]);

        Product::create([
            'category_id' => 2,
            'sku' => 'BRG-002',
            'name' => 'Kopi Susu Gula Aren',
            'stock' => 100,
            'price' => 18000,
            'min_stock_threshold' => 15
        ]);

        Product::create([
            'category_id' => 3,
            'sku' => 'BRG-003',
            'name' => 'Sabun Cuci Tangan 250ml',
            'stock' => 5,
            'price' => 12000,
            'min_stock_threshold' => 5
        ]);
    }
}