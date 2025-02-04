<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SanctumAuthController extends BaseController
{
    /**
     * Register a new user and create a Sanctum token.
     */
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $registerValidatedData = $request->validated();
        $user = User::create($registerValidatedData);
        $data['token'] = $user->createToken(config('app.name'))->plainTextToken;
        $data['name'] = $user->name;

        return $this->sendResponse($data, 'User register successfully.');
    }

    /**
     * Log in an existing user and provide a Sanctum token.
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $loginValidatedData = $request->validated();
        if (! Auth::attempt(['email' => $loginValidatedData['email'], 'password' => $loginValidatedData['password']])) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }

        $user = Auth::user();
        $data['token'] = $user->createToken(config('app.name'))->plainTextToken;
        $data['name'] = $user->name;

        return $this->sendResponse($data, 'User login successfully.');
    }

    /**
     * Log out the authenticated user by deleting their tokens.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse([], 'You are logged out.');
    }

    /**
     * Retrieve the authenticated user's data.
     */
    public function me(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return $this->sendError('Unauthenticated.',
                ['error' => 'Authentication is required to access this resource.'], 401);
        }

        $userData = [
            'user' => $user,
        ];

        return $this->sendResponse($userData, 'Data retrieved successfully.');
    }
}
