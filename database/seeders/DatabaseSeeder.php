<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Contract;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Seeder de usuarios
        $this->call(BranchSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);

        //Factories
        Branch::factory(2)->create();
        Client::factory(20)->create();
        $this->call(ServiceSeeder::class);
        $this->call(PlanSeeder::class);
        Contract::factory(8)->create();




    }
}
