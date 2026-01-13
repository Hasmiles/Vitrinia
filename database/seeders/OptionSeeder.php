<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Option;


class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = array(
            [
                'title' => 'Renk',
                'active' => 1
            ],
            [
                'title' => 'Beden (Giyim)',
                'active' => 1
            ],
            [
                'title' => 'Ayakkabı Numarası',
                'active' => 1
            ],
            [
                'title' => 'Bebek/Çocuk',
                'active' => 1
            ],
        );

        foreach($options as $option){
            Option::UpdateOrCreate(
                 ['title' => $option['title']], 
                ['active' => $option['active']]
            );
        }
    }
}
