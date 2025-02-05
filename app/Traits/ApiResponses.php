<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponses
{
    protected function ok(string $message, array $data = []): JsonResponse
    {
        return $this->success($message, $data, Response::HTTP_OK);
    }

    protected function success(string $message, array $data = [], ?int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function error(string|array $errors, int $statusCode): JsonResponse
    {
        if (is_string($errors)) {
            return response()->json([
                'message' => $errors,
                'status' => $statusCode,
            ], $statusCode);
        }

        return response()->json([
            'errors' => $errors,
        ], $statusCode);
    }

    protected function notAuthorized(string $message): JsonResponse
    {
        return $this->error([
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => $message,
            'source' => '',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
