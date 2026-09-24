<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Utama', 'description' => 'Menu makanan berat seperti nasi dan lauk', 'is_active' => true],
            ['name' => 'Minuman', 'description' => 'Minuman dingin dan panas', 'is_active' => true],
            ['name' => 'Snack & Gorengan', 'description' => 'Camilan dan gorengan', 'is_active' => true],
            ['name' => 'Paket & Add-on', 'description' => 'Paket hemat dan tambahan', 'is_active' => true],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
