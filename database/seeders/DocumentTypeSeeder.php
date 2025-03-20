<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposDocumentos = [
            ['name' => 'BOLETA DE VENTA', 'sunat_code' => '03', 'abbreviation' => 'BT'],
            ['name' => 'FACTURA', 'sunat_code' => '01', 'abbreviation' => 'FT'],
            ['name' => 'NOTA DE CRÉDITO', 'sunat_code' => '07', 'abbreviation' => 'NC'],
            ['name' => 'NOTA DE DÉBITO', 'sunat_code' => '08', 'abbreviation' => 'ND'],
            ['name' => 'NOTA DE RECEPCIÓN', 'sunat_code' => '09', 'abbreviation' => 'GR'],
            ['name' => 'NOTA DE VENTA', 'sunat_code' => '00', 'abbreviation' => 'NV'],
            ['name' => 'NOTA DE SEPARACIÓN', 'sunat_code' => '00', 'abbreviation' => 'NS'],
            ['name' => 'NOTA DE TRASLADO', 'sunat_code' => '00', 'abbreviation' => 'NT'],
            ['name' => 'NOTA DE INVENTARIO', 'sunat_code' => '00', 'abbreviation' => 'NIV'],
            ['name' => 'NOTA DE INGRESO', 'sunat_code' => '00', 'abbreviation' => 'NIG'],
            ['name' => 'GUÍA DE REMISIÓN', 'sunat_code' => '09', 'abbreviation' => 'GR'],
            // ['name' => 'NOTA DE COMPRA', 'sunat_code' => '00', 'abbreviation' => null],
        ];

        DB::table('document_types')->insert($tiposDocumentos);
    }
}
