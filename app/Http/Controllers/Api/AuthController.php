<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^(\+63|0)[\d]{10}$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'
            ],
        ], [
            'email.required' => 'The email address may not be empty.',
            'email.email' => 'The email address format is invalid.',
            'phone.regex' => 'Phone number must be in format +63XXXXXXXXXX or 09XXXXXXXXX',
            'password.regex' => 'Password must contain at least one uppercase, lowercase, number, and special character.',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_verified' => false
        ]);
        // $user->assignRole('client');

        event(new Registered($user));

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->getRoleNames(),
                "is_verified" => $user->is_verified,
            ],
        ], 201);
    }
}
