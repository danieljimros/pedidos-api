<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    // Método para respuestas exitosas
    public static function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    // Método para respuestas de error
    public static function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}