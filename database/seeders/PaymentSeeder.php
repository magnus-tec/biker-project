<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payments')->insert([
            ['name' => 'contado', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'credito', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
