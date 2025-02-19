<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('unit_types')->insert([
            ['name' => 'Peso'],
            ['name' => 'Volumen'],
            ['name' => 'Cantidad'],
        ]);
    }
}
