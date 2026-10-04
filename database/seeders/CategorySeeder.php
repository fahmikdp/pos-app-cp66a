<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'makanan', 'description' => 'Makanan', 'is_active' => true],
            ['name' => 'minuman', 'description' => 'Minuman', 'is_active' => true],
            ['name' => 'cemilan', 'description' => 'Cemilan', 'is_active' => true],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
