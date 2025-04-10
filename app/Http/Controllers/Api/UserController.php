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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // password_confirmation je druhé heslo na overenie
        ]);

        // Vytvorenie používateľa
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_role' => 1,
        ]);

        $role = $user->role;
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

    // Úprava profilu prihláseného používateľa
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validácia údajov – heslo nemusí byť povinné, ale ak sa zadá, musí byť potvrdené
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Aktualizácia údajov
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil bol úspešne aktualizovaný.',
            'user' => $user
        ]);
    }
    
    public function index()
    {
        return response()->json(User::all());
    }



}
