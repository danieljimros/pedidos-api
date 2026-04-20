<?php
// database/factories/PedidoFactory.php
namespace Database\Factories;

// Importamos el modelo Pedido para asociarlo con esta fábrica
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generar datos de prueba para el modelo Pedido
        return [
            'cliente' => fake()->name(),
            'producto' => fake()->word(),
            'cantidad' => fake()->numberBetween(1, 100),
            'precio' => fake()->randomFloat(2, 1, 500), // Precio entre 1 y 500 con 2 decimales
            'estado' => fake()->randomElement(['pendiente', 'en_proceso', 'completado']),
        ];
    }
}
