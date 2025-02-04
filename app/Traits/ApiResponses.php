<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function ok(string $message, array $data = []): JsonResponse
    {
        return $this->success($message, $data, 200);
    }

    protected function success(string $message, array $data = [], ?int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function error(string|array $errors = [], ?int $statusCode = null): JsonResponse
    {
        if (is_string($errors)) {
            return response()->json([
                'message' => $errors,
                'status' => $statusCode,
            ], $statusCode);
        }

        return response()->json([
            'errors' => $errors,
        ]);
    }

    protected function notAuthorized(string $message): JsonResponse
    {
        return $this->error([
            'status' => 401,
            'message' => $message,
            'source' => '',
        ]);
    }
}
