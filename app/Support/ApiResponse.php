<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message, int $status = 400, mixed $data = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function unauthorized(string $message, mixed $data = null): JsonResponse
    {
        return self::error($message, 401, $data);
    }

    public static function forbidden(string $message, mixed $data = null): JsonResponse
    {
        return self::error($message, 403, $data);
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public static function validation(array $errors): JsonResponse
    {
        $message = collect($errors)->flatten()->first() ?? __('api.validation_failed');

        return self::error($message, 422);
    }
}
