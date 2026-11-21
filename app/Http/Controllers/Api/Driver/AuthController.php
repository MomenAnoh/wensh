<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Wensh;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone_code' => ['required', 'string', 'max:5'],
            'phone' => ['required', 'string', 'max:20', 'unique:wenshes,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        unset($data['password_confirmation']);

        $wensh = Wensh::query()->create($data);
        $token = auth('wensh')->login($wensh);

        return $this->tokenResponse($wensh, $token, __('api.driver.registered'), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $wensh = Wensh::query()->where('phone', $credentials['phone'])->first();

        if ($wensh === null || $wensh->password === null || ! Hash::check($credentials['password'], $wensh->password)) {
            throw ValidationException::withMessages([
                'phone' => [__('api.driver.invalid_credentials')],
            ]);
        }

        $token = auth('wensh')->login($wensh);

        return $this->tokenResponse($wensh, $token, __('api.driver.logged_in'));
    }

    private function tokenResponse(Wensh $wensh, string $token, string $message, int $status = 200): JsonResponse
    {
        return ApiResponse::success($message, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null,
            'wensh' => [
                'id' => $wensh->id,
                'full_name' => $wensh->full_name,
                'phone_code' => $wensh->phone_code,
                'phone' => $wensh->phone,
            ],
        ], $status);
    }
}
