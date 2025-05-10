<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Discount;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register new user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_role' => 1,
        ]);

        $token = $user->createToken('API Token')->plainTextToken;
        $privilege = $user->role->privilege;

        return response()->json([
            'firstname' => $user->first_name,
            'lastname' => $user->last_name,
            'email' => $user->email,
            'card_id' => $user->card_id,
            'discount' => $user->discount ? ['name' => $user->discount->name] : null,
            'message' => 'User registered successfully',
            'token' => $token,
            'privilege' => $privilege,
        ], 201);
    }

    public function storeExpoToken(Request $request)
    {
        // Validácia tokenu
        $request->validate([
            'expo_token' => 'required|string',
        ]);

        // Získanie používateľa podľa tokenu (alebo iného spôsobu autentifikácie)
        $user = auth()->user(); // Toto predpokladá, že používateľ je autentifikovaný

        // Uloženie expo tokenu do databázy
        $user->expo_token = $request->expo_token;
        $user->save();

        return response()->json([
            'message' => 'Expo token uložený úspešne.',
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            $privilege = $user->role->privilege;

            return response()->json([
                'firstname' => $user->first_name,
                'lastname' => $user->last_name,
                'email' => $user->email,
                'message' => 'Login successful',
                'token' => $token,
                'privilege' => $privilege,
                'card_id' => $user->card_id,
                'discount' => $user->discount ? $user->discount->name : null,
                'discount_coeficient' => $user->discount ? $user->discount->coeficient : null,
                'discount_card_code' => $user->discount ? $user->discount->card_code : null,
            ]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get authenticated user",
     *     tags={"User"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Authenticated user info"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getUser(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/updateProfile",
     *     summary="Update profile of authenticated user",
     *     tags={"User"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
    
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'card_id' => 'sometimes|nullable|string|max:100',
        ]);
    
        // Ak je zadaný card_id, overíme, či existuje zľava s týmto card_code
        if (array_key_exists('card_id', $validated)) {
            // Vezmeme len prvé 4 znaky pre porovnanie
            $cardPrefix = substr($validated['card_id'], 0, 4);
        
            // Hľadáme zľavu podľa prvých 4 znakov kódu
            $discount = Discount::where('card_code', $cardPrefix)->first();
            
            if (!$discount && $validated['card_id'] !== null) {
                return response()->json([
                    'message' => 'Zľavová karta s týmto ID neexistuje.',
                ], 422); // 422 Unprocessable Entity
            }
            
            // Ak existuje zľava, priradíme ju k používateľovi
            $user->card_id = $validated['card_id'];
            $user->discount_id = $discount ? $discount->id : null;
        }
        
    
        // Ak je zadané heslo, hashujeme
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
    
        // Aktualizuj ďalšie polia
        $user->first_name = $validated['first_name'] ?? $user->first_name;
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->email = $validated['email'] ?? $user->email;
    
        $user->save();
    
        // Načítaj discount pre frontend
        $user->load('discount');
    
        return response()->json([
            'message' => 'Profil bol úspešne aktualizovaný.',
            'user' => $user
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of all users",
     *     tags={"User"},
     *     @OA\Response(response=200, description="List of users")
     * )
     */
    public function index()
    {
        return response()->json(User::all());
    }
}
