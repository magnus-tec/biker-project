<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            ['name' => 'Kilogramos', 'unit_type_id' => 1], // Peso
            ['name' => 'Litros', 'unit_type_id' => 2], // Volumen
            ['name' => 'Unidades', 'unit_type_id' => 3], // Cantidad
        ]);
    }
}
