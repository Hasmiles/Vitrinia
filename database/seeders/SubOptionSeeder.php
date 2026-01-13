<?php

namespace Database\Seeders;

use App\Models\SubOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sub_options = [
            // --- RENKLER  ---
            ['main_id' => 1, 'title' => 'Siyah', 'value' => 'Siyah', 'color_hex' => '#000000', 'active' => 1],
            ['main_id' => 1, 'title' => 'Beyaz', 'value' => 'Beyaz', 'color_hex' => '#FFFFFF', 'active' => 1],
            ['main_id' => 1, 'title' => 'Gri', 'value' => 'Gri', 'color_hex' => '#808080', 'active' => 1],
            ['main_id' => 1, 'title' => 'Kırmızı', 'value' => 'Kırmızı', 'color_hex' => '#FF0000', 'active' => 1],
            ['main_id' => 1, 'title' => 'Mavi', 'value' => 'Mavi', 'color_hex' => '#0000FF', 'active' => 1],
            ['main_id' => 1, 'title' => 'Lacivert', 'value' => 'Lacivert', 'color_hex' => '#000080', 'active' => 1],
            ['main_id' => 1, 'title' => 'Yeşil', 'value' => 'Yeşil', 'color_hex' => '#008000', 'active' => 1],
            ['main_id' => 1, 'title' => 'Haki', 'value' => 'Haki', 'color_hex' => '#4B5320', 'active' => 1],
            ['main_id' => 1, 'title' => 'Sarı', 'value' => 'Sarı', 'color_hex' => '#FFFF00', 'active' => 1],
            ['main_id' => 1, 'title' => 'Turuncu', 'value' => 'Turuncu', 'color_hex' => '#FFA500', 'active' => 1],
            ['main_id' => 1, 'title' => 'Pembe', 'value' => 'Pembe', 'color_hex' => '#FFC0CB', 'active' => 1],
            ['main_id' => 1, 'title' => 'Mor', 'value' => 'Mor', 'color_hex' => '#800080', 'active' => 1],
            ['main_id' => 1, 'title' => 'Bordo', 'value' => 'Bordo', 'color_hex' => '#800000', 'active' => 1],
            ['main_id' => 1, 'title' => 'Bej', 'value' => 'Bej', 'color_hex' => '#F5F5DC', 'active' => 1],
            ['main_id' => 1, 'title' => 'Kahverengi', 'value' => 'Kahverengi', 'color_hex' => '#A52A2A', 'active' => 1],
            ['main_id' => 1, 'title' => 'Altın', 'value' => 'Altın', 'color_hex' => '#FFD700', 'active' => 1],
            ['main_id' => 1, 'title' => 'Gümüş', 'value' => 'Gümüş', 'color_hex' => '#C0C0C0', 'active' => 1],
            ['main_id' => 1, 'title' => 'Çok Renkli', 'value' => 'Çok Renkli', 'color_hex' => 'transparent', 'active' => 1],
            // --- BEDEN GİYİM  ---
            ['main_id' => 2, 'title' => 'XXS', 'value' => 'XXS', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'XS', 'value' => 'XS', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'S', 'value' => 'S', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'M', 'value' => 'M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'L', 'value' => 'L', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'XL', 'value' => 'XL', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'XXL', 'value' => 'XXL', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => '3XL', 'value' => '3XL', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => '4XL', 'value' => '4XL', 'color_hex' => null, 'active' => 1],
            ['main_id' => 2, 'title' => 'Standart', 'value' => 'STD', 'color_hex' => null, 'active' => 1],
            // --- AYAKKABI NUMARASI  ---
            ['main_id' => 3, 'title' => '35', 'value' => '35', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '36', 'value' => '36', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '37', 'value' => '37', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '38', 'value' => '38', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '39', 'value' => '39', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '40', 'value' => '40', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '41', 'value' => '41', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '42', 'value' => '42', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '43', 'value' => '43', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '44', 'value' => '44', 'color_hex' => null, 'active' => 1],
            ['main_id' => 3, 'title' => '45', 'value' => '45', 'color_hex' => null, 'active' => 1],
            // --- BEBEK/ÇOCUK  ---
            ['main_id' => 4, 'title' => 'Yenidoğan', 'value' => 'Newborn', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '0-3 Ay', 'value' => '0-3M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '3-6 Ay', 'value' => '3-6M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '6-9 Ay', 'value' => '6-9M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '9-12 Ay', 'value' => '9-12M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '12-18 Ay', 'value' => '12-18M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '18-24 Ay', 'value' => '18-24M', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '2 Yaş', 'value' => '2Y', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '3 Yaş', 'value' => '3Y', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '4 Yaş', 'value' => '4Y', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '5-6 Yaş', 'value' => '5-6Y', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '7-8 Yaş', 'value' => '7-8Y', 'color_hex' => null, 'active' => 1],
            ['main_id' => 4, 'title' => '9-10 Yaş', 'value' => '9-10Y', 'color_hex' => null, 'active' => 1],
        ];
        foreach ($sub_options as $sub) {
            SubOption::updateOrCreate(
                [
                    'main_id' => $sub['main_id'],
                    'title' => $sub['title']
                ],
                [
                    'value' => $sub['value'],
                    'color_hex' => $sub['color_hex'],
                    'active' => $sub['active']
                ]
            );
        }
    }
}
