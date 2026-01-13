<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = array(
            [
                'title' => 'Taslak',
                'active' => 1
            ],
            [
                'title' => 'Ödeme Bekliyor',
                'active' => 1
            ],
            [
                'title' => 'Hazırlanıyor',
                'active' => 1
            ],
            [
                'title' => 'Kargolandı',
                'active' => 1
            ],
            [
                'title' => 'Tamamlandı',
                'active' => 1
            ],
            [
                'title' => 'İptal Edilen',
                'active' => 1
            ],
        );

       foreach ($statuses as $status) {
           
            Status::updateOrCreate(
                ['title' => $status['title']], 
                ['active' => $status['active']]
            );
        }

    }
}
