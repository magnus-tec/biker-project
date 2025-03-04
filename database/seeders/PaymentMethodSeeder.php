<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->insert([
            ['name' => 'Efectivo', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tarjeta de Crédito', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tarjeta de Débito', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transferencia Bancaria', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yape', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Plin', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paypal', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
