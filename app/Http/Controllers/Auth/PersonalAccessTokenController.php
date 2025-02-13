<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Ability\AbilityEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PersonalAccessTokenController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function store(Request $request): array
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return ['token' => $user->createToken(
            $request->device_name,
            [AbilityEnum::MANAGE_ENTITIES_FULL_ACCESS->value]
        )->plainTextToken];
    }

    public function destroy(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
