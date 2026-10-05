<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with('role')->where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password) || $user->status !== 'ACTIVE') {
            throw ValidationException::withMessages(['email' => ['The provided credentials are invalid.']]);
        }

        $token = $user->createToken('api-client')->plainTextToken;

        return response()->json(['success' => true, 'message' => 'Login successful.', 'data' => [
            'user' => new UserResource($user), 'token' => $token,
        ]]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['success' => true, 'message' => 'Logout successful.', 'data' => null]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Authenticated user retrieved successfully.', 'data' => new UserResource($request->user()->load('role'))]);
    }
}