<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
           'identity_number' => '1042770586',
           'name' => 'Duban',
            'last_name' => 'Restrepo',
            'number_phone' => '3126143902',
            'address' => 'Cra 19 # 16-28',
            'email' => 'duban52@gmail.com',
            'password' => Hash::make('12345678'),
        ])->assignBranch('EasyNet Gómez Plata');

        //Estos son 10 registros automáticos
        User::factory(10)->create();
        //Debo llamar esto en el DataBaseSeeder
    }
}
