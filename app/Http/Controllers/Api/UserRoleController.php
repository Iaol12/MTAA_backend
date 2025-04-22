<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-roles",
     *     summary="Get all user roles",
     *     tags={"User Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all user roles",
     *         
     *     )
     * )
     */
    public function index()
    {
        return response()->json(UserRole::with('users')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/user-roles",
     *     summary="Create a new user role",
     *     tags={"User Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role_name", "privilege"},
     *             @OA\Property(property="role_name", type="string", example="Admin"),
     *             @OA\Property(property="privilege", type="string", example="Full Access")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User role created",
     *         
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'privilege' => 'required|string|max:255',
        ]);

        $role = UserRole::create($validated);

        return response()->json($role, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user-roles/{id}",
     *     summary="Get a specific user role",
     *     tags={"User Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the user role"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role details",
     *         
     *     )
     * )
     */
    public function show($id)
    {
        $role = UserRole::with('users')->findOrFail($id);
        return response()->json($role);
    }

    /**
     * @OA\Put(
     *     path="/api/user-roles/{id}",
     *     summary="Update an existing user role",
     *     tags={"User Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the user role"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="role_name", type="string", example="user"),
     *             @OA\Property(property="privilege", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role updated",
     *         
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $role = UserRole::findOrFail($id);

        $validated = $request->validate([
            'role_name' => 'sometimes|string|max:255',
            'privilege' => 'sometimes|string|max:255',
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-roles/{id}",
     *     summary="Delete a user role",
     *     tags={"User Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the user role"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rola bola vymazaná.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $role = UserRole::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Rola bola vymazaná.']);
    }
}
