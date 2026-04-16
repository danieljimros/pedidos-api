<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_lista_pedidos_devuelve_200_y_datos_paginados(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Creamos algunos pedidos de prueba
        \App\Models\Pedido::factory()->count(3)->create();

        $response = $this->getJson('/api/pedidos');

        $response->assertOk()
            ->assertJsonPath('message', 'Listado de pedidos')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data',
                    'per_page',
                    'total',
                ]
            ]);
    }

    /** @test */
    public function test_crea_pedido_con_datos_validos(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Datos para crear un nuevo pedido
        $data = [
            'cliente' => 'Juan Pérez',
            'producto' => 'Laptop',
            'cantidad' => 2,
            'precio' => 1500.50,
            'estado' => 'pendiente',
        ];

        // Enviamos la solicitud POST para crear el pedido
        $response = $this->postJson('/api/pedidos', $data);

        $response->assertCreated()  // Verifica status 201
            ->assertJsonPath('message', 'Pedido creado exitosamente')
            ->assertJsonPath('data.cliente', 'Juan Pérez')
            ->assertJsonPath('data.producto', 'Laptop');
    }

    /** @test */
    public function test_crea_pedido_sin_datos_requeridos(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Intentamos crear sin campos requeridos
        $data = [
            'cliente' => 'Juan',
            // falta producto, cantidad, precio
        ];

        // Enviamos la solicitud POST para crear el pedido
        $response = $this->postJson('/api/pedidos', $data);

        $response->assertUnprocessable()  // Verifica status 422
            ->assertJsonValidationErrors(['producto', 'cantidad', 'precio']);
    }

    /** @test */
    public function test_obtiene_pedido_por_id(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Creamos un pedido
        $pedido = \App\Models\Pedido::factory()->create([
            'cliente' => 'Ana García',
            'producto' => 'Monitor',
        ]);

        // Enviamos la solicitud GET para obtener el pedido por ID
        $response = $this->getJson("/api/pedidos/{$pedido->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Detalle del pedido')
            ->assertJsonPath('data.cliente', 'Ana García')
            ->assertJsonPath('data.id', $pedido->id);
    }

    /** @test */
    public function test_obtiene_404_si_pedido_no_existe(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Intentamos obtener un pedido con ID que no existe
        $response = $this->getJson('/api/pedidos/999');

        $response->assertNotFound();  // Verifica status 404
    }

    /** @test */
    public function test_actualiza_pedido_con_datos_validos(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Creamos un pedido
        $pedido = \App\Models\Pedido::factory()->create([
            'estado' => 'pendiente',
        ]);

        // Datos para actualizar el pedido
        $dataActualizada = [
            'cliente' => 'Pedro López',
            'estado' => 'en_proceso',
        ];

        // Enviamos la solicitud PUT para actualizar el pedido
        $response = $this->putJson("/api/pedidos/{$pedido->id}", $dataActualizada);

        $response->assertOk()
            ->assertJsonPath('message', 'Pedido actualizado exitosamente')
            ->assertJsonPath('data.estado', 'en_proceso');

        // Verificamos en BD que se guardó el cambio
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'en_proceso',
        ]);
    }

    /** @test */
    public function test_elimina_pedido(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Creamos un pedido
        $pedido = \App\Models\Pedido::factory()->create();
        $pedidoId = $pedido->id;

        // Enviamos la solicitud DELETE para eliminar el pedido
        $response = $this->deleteJson("/api/pedidos/{$pedidoId}");

        $response->assertOk()
            ->assertJsonPath('message', 'Pedido eliminado exitosamente');

        // Verificamos que ya no existe en BD
        $this->assertDatabaseMissing('pedidos', [
            'id' => $pedidoId,
        ]);
    }

    /** @test */
    public function test_elimina_retorna_404_si_no_existe(): void
    {
        // Simulamos un usuario autenticado
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        // Intentamos eliminar un pedido con ID que no existe
        $response = $this->deleteJson('/api/pedidos/999');

        $response->assertNotFound();
    }
}
