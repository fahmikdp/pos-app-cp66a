<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::where('name', 'Makanan Utama')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $snack = Category::where('name', 'Snack & Gorengan')->first();
        $paket = Category::where('name', 'Paket & Add-on')->first();

        $items = [
            // Makanan Utama
            ['category_id' => $makanan->id, 'name' => 'Ayam Geprek Sambal Ijo', 'price' => 18000, 'stock_qty' => 50, 'is_available' => true],
            ['category_id' => $makanan->id, 'name' => 'Ayam Geprek Original', 'price' => 16000, 'stock_qty' => 50, 'is_available' => true],
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng Spesial', 'price' => 20000, 'stock_qty' => 40, 'is_available' => true],
            ['category_id' => $makanan->id, 'name' => 'Mie Goreng Ayam', 'price' => 17000, 'stock_qty' => 30, 'is_available' => true],
            ['category_id' => $makanan->id, 'name' => 'Lele Goreng + Nasi', 'price' => 15000, 'stock_qty' => 25, 'is_available' => true],

            // Minuman
            ['category_id' => $minuman->id, 'name' => 'Es Teh Manis', 'price' => 5000, 'stock_qty' => 100, 'is_available' => true],
            ['category_id' => $minuman->id, 'name' => 'Es Jeruk', 'price' => 8000, 'stock_qty' => 80, 'is_available' => true],
            ['category_id' => $minuman->id, 'name' => 'Kopi Hitam', 'price' => 6000, 'stock_qty' => 60, 'is_available' => true],
            ['category_id' => $minuman->id, 'name' => 'Susu Coklat', 'price' => 10000, 'stock_qty' => 40, 'is_available' => true],
            ['category_id' => $minuman->id, 'name' => 'Air Mineral', 'price' => 4000, 'stock_qty' => 200, 'is_available' => true],

            // Snack & Gorengan
            ['category_id' => $snack->id, 'name' => 'Tempe Goreng (3 pcs)', 'price' => 3000, 'stock_qty' => 80, 'is_available' => true],
            ['category_id' => $snack->id, 'name' => 'Tahu Goreng (3 pcs)', 'price' => 3000, 'stock_qty' => 80, 'is_available' => true],
            ['category_id' => $snack->id, 'name' => 'Bakwan Sayur (2 pcs)', 'price' => 4000, 'stock_qty' => 60, 'is_available' => true],
            ['category_id' => $snack->id, 'name' => 'Kerupuk', 'price' => 2000, 'stock_qty' => 150, 'is_available' => true],

            // Paket & Add-on
            ['category_id' => $paket->id, 'name' => 'Paket Hemat (Ayam + Es Teh)', 'price' => 22000, 'stock_qty' => 30, 'is_available' => true],
            ['category_id' => $paket->id, 'name' => 'Tambahan Nasi', 'price' => 4000, 'stock_qty' => 100, 'is_available' => true],
            ['category_id' => $paket->id, 'name' => 'Tambahan Sambal', 'price' => 2000, 'stock_qty' => 100, 'is_available' => true],
        ];

        foreach ($items as $data) {
            MenuItem::updateOrCreate(
                ['name' => $data['name'], 'category_id' => $data['category_id']],
                $data
            );
        }
    }
}
