<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        return ApiResponse::success('Listado de pedidos', Pedido::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $pedido = Pedido::create($data);

        return ApiResponse::success('Pedido creado exitosamente', $pedido, 201);
    }

    public function show(Pedido $pedido)
    {
        return ApiResponse::success('Detalle del pedido', $pedido);
    }

    public function update(Request $request, Pedido $pedido)
    {
        $data = $request->validate($this->updateRules());
        $pedido->update($data);

        return ApiResponse::success('Pedido actualizado exitosamente', $pedido);
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return ApiResponse::success('Pedido eliminado exitosamente');
    }

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
