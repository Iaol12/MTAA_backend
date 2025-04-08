<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Registrácia nového používateľa
    public function register(Request $request)
    {
        // Validácia vstupných údajov
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // password_confirmation je druhé heslo na overenie
        ]);

        // Vytvorenie používateľa
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_role' => 1, // Predvolená rola pre nového používateľa
        ]);

        // Vytvorenie tokenu pre prihlásenie
        $token = $user->createToken('API Token')->plainTextToken;
        $privilege = $user->role->privilege;
        // Vrátenie odpovede s tokenom
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'privilege' => $privilege,
        ], 201);
    }

    // Prihlásenie používateľa
    public function login(Request $request)
    {
        // Validácia údajov
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Overenie používateľa
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;


            $privilege = $user->role->privilege;
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'privilege' => $privilege,
            ]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    // Získanie informácií o prihlásenom používateľovi
    public function getUser(Request $request)
    {
        return response()->json([
            'user' => $request->user(), // Vráti prihláseného používateľa
        ]);
    }

    public function index()
    {
        return response()->json(User::all());
    }



}
