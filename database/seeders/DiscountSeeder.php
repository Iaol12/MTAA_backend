<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('discounts')->insert([
            [
                'name' => 'Študentská zľava',
                'coeficient' => 70,
                'card_code' => 'ISIC'
            ],
            [
                'name' => 'Seniorská zľava',
                'coeficient' => 80,
                'card_code' => 'SENI'
            ],
            [
                'name' => 'Zamestnanecká zľava',
                'coeficient' => 60,
                'card_code' => 'ZSSK'
            ]
        ]);
    }
}