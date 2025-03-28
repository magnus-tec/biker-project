<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'razon_social' => 'Tech Solutions S.A.C.',
                'ruc' => '20123456789',
                'direccion' => 'Av. Principal 123, Lima',
                'logo_path' => 'logos/tech.png',
                'sol_user' => 'admin_tech',
                'sol_pass' => Hash::make('password123'),
                'cert_path' => 'certs/tech_cert.pem',
                'client_id' => 'client_id_tech',
                'client_secret' => 'client_secret_tech',
                'ubigeo' => '150101',
                'distrito' => 'Lima',
                'provincia' => 'Lima',
                'departamento' => 'Lima',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'razon_social' => 'Innova Business E.I.R.L.',
                'ruc' => '20100686814',
                'direccion' => 'Calle Comercio 456, Arequipa',
                'logo_path' => 'logos/innova.png',
                'sol_user' => 'admin_innova',
                'sol_pass' => Hash::make('securepass'),
                'cert_path' => 'certs/innova_cert.pem',
                'client_id' => 'client_id_innova',
                'client_secret' => 'client_secret_innova',
                'ubigeo' => '040101',
                'distrito' => 'Arequipa',
                'provincia' => 'Arequipa',
                'departamento' => 'Arequipa',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
