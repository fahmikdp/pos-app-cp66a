<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $menus = [
            // Makanan
            ['nama' => 'Soto Daging Sapi', 'kategori' => 'makanan', 'harga' => 11000],
            ['nama' => 'Soto Babat', 'kategori' => 'makanan', 'harga' => 10000],
            ['nama' => 'Soto Ayam Biasa', 'kategori' => 'makanan', 'harga' => 9000],
            ['nama' => 'Soto Kulit', 'kategori' => 'makanan', 'harga' => 13000],
            ['nama' => 'Soto Balungan', 'kategori' => 'makanan', 'harga' => 10000],
            ['nama' => 'Soto Ceker', 'kategori' => 'makanan', 'harga' => 13000],
            ['nama' => 'Soto Sayap', 'kategori' => 'makanan', 'harga' => 13000],
            ['nama' => 'Soto Paha', 'kategori' => 'makanan', 'harga' => 15000],

            // Minuman
            ['nama' => 'Teh (Es / Panas)', 'kategori' => 'minuman', 'harga' => 3000],
            ['nama' => 'Lemon Tea (Es / Panas)', 'kategori' => 'minuman', 'harga' => 4000],
            ['nama' => 'Jeruk (Es / Panas)', 'kategori' => 'minuman', 'harga' => 4000],
            ['nama' => 'Susu (Putih / Coklat)', 'kategori' => 'minuman', 'harga' => 4000],
            ['nama' => 'White Coffee / Good Day', 'kategori' => 'minuman', 'harga' => 4000],
            ['nama' => 'Kopi Hitam / Nescafe', 'kategori' => 'minuman', 'harga' => 4000],
            ['nama' => 'Milo', 'kategori' => 'minuman', 'harga' => 5000],
            ['nama' => 'Teh Tarik / Jahe Sereh', 'kategori' => 'minuman', 'harga' => 5000],
            ['nama' => 'Air Mineral', 'kategori' => 'minuman', 'harga' => 3500],
            ['nama' => 'Wedang Uwuh', 'kategori' => 'minuman', 'harga' => 5000],

            // Cemilan
            ['nama' => 'Tempe Tepung / Garet', 'kategori' => 'cemilan', 'harga' => 1000],
            ['nama' => 'Tahu Mercon / Isi', 'kategori' => 'cemilan', 'harga' => 1000],
            ['nama' => 'Bakwan Sayur / Potel', 'kategori' => 'cemilan', 'harga' => 1000],
            ['nama' => 'Perkedel / Tahu Bakso', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Ati / Telur Puyuh', 'kategori' => 'cemilan', 'harga' => 3000],
            ['nama' => 'Sate Ayam / Kulit', 'kategori' => 'cemilan', 'harga' => 3000],
            ['nama' => 'Usus / Tahu Bacem', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Jamur / Kulit Krispi', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Keong / Ayam Krispi', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Bakso Pedas', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Jamur Bacem', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Bakso Krispi', 'kategori' => 'cemilan', 'harga' => 2500],
            ['nama' => 'Sempol Ayam', 'kategori' => 'cemilan', 'harga' => 2500],
            ['nama' => 'Sosis Mie', 'kategori' => 'cemilan', 'harga' => 3000],
            ['nama' => 'Onde Onde', 'kategori' => 'cemilan', 'harga' => 2500],
            ['nama' => 'Keripik Usus', 'kategori' => 'cemilan', 'harga' => 2500],
            ['nama' => 'Rambak Kulit', 'kategori' => 'cemilan', 'harga' => 2500],
            ['nama' => 'Kacang Telur', 'kategori' => 'cemilan', 'harga' => 2000],
            ['nama' => 'Kerupuk Terung Bulat', 'kategori' => 'cemilan', 'harga' => 1000],
            ['nama' => 'Telur Asin', 'kategori' => 'cemilan', 'harga' => 4000],
        ];

        foreach ($menus as $item) {
            $categoryId = $categories[$item['kategori']] ?? null;

            if ($categoryId) {
                MenuItem::updateOrCreate(
                    ['name' => $item['nama'], 'category_id' => $categoryId],
                    [
                        'price' => $item['harga'],
                        'stock_qty' => 100,
                        'is_available' => true,
                    ]
                );
            }
        }
    }
}
