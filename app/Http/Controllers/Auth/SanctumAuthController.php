<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SanctumAuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $service
    ) {
        //
    }

    /**
     * Register a new user and create a Sanctum token.
     */
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="accessToken", type="string", example="67b44704b7dabb5ccd037c10|JaAcoNkoMkW4debCEaVTOtqyS1ycHp7ZWroq15RY97ea1576"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string", example="test"),
     *                     @OA\Property(property="email", type="string", example="test18@example.com"),
     *                     @OA\Property(property="emailVerifiedAt", type="string", format="date-time", example=null),
     *                     @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-18 08:38:28"),
     *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-18 08:38:28"),
     *                     @OA\Property(property="id", type="string", example="67b44704b7dabb5ccd037c0f")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="User register successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=422),
     *                     @OA\Property(property="message", type="string", example="The email has already been taken."),
     *                     @OA\Property(property="source", type="string", example="email")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=429,
     *          description="Too Many Attempts",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="errors", type="array",
     *
     *                  @OA\Items(
     *
     *                      @OA\Property(property="status", type="integer", example=429),
     *                      @OA\Property(property="message", type="string", example="Too Many Attempts."),
     *                      @OA\Property(property="source", type="string", example="")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $registerValidatedData = $request->validated();
        $user = User::create($registerValidatedData);
        Auth::login($user);

        // TODO: fix it
        $user->createdAt = $user->created_at->toDateTime()->format('Y-m-d H:i:s');
        $user->updatedAt = $user->updated_at->toDateTime()->format('Y-m-d H:i:s');

        $tokens = $this->service->generateTokens($user);
        $rtExpireTime = config('sanctum.rt_expiration');
        $cookie = cookie('refreshToken', $tokens['refreshToken'], $rtExpireTime, secure: true);
        $data = [
            'accessToken' => $tokens['accessToken'],
            'user' => $user,
        ];

        return $this->sendResponse($data, 'User register successfully.')->withCookie($cookie);
    }

    /**
     * Log in an existing user and provide a Sanctum token.
     */
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Log in an existing user and provide a Sanctum token",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="accessToken", type="string", example="67b443f25ddf821592094e98|plXajuOvCw8jUbdPcXOXJZiEn4wA7wCefpkLgHsJda024c70"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com"),
     *                     @OA\Property(property="emailVerifiedAt", type="string", format="date-time", example="2025-02-13 12:36:35"),
     *                     @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-13 12:36:35"),
     *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-13 12:36:35"),
     *                     @OA\Property(property="id", type="string", example="67ade753c3f4971059007e97")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="User login successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=422),
     *                     @OA\Property(property="message", type="string", example="The password is incorrect."),
     *                     @OA\Property(property="source", type="string", example="password")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Too Many Attempts",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=429),
     *                     @OA\Property(property="message", type="string", example="Too Many Attempts."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @throws ValidationException
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $loginValidatedData = $request->validated();

        if (! Auth::attempt(['email' => $loginValidatedData['email'], 'password' => $loginValidatedData['password']])) {
            $user = User::where('email', $loginValidatedData['email'])->first();
            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => ['The email is not registered.'],
                ]);
            }
            if (! Hash::check($loginValidatedData['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The password is incorrect.'],
                ]);
            }
        }

        $user = Auth::guard('sanctum')->user();
        $tokens = $this->service->generateTokens($user);

        $rtExpireTime = config('sanctum.rt_expiration');
        $cookie = cookie('refreshToken', $tokens['refreshToken'], $rtExpireTime, secure: true);

        $data = [
            'accessToken' => $tokens['accessToken'],
            'user' => $user,
        ];

        return $this->sendResponse($data, 'User login successfully.')->withCookie($cookie);
    }

    /**
     * Log out the authenticated user by deleting their tokens.
     */
    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Log out the authenticated user by deleting their tokens",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", items={}),
     *             @OA\Property(property="message", type="string", example="You are logged out.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=401),
     *                     @OA\Property(property="message", type="string", example="Unauthenticated."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=429,
     *          description="Too Many Attempts",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="errors", type="array",
     *
     *                  @OA\Items(
     *
     *                      @OA\Property(property="status", type="integer", example=429),
     *                      @OA\Property(property="message", type="string", example="Too Many Attempts."),
     *                      @OA\Property(property="source", type="string", example="")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('sanctum')->user()?->tokens()->delete();

        $cookie = cookie()->forget('refreshToken');

        return $this->sendResponse([], 'You are logged out.')->withCookie($cookie);
    }

    /**
     * Retrieve the authenticated user's data.
     */
    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Retrieve the authenticated user's data",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User data retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="string", example="67a30b97677e1eb7ed0b0c67"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="emailVerifiedAt", type="string", format="date-time", example="2025-02-05 06:56:23"),
     *                     @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-05 06:56:23"),
     *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-05 06:56:23")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function me(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $userData = [
            'user' => $user,
        ];

        return $this->sendResponse($userData, 'Data retrieved successfully.');
    }

    /**
     * Refresh access token.
     */
    /**
     * @OA\Post(
     *     path="/api/auth/refresh-token",
     *     summary="Refresh access token",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={},
     *             type="object",
     *             example={}
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Access token refreshed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="accessToken", type="string", example="67b44b44fdfe254315079401|PRStJVgVkcLnhgXx92WG9em89DR0xqJoUh9hFjAc0ff5535a")
     *             ),
     *             @OA\Property(property="message", type="string", example="Refresh access token successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=401),
     *                     @OA\Property(property="message", type="string", example="Unauthenticated."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $request->user()->tokens()->delete();
        $tokens = $this->service->generateTokens($user);

        $rtExpireTime = config('sanctum.rt_expiration');
        $cookie = cookie('refreshToken', $tokens['refreshToken'], $rtExpireTime, secure: true);
        $data = [
            'accessToken' => $tokens['accessToken'],
        ];

        return $this->sendResponse($data, 'Refresh access token successfully.')->withCookie($cookie);
    }
}
