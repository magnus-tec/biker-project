<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('warehouses')->insert([
            ['name' => 'Almacén Central'],
            ['name' => 'Almacén Sucursal'],
            ['name' => 'Tienda Junin'],
            ['name' => 'Tienda Mazuko'],
        ]);
    }
}
