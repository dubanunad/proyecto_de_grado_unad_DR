<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Service::create([
            'name' => 'Internet 30mbps',
            'base_price' => '50000',
            'tax_percentage' => '0',
            'user_id' => 1,
            'branch_id' => 1,
        ]);
        Service::create([
            'name' => 'Internet 50mbps',
            'base_price' => '70000',
            'tax_percentage' => '0',
            'user_id' => 1,
            'branch_id' => 1,
        ]);
        Service::create([
            'name' => 'Internet 80mbps',
            'base_price' => '90000',
            'tax_percentage' => '0',
            'user_id' => 1,
            'branch_id' => 1,
        ]);
        Service::create([
            'name' => 'Servicio de TV',
            'base_price' => '27000',
            'tax_percentage' => '19',
            'user_id' => 1,
            'branch_id' => 1,
        ]);
    }
}
