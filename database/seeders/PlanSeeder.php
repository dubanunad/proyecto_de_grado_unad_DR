<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Consultar los servicios
        $internet30 = Service::where('name', 'Internet 30mbps')->first();
        $internet50 = Service::where('name', 'Internet 50mbps')->first();
        $internet80 = Service::where('name', 'Internet 80mbps')->first();
        $tv = Service::where('name', 'Servicio de TV')->first();

        // Crear los planes y asignar servicios
        $plan1 = Plan::create(['name' => 'Internet 30mbps', 'user_id' => 1, 'branch_id' => 1]);
        $plan1->services()->attach([$internet30->id]);

        $plan2 = Plan::create(['name' => 'Internet 50mbps', 'user_id' => 1, 'branch_id' => 1]);
        $plan2->services()->attach([$internet50->id]);

        $plan3 = Plan::create(['name' => 'Internet 80mbps', 'user_id' => 1, 'branch_id' => 1]);
        $plan3->services()->attach([$internet80->id]);

        $plan4 = Plan::create(['name' => 'Internet 30mbps + TV', 'user_id' => 1, 'branch_id' => 1]);
        $plan4->services()->attach([$internet30->id, $tv->id]);

        $plan5 = Plan::create(['name' => 'Internet 50mbps + TV', 'user_id' => 1, 'branch_id' => 1]);
        $plan5->services()->attach([$internet50->id, $tv->id]);

        $plan6 = Plan::create(['name' => 'Internet 80mbps + TV', 'user_id' => 1, 'branch_id' => 1]);
        $plan6->services()->attach([$internet80->id, $tv->id]);
    }
}
