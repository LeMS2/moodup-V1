<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriggerSeeder extends Seeder
{
    public function run()
    {
        DB::table('triggers')->insert([

            ['name' => 'trabalho', 'label' => 'Trabalho'],
            ['name' => 'escola', 'label' => 'Escola/Faculdade'],
            ['name' => 'familia', 'label' => 'Família'],
            ['name' => 'transito', 'label' => 'Trânsito'],
            ['name' => 'amizades', 'label' => 'Amizades'],
            ['name' => 'dinheiro', 'label' => 'Dinheiro'],
            ['name' => 'saude', 'label' => 'Saúde'],
            ['name' => 'sono', 'label' => 'Sono'],


        ]);
    }
}