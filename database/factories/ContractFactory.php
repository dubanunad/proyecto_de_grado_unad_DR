<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => $this->faker->numberBetween(1, 3),
            'client_id' => $this->faker->unique()->numberBetween(1, 20), // Número único del 1 al 20, // Crea o asocia con un cliente
            'plan_id' => $this->faker->numberBetween(1, 6), // Número único del 1 al 6,
            'neighborhood' => $this->faker->word(),
            'address' => $this->faker->address(),
            'home_type' => $this->faker->randomElement(['Propia', 'En Arriendo', 'Otro']),
            'nap_port' => $this->faker->numerify('NAP-PORT-###'),
            'cpe_sn' => Str::random(20), // Genera un número de serie único
            'user_pppoe' => $this->faker->unique()->userName(),
            'password_pppoe' => $this->faker->password(8, 16),
            'status' => $this->faker->randomElement(['Por Instalar','Activo','Cortado','Retirado','Por Reconectar']),
            'social_stratum' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6']),
            'permanence_clause' => $this->faker->numberBetween(12, 36), // Ej. cláusula de 12 a 36 meses
            'ssid_wifi' => 'WiFi-' . $this->faker->word(),
            'password_wifi' => $this->faker->password(8, 16),
            'comment' => $this->faker->sentence(),
            'user_id' => 1, // Relación con un usuario
        ];
    }
}
