<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Branch::create([
            'nit' => '901446367-0',
            'name' => 'EasyNet Gómez Plata',
            'country' => 'Colombia',
            'department' => 'Antioquia',
            'municipality' => 'Gómez Plata',
            'address' => 'Parque principal sobre el D1',
            'number_phone' => '3206181020',
            'additional_number' => null, // Opcional, puede omitirse
            'image' => 'https://i.ibb.co/MffN622/logo-easynet-png.png',
            'moving_price' => 25000,     // Opcional, puede omitirse
            'reconnection_price' => 15000, // Opcional, puede omitirse
            'message_custom_invoice' => 'Iva Régimen Común. No somos autoretenedores. No somos grandes contribuyentes.
Resolución Dian 18764068544649 Fecha: 06/04/2024 al 06/04/2026 AUTORIZA Del FEG10001 al FEG20000
Registro TIC ante el Ministerio de las Comunicaciones 96006046 DEL 16/03/2021', // Opcional, puede omitirse
            'observation' => null,      // Opcional, puede omitirse
        ]);

    }
}
