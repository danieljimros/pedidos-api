<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Pedido;
use Illuminate\Http\Request;

// Controlador para manejar las operaciones CRUD de Pedidos
class PedidoController extends Controller
{
    // Listar todos los pedidos con paginación
    public function index(Request $request)
    {
        // Validar el parámetro de paginación opcional
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        // Obtener el número de elementos por página, con un valor predeterminado de 10
        $perPage = (int) ($validated['per_page'] ?? 10);

        // Obtener los pedidos ordenados por el más reciente y paginados
        $pedidos = Pedido::latest('id')
            ->paginate($perPage)
            ->appends($request->query());

        return ApiResponse::success('Listado de pedidos', $pedidos);
    }

    // Crear un nuevo pedido
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $pedido = Pedido::create($data);

        return ApiResponse::success('Pedido creado exitosamente', $pedido, 201);
    }

    // Mostrar los detalles de un pedido específico
    public function show(Pedido $pedido)
    {
        return ApiResponse::success('Detalle del pedido', $pedido);
    }

    // Actualizar un pedido existente
    public function update(Request $request, Pedido $pedido)
    {
        $data = $request->validate($this->updateRules());
        $pedido->update($data);

        return ApiResponse::success('Pedido actualizado exitosamente', $pedido);
    }

    // Eliminar un pedido
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return ApiResponse::success('Pedido eliminado exitosamente');
    }

    // Reglas de validación para crear un pedido
    protected function rules(): array
    {
        return [
            'cliente' => 'required|string|max:255',
            'producto' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'estado' => 'sometimes|in:pendiente,en_proceso,completado',
        ];
    }

    // Reglas de validación para actualizar un pedido (permitiendo campos opcionales)
    protected function updateRules(): array
    {
        return [
            'cliente' => 'sometimes|required|string|max:255',
            'producto' => 'sometimes|required|string|max:255',
            'cantidad' => 'sometimes|required|integer|min:1',
            'precio' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|in:pendiente,en_proceso,completado',
        ];
    }
}
